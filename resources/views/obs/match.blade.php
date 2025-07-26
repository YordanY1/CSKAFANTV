<!DOCTYPE html>
<html lang="bg">

<head>
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <meta charset="UTF-8">
    <title>OBS Ultra Compact Scoreboard</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <style>
        :root {
            --red: #ef4444;
            --white: #ffffff;
            --light-red: #f87171;
        }

        body {
            margin: 0;
            background: transparent;
            font-family: 'Arial Black', sans-serif;
            font-size: 20px;
            color: var(--white);
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            min-height: 100vh;
            padding: 16px;
        }

        .scoreboard {
            display: inline-flex;
            align-items: center;
            background: rgba(0, 0, 0, 0.85);
            padding: 4px 8px;
            border-radius: 8px;
            gap: 8px;
        }

        .team {
            display: flex;
            align-items: center;
            gap: 6px;
            font-weight: bold;
            color: var(--red);
            white-space: nowrap;
        }

        .team-logo {
            height: 22px;
            width: auto;
        }

        .score {
            font-size: 26px;
            font-weight: bold;
            color: var(--white);
        }

        .timer-inline {
            font-size: 18px;
            font-weight: bold;
            color: var(--light-red);
            white-space: nowrap;
        }

        .controls {
            margin-top: 14px;
            display: flex;
            gap: 10px;
        }

        .controls button {
            padding: 6px 14px;
            font-size: 14px;
            font-weight: bold;
            color: white;
            background-color: #ef4444;
            border: none;
            border-radius: 6px;
            box-shadow: 0 2px 6px rgba(0, 0, 0, 0.25);
            cursor: pointer;
            transition: background 0.2s ease;
        }

        .controls button:hover {
            background-color: #dc2626;
        }

        .controls button:active {
            background-color: #b91c1c;
        }
    </style>
</head>

@php
    $isOBS = str_contains(request()->header('User-Agent'), 'OBS');
@endphp

<body>
    <div class="scoreboard">
        <div class="team">
            @if ($match->homeTeam?->logo)
                <img src="{{ asset('storage/' . $match->homeTeam->logo) }}" alt="logo" class="team-logo">
            @endif
            <span>{{ $match->homeTeam->name }}</span>
        </div>

        <div class="score" id="score">
            {{ $match->home_score }} : {{ $match->away_score }}
        </div>

        <div class="team">
            <span>{{ $match->awayTeam->name }}</span>
            @if ($match->awayTeam?->logo)
                <img src="{{ asset('storage/' . $match->awayTeam->logo) }}" alt="logo" class="team-logo">
            @endif
        </div>

        <div class="timer-inline" id="timer">00:00</div>
    </div>

    @unless ($isOBS)
        <div class="controls">
            <button onclick="startTimer()">Старт</button>
            <button onclick="pauseTimer()">Пауза</button>
            <button onclick="resumeTimer()">Продължи</button>
        </div>
    @endunless

    <script>
        let timerEl;
        let interval = null;
        let startTimestamp = null;
        let stoppedTimestamp = null;

        const csrf = document.querySelector('meta[name="csrf-token"]').getAttribute('content');

        function updateTimerDisplay() {
            if (!startTimestamp) return;

            const now = Date.now();
            const effectiveNow = stoppedTimestamp ?? now;
            const elapsed = effectiveNow - startTimestamp;

            const minutes = Math.floor(elapsed / 60000);
            const seconds = Math.floor((elapsed % 60000) / 1000);
            timerEl.innerText = String(minutes).padStart(2, '0') + ':' + String(seconds).padStart(2, '0');
        }

        function sendRequest(routeName, callback = null) {
            fetch(routeName, {
                    method: 'POST',
                    headers: {
                        'X-CSRF-TOKEN': csrf,
                        'Content-Type': 'application/json'
                    }
                })
                .then(res => res.ok ? res.json() : Promise.reject(res))
                .then(() => {
                    fetchMatchData(callback);
                })
                .catch(err => {
                    console.error('❌ ГРЕШКА при заявка:', err);
                });
        }

        function startTimer() {
            clearInterval(interval);
            interval = null;
            sendRequest("{{ route('obs.match.start', ['slug' => $match->slug]) }}", () => {
                interval = setInterval(updateTimerDisplay, 1000);
            });
        }

        function pauseTimer() {
            clearInterval(interval);
            interval = null;
            sendRequest("{{ route('obs.match.stop', ['slug' => $match->slug]) }}");
        }

        function resumeTimer() {
            clearInterval(interval);
            interval = null;
            sendRequest("{{ route('obs.match.resume', ['slug' => $match->slug]) }}", () => {
                interval = setInterval(updateTimerDisplay, 1000);
            });
        }

        function fetchMatchData(callback = null) {
            fetch("{{ route('obs.match.json', ['slug' => $match->slug]) }}", {
                    cache: 'no-store'
                })
                .then(res => res.json())
                .then(data => {
                    document.getElementById('score').innerText = `${data.home_score} : ${data.away_score}`;
                    startTimestamp = data.started_at ? data.started_at * 1000 : null;
                    stoppedTimestamp = data.stopped_at ? data.stopped_at * 1000 : null;

                    console.log("Fetched match data:", {
                        home: data.home_score,
                        away: data.away_score,
                        startTimestamp,
                        stoppedTimestamp,
                        now: Date.now()
                    });

                    if (callback && !stoppedTimestamp) {
                        callback();
                    } else if (startTimestamp && !stoppedTimestamp && !interval) {
                        interval = setInterval(updateTimerDisplay, 1000);
                    } else if (stoppedTimestamp && interval) {
                        clearInterval(interval);
                        interval = null;
                    }

                    updateTimerDisplay();
                })
                .catch(err => {
                    console.error('❌ Fetch error:', err);
                });
        }


        window.onload = () => {
            timerEl = document.getElementById('timer');

            fetchMatchData();

            setInterval(() => {
                fetchMatchData();
            }, 3000);
        };
    </script>
</body>

</html>
