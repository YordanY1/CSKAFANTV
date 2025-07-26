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
            <button onclick="resetTimer()">Рестарт</button>
        </div>
    @endunless

    <script>
        let timerEl;
        let interval = null;
        let isPaused = true;
        let startTimestamp = null;

        function updateTimerDisplay() {
            if (!startTimestamp || isPaused) return;

            const elapsed = Date.now() - startTimestamp;
            const minutes = Math.floor(elapsed / 60000);
            const seconds = Math.floor((elapsed % 60000) / 1000);
            timerEl.innerText = String(minutes).padStart(2, '0') + ':' + String(seconds).padStart(2, '0');
        }

        function startTimer() {
            if (!startTimestamp) {
                startTimestamp = Date.now();
                localStorage.setItem('matchTimerStart', startTimestamp);
            }

            isPaused = false;

            if (!interval) {
                interval = setInterval(updateTimerDisplay, 1000);
            }
        }

        function pauseTimer() {
            isPaused = true;
        }

        function resetTimer() {
            localStorage.removeItem('matchTimerStart');
            startTimestamp = null;
            isPaused = true;
            clearInterval(interval);
            interval = null;
            timerEl.innerText = "00:00";
        }

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

            const storedStart = localStorage.getItem('matchTimerStart');
            if (storedStart) {
                startTimestamp = parseInt(storedStart);
                isPaused = false;
                updateTimerDisplay();
                interval = setInterval(updateTimerDisplay, 1000);
            }

            setInterval(fetchScore, 5000);
        };
    </script>
</body>

</html>
