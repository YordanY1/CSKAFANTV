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
    </style>
</head>

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

    <audio id="goal-audio" src="{{ asset('sounds/goal.mp3') }}" preload="auto"></audio>

    <script>
        let startTime = new Date().getTime();
        let elapsedBeforePause = 0;
        let timerInterval = null;

        let lastScore = {
            home: {{ $match->home_score ?? 0 }},
            away: {{ $match->away_score ?? 0 }}
        };

        function updateTimerDisplay() {
            const now = new Date().getTime();
            const diff = now - startTime + elapsedBeforePause;
            const minutes = Math.floor(diff / 60000);
            const seconds = Math.floor((diff % 60000) / 1000);
            const formatted = String(minutes).padStart(2, '0') + ':' + String(seconds).padStart(2, '0');
            document.getElementById('timer').innerText = formatted;
        }

        function fetchScore() {
            fetch("{{ route('obs.match', ['slug' => $match->slug, 'token' => request('token')]) }}/json")
                .then(res => res.json())
                .then(data => {
                    const newHome = data.home_score;
                    const newAway = data.away_score;

                    if (newHome !== lastScore.home || newAway !== lastScore.away) {
                        document.getElementById('score').innerText = `${newHome} : ${newAway}`;

                        if ((newHome > lastScore.home || newAway > lastScore.away)) {
                            document.getElementById('goal-audio').play();
                        }

                        lastScore = {
                            home: newHome,
                            away: newAway
                        };
                    }
                });
        }

        updateTimerDisplay();
        timerInterval = setInterval(updateTimerDisplay, 1000);
        setInterval(fetchScore, 5000);
    </script>
</body>

</html>
