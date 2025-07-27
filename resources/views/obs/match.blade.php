@extends('layouts.obs')

@section('content')
    <div x-data="goalBanner()" x-init="init()"
        class="w-screen h-screen flex items-center justify-center bg-transparent px-4 relative">

        {{-- Goal banner --}}
        <div x-show="show" x-transition:enter="transform transition ease-out duration-500"
            x-transition:enter-start="scale-75 opacity-0" x-transition:enter-end="scale-100 opacity-100"
            x-transition:leave="transform transition ease-in duration-300" x-transition:leave-start="scale-100 opacity-100"
            x-transition:leave-end="scale-75 opacity-0"
            :class="isЦСКАGoal ? 'bg-white text-red-600' : 'bg-yellow-400 text-black'"
            class="absolute top-10 text-3xl font-extrabold px-6 py-3 rounded-xl shadow-xl tracking-wide z-50">
            ⚽ ГОООЛ!
        </div>


        <div class="flex flex-col items-center gap-6">

            {{-- ✅ Scoreboard --}}
            <div
                class="inline-flex items-center gap-4 bg-black/90 px-6 py-3 rounded-2xl shadow-xl border border-white/10 backdrop-blur-md">
                {{-- Home team --}}
                <div class="flex items-center gap-2 font-bold text-red-500 text-xl">
                    @if ($match->homeTeam?->logo)
                        <img src="{{ asset('storage/' . $match->homeTeam->logo) }}" alt="logo" class="h-[28px] w-auto">
                    @endif
                    <span class="uppercase tracking-wide">{{ $match->homeTeam->name }}</span>
                </div>

                {{-- Score --}}
                <div class="text-white text-[32px] font-extrabold px-4">
                    <span id="score">{{ $match->obs_home_score ?? 0 }} : {{ $match->obs_away_score ?? 0 }}</span>
                </div>

                {{-- Away team --}}
                <div class="flex items-center gap-2 font-bold text-red-500 text-xl">
                    <span class="uppercase tracking-wide">{{ $match->awayTeam->name }}</span>
                    @if ($match->awayTeam?->logo)
                        <img src="{{ asset('storage/' . $match->awayTeam->logo) }}" alt="logo" class="h-[28px] w-auto">
                    @endif
                </div>

                {{-- Timer --}}
                <div class="ml-6 text-[20px] font-extrabold text-red-300 bg-white/10 px-3 py-1 rounded-lg tracking-wide shadow-inner"
                    id="timer">
                    00:00
                </div>
            </div>

            {{-- ✅ Controls (само извън OBS) --}}
            @unless ($isOBS)
                <div class="mt-4 flex flex-wrap justify-center gap-3">
                    @foreach ([['label' => 'Старт', 'action' => 'startTimer()'], ['label' => 'Пауза', 'action' => 'pauseTimer()'], ['label' => 'Продължи', 'action' => 'resumeTimer()'], ['label' => '⬅ -10 сек', 'action' => 'adjustTime(-10)']] as $btn)
                        <button onclick="{{ $btn['action'] }}"
                            class="px-4 py-1.5 text-sm font-bold text-white bg-red-600 hover:bg-red-700 active:bg-red-800 rounded-lg shadow">
                            {{ $btn['label'] }}
                        </button>
                    @endforeach

                    <label class="flex items-center gap-2 text-white text-sm font-semibold">
                        Домакин:
                        <select onchange="updateScore('home', this.value)"
                            class="text-sm p-1.5 rounded border border-gray-300 bg-white text-black">
                            @for ($i = 0; $i <= 20; $i++)
                                <option value="{{ $i }}" {{ $match->obs_home_score == $i ? 'selected' : '' }}>
                                    {{ $i }}
                                </option>
                            @endfor
                        </select>
                    </label>

                    <label class="flex items-center gap-2 text-white text-sm font-semibold">
                        Гост:
                        <select onchange="updateScore('away', this.value)"
                            class="text-sm p-1.5 rounded border border-gray-300 bg-white text-black">
                            @for ($i = 0; $i <= 20; $i++)
                                <option value="{{ $i }}" {{ $match->obs_away_score == $i ? 'selected' : '' }}>
                                    {{ $i }}
                                </option>
                            @endfor
                        </select>
                    </label>
                </div>
            @endunless
        </div>
    </div>
@endsection



@push('scripts')
    <script>
        let timerEl;
        let interval = null;
        let startTimestamp = null;
        let stoppedTimestamp = null;
        let adjustSeconds = 0;

        const csrf = document.querySelector('meta[name="csrf-token"]').getAttribute('content');

        function updateTimerDisplay() {
            if (!startTimestamp) return;

            const now = Date.now();
            const effectiveNow = stoppedTimestamp ?? now;
            let elapsed = effectiveNow - startTimestamp;

            elapsed += adjustSeconds * 1000;

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

        function updateScore(team, value) {
            fetch("{{ route('obs.match.score', ['slug' => $match->slug]) }}", {
                    method: 'POST',
                    headers: {
                        'X-CSRF-TOKEN': csrf,
                        'Content-Type': 'application/json'
                    },
                    body: JSON.stringify({
                        team,
                        value
                    })
                })
                .then(res => res.ok ? res.json() : Promise.reject(res))
                .then(() => fetchMatchData())
                .catch(err => {
                    console.error('❌ Score update error:', err);
                });
        }

        function adjustTime(seconds) {
            fetch("{{ route('obs.match.adjust', ['slug' => $match->slug]) }}", {
                    method: 'POST',
                    headers: {
                        'X-CSRF-TOKEN': csrf,
                        'Content-Type': 'application/json'
                    },
                    body: JSON.stringify({
                        seconds
                    })
                })
                .then(() => fetchMatchData(updateTimerDisplay))
                .catch(err => console.error('⛔ Adjust error:', err));
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
                    adjustSeconds = data.adjust_seconds ?? 0;

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

        function goalBanner() {
            return {
                show: false,
                isCskaGoal: false,
                lastHome: {{ $match->obs_home_score ?? 0 }},
                lastAway: {{ $match->obs_away_score ?? 0 }},
                homeTeam: @json($match->homeTeam->name),
                awayTeam: @json($match->awayTeam->name),

                init() {
                    setInterval(() => this.checkForGoal(), 3000);
                },

                checkForGoal() {
                    fetch("{{ route('obs.match.json', ['slug' => $match->slug]) }}", {
                            cache: 'no-store'
                        })
                        .then(res => res.json())
                        .then(data => {
                            const newHome = data.home_score;
                            const newAway = data.away_score;

                            const homeScored = newHome > this.lastHome;
                            const awayScored = newAway > this.lastAway;

                            const homeIsCSKA = this.homeTeam.trim().toUpperCase() === 'ЦСКА';
                            const awayIsCSKA = this.awayTeam.trim().toUpperCase() === 'ЦСКА';

                            this.isCskaGoal = false;

                            if ((homeScored && homeIsCSKA) || (awayScored && awayIsCSKA)) {
                                this.isCskaGoal = true;
                                this.show = true;
                                setTimeout(() => this.show = false, 3000);
                            }

                            this.lastHome = newHome;
                            this.lastAway = newAway;

                            document.getElementById('score').innerText = `${newHome} : ${newAway}`;

                            const timerEl = document.getElementById('timer');
                            if (data.started_at) {
                                const now = Date.now();
                                const effectiveNow = data.stopped_at ? data.stopped_at * 1000 : now;
                                let elapsed = effectiveNow - data.started_at * 1000;
                                elapsed += (data.adjust_seconds ?? 0) * 1000;
                                const minutes = Math.floor(elapsed / 60000);
                                const seconds = Math.floor((elapsed % 60000) / 1000);
                                timerEl.innerText = String(minutes).padStart(2, '0') + ':' + String(seconds).padStart(2,
                                    '0');
                            }
                        })
                        .catch(err => console.error('⚠️ Fetch error:', err));
                }
            };
        }



        window.onload = () => {
            timerEl = document.getElementById('timer');
            fetchMatchData();
            setInterval(fetchMatchData, 3000);
        };
    </script>
@endpush
