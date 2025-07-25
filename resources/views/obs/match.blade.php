<!DOCTYPE html>
<html lang="bg">

<head>
    <meta charset="UTF-8">
    <title>OBS Scoreboard</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <style>
        :root {
            --color-primary: #b91c1c;
            --color-card: #fef2f2;
            --color-text: #1e1e1e;
            --color-accent: #ef4444;
            --color-accent-2: #f87171;
            --color-cta: #ffffff;
        }

        * {
            box-sizing: border-box;
        }

        body {
            margin: 0;
            background: transparent;
            font-family: 'Arial Black', sans-serif;
            font-size: 36px;
            color: var(--color-cta);
            display: flex;
            flex-direction: column;
            align-items: center;
        }

        .scoreboard {
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 40px;
            background: rgba(0, 0, 0, 0.8);
            padding: 30px 40px;
            border-radius: 18px;
            width: auto;
            max-width: 100%;
        }



        .team {
            display: flex;
            align-items: center;
            gap: 12px;
            font-weight: bold;
            color: var(--color-accent);
            white-space: nowrap;
        }

        .team-logo {
            height: 48px;
            width: auto;
        }

        .timer-inline {
            font-size: 32px;
            font-weight: bold;
            color: var(--color-accent-2);
            margin-left: 20px;
            white-space: nowrap;
        }


        .score {
            display: flex;
            align-items: center;
            justify-content: center;
            min-width: 120px;
            font-size: 72px;
            font-weight: bold;
            color: var(--color-cta);
        }


        .timer {
            font-size: 32px;
            margin-top: 12px;
            color: var(--color-accent-2);
            text-align: center;
        }

        .controls {
            margin-top: 16px;
            display: flex;
            gap: 12px;
        }

        .controls button {
            background-color: var(--color-accent);
            color: white;
            border: none;
            padding: 10px 20px;
            font-size: 20px;
            border-radius: 8px;
            cursor: pointer;
        }

        @media screen and (max-height: 300px) {
            .controls {
                display: none;
            }
        }
    </style>
</head>

<body>
    <div class="scoreboard">
        <div class="team">
            @if ($match->homeTeam?->logo)
                <img src="{{ asset('storage/' . $match->homeTeam->logo) }}" alt="{{ $match->homeTeam->name }}"
                    class="team-logo">
            @endif
            <span>{{ $match->homeTeam->name ?? '—' }}</span>
        </div>

        <div class="score">
            {{ $match->home_score }} : {{ $match->away_score }}
        </div>

        <div class="team">
            <span>{{ $match->awayTeam->name ?? '—' }}</span>
            @if ($match->awayTeam?->logo)
                <img src="{{ asset('storage/' . $match->awayTeam->logo) }}" alt="{{ $match->awayTeam->name }}"
                    class="team-logo">
            @endif
        </div>

        <div class="timer-inline" id="timer">00:00</div>
    </div>


    <div class="controls">
        <button onclick="startTimer()">Старт</button>
        <button onclick="pauseTimer()">Пауза</button>
        <button onclick="resetTimer()">Рестарт</button>
    </div>

    <script>
        let startTime = null;
        let elapsedBeforePause = 0;
        let timerInterval = null;

        function updateTimerDisplay() {
            const now = new Date().getTime();
            const diff = now - startTime + elapsedBeforePause;

            const minutes = Math.floor(diff / 60000);
            const seconds = Math.floor((diff % 60000) / 1000);
            const formatted = String(minutes).padStart(2, '0') + ':' + String(seconds).padStart(2, '0');

            document.getElementById('timer').innerText = formatted;
        }

        function startTimer() {
            if (!startTime) {
                startTime = new Date().getTime();
            } else {
                startTime = new Date().getTime() - elapsedBeforePause;
            }

            if (timerInterval) clearInterval(timerInterval);
            timerInterval = setInterval(updateTimerDisplay, 1000);
        }

        function pauseTimer() {
            if (timerInterval) {
                clearInterval(timerInterval);
                timerInterval = null;
            }
            if (startTime) {
                elapsedBeforePause += new Date().getTime() - startTime;
                startTime = null;
            }
        }

        function resetTimer() {
            if (timerInterval) clearInterval(timerInterval);
            startTime = null;
            elapsedBeforePause = 0;
            timerInterval = null;
            document.getElementById('timer').innerText = "00:00";
        }

        // Авто старт (ако искаш OBS да започва таймера автоматично)
        // startTimer();
    </script>
</body>

</html>
