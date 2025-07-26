<!DOCTYPE html>
<html lang="bg">

<head>
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

        .extra-time {
            font-size: 16px;
            font-weight: bold;
            color: var(--light-red);
            margin-top: 4px;
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

    {{-- <div class="extra-time" id="extra-time-indicator"></div> --}}

    @unless ($isOBS)
        <div class="controls">
            <button onclick="startTimer()">Старт</button>
            <button onclick="pauseTimer()">Пауза</button>
            {{-- <button onclick="addExtraTime(1)">+1 мин</button> --}}
        </div>
    @endunless

    <script>
        let elapsedTime = 0;
        let extraMinutes = 0;
        let timerInterval = null;
        let isPaused = true;

        let timerEl, extraIndicator;

        function updateTimerDisplay() {
            const totalTime = elapsedTime + extraMinutes * 60000;
            const minutes = Math.floor(totalTime / 60000);
            const seconds = Math.floor((totalTime % 60000) / 1000);
            timerEl.innerText = String(minutes).padStart(2, '0') + ':' + String(seconds).padStart(2, '0');

            if (extraMinutes > 0) {
                extraIndicator.innerText = `ДОБАВЕНО ВРЕМЕ: ${extraMinutes}'`;
            } else {
                extraIndicator.innerText = '';
            }
        }

        function startTimer() {
            if (isPaused) {
                const start = Date.now() - elapsedTime;
                timerInterval = setInterval(() => {
                    elapsedTime = Date.now() - start;
                    updateTimerDisplay();
                }, 1000);
                isPaused = false;
            }
        }

        function pauseTimer() {
            clearInterval(timerInterval);
            isPaused = true;
        }

        // function addExtraTime(mins) {
        //     extraMinutes += mins;
        //     updateTimerDisplay();
        // }

        function fetchScore() {
            fetch("{{ route('obs.match.json', ['slug' => $match->slug]) }}", {
                    cache: 'no-store'
                })
                .then(res => res.json())
                .then(data => {
                    const newHome = data.home_score;
                    const newAway = data.away_score;

                    if (newHome !== lastScore.home || newAway !== lastScore.away) {
                        document.getElementById('score').innerText = `${newHome} : ${newAway}`;
                        lastScore = {
                            home: newHome,
                            away: newAway
                        };
                    }
                });
        }

        let lastScore = {
            home: {{ $match->home_score ?? 0 }},
            away: {{ $match->away_score ?? 0 }}
        };

        window.onload = () => {
            timerEl = document.getElementById('timer');
            extraIndicator = document.getElementById('extra-time-indicator');

            const urlParams = new URLSearchParams(window.location.search);
            const startMinutes = parseInt(urlParams.get('set') || '0');
            const extraParam = parseInt(urlParams.get('extra') || '0');

            elapsedTime = !isNaN(startMinutes) ? startMinutes * 60000 : 0;
            extraMinutes = !isNaN(extraParam) ? extraParam : 0;

            updateTimerDisplay();

            const userAgent = navigator.userAgent || '';
            if (userAgent.includes('OBS')) {
                startTimer();
            }

            setInterval(fetchScore, 5000);
        };
    </script>
</body>

</html>
