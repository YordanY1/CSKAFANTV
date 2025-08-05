<section class="px-6 py-12 bg-card text-text animate-fade-in">
    <div class="max-w-7xl mx-auto">
        <h2 class="text-3xl text-primary font-extrabold uppercase mb-8 text-center tracking-wide">
            Мачове
        </h2>

        <div class="flex justify-center mb-8 flex-wrap gap-2 sm:gap-4">
            <button wire:click="setFilter('live')"
                class="px-3 py-1 sm:px-4 sm:py-1.5 rounded-xl text-[13px] sm:text-sm font-medium transition cursor-pointer
        {{ $filter === 'live' ? 'bg-accent text-white shadow-md' : 'bg-gray-200 text-gray-700 hover:bg-gray-300' }}">
                На живо
            </button>
            <button wire:click="setFilter('upcoming')"
                class="px-3 py-1 sm:px-4 sm:py-1.5 rounded-xl text-[13px] sm:text-sm font-medium transition cursor-pointer
        {{ $filter === 'upcoming' ? 'bg-accent text-white shadow-md' : 'bg-gray-200 text-gray-700 hover:bg-gray-300' }}">
                Предстоящи
            </button>
            <button wire:click="setFilter('completed')"
                class="px-3 py-1 sm:px-4 sm:py-1.5 rounded-xl text-[13px] sm:text-sm font-medium transition cursor-pointer
        {{ $filter === 'completed' ? 'bg-accent text-white shadow-md' : 'bg-gray-200 text-gray-700 hover:bg-gray-300' }}">
                Приключени
            </button>
        </div>



        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-6">
            @forelse ($matches as $match)
                <div wire:key="match-{{ $match->id }}"
                    class="bg-white rounded-xl shadow-lg overflow-hidden transition transform hover:scale-[1.02] duration-200">
                    <div class="bg-primary text-cta px-4 py-2 text-sm font-semibold tracking-wider">
                        {{ $match->match_datetime->timezone('Europe/Sofia')->locale('bg')->translatedFormat('d F Y • H:i \ч.') }}
                    </div>

                    <div class="flex justify-center mt-4" x-data="matchCountdown({
                        id: {{ $match->id }},
                        matchTime: '{{ $match->match_datetime->toIso8601String() }}',
                        isFinished: {{ (int) $match->is_finished }},
                        youtube: '{{ $match->youtube_url }}'
                    })" x-init="init()"
                        x-show="youtubeUrl">
                        <div x-show="label !== ''" @click="if (isLive && youtubeUrl) window.open(youtubeUrl, '_blank')"
                            class="flex items-center gap-2 px-5 py-3 rounded-lg text-white font-semibold shadow-lg transition-all duration-300"
                            x-bind:class="{
                                'bg-red-600 hover:bg-red-700 animate-pulse cursor-pointer': isLive,
                                'bg-yellow-500 hover:bg-yellow-600': !isLive && label.includes('⏳'),
                                'bg-gray-400': label.includes('✅')
                            }">
                            <i class="fab fa-youtube text-xl" x-show="isLive"></i>
                            <span x-text="label"></span>
                        </div>
                    </div>

                    <div class="p-6">
                        <h3 class="text-xl font-bold text-center text-accent mb-2">
                            {{ $match->homeTeam->name }} vs {{ $match->awayTeam->name }}
                        </h3>
                        <p class="text-sm text-gray-500 text-center mb-4">
                            {{ $match->stadium ?? '—' }}
                        </p>

                        <div class="flex justify-center space-x-6 items-center mb-2">
                            <div class="text-center">
                                <img src="{{ $match->homeTeam->logo_url }}" alt="{{ $match->homeTeam->name }}"
                                    class="w-12 h-12 mx-auto rounded-full shadow">
                                <p class="text-xs mt-1 font-semibold">{{ $match->homeTeam->name }}</p>
                            </div>

                            @if ($match->home_score !== null && $match->away_score !== null)
                                <span class="text-2xl font-bold text-gray-700">
                                    {{ $match->home_score }} : {{ $match->away_score }}
                                </span>
                            @else
                                <span class="text-2xl font-semibold text-gray-400">– : –</span>
                            @endif


                            <div class="text-center">
                                <img src="{{ $match->awayTeam->logo_url }}" alt="{{ $match->awayTeam->name }}"
                                    class="w-12 h-12 mx-auto rounded-full shadow">
                                <p class="text-xs mt-1 font-semibold">{{ $match->awayTeam->name }}</p>
                            </div>
                        </div>

                        @php
                            $matchEndTime = $match->match_datetime->copy()->addMinutes($match->duration ?? 90);
                            $hoursSinceEnd = now()->diffInHours($matchEndTime, false);
                            $showRatePlayers = $match->is_finished && $hoursSinceEnd >= -48;
                        @endphp

                        <div class="flex justify-center items-center gap-4 mt-4">
                            <a href="{{ route('match.show', $match) }}" wire:navigate
                                class="text-primary font-semibold hover:underline">
                                {{ $showRatePlayers ? 'ОЦЕНИ ИГРАЧИТЕ ⭐' : 'Детайли за мача' }}
                            </a>

                            @auth
                                @php
                                    $prediction = $predictions->get($match->id);
                                @endphp

                                @if (!$match->is_finished && $match->match_datetime->isFuture())
                                    <button x-data
                                        @click="$dispatch('open-prediction-modal', { matchId: {{ $match->id }} })"
                                        class="bg-primary text-white px-3 py-1.5 rounded text-sm cursor-pointer">
                                        {{ $prediction ? 'Моята прогноза' : 'Прогнозирай' }}
                                    </button>
                                @endif
                            @endauth
                        </div>
                    </div>
                </div>
            @empty
                <p class="text-center col-span-3 text-gray-500">
                    {{ $filter === 'upcoming' ? 'Няма предстоящи мачове.' : 'Няма приключени мачове.' }}
                </p>
            @endforelse
        </div>
    </div>
</section>
