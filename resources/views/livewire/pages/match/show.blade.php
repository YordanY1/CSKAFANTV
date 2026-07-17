<div class="max-w-7xl mx-auto px-6 pt-16 pb-12 bg-card rounded-2xl shadow-2xl border border-primary font-primary">

    {{-- HEADER --}}
    <h1 class="text-4xl font-bold text-center text-primary tracking-wide mb-10 relative">
        {{ $match->homeTeam->name }}
        <span class="mx-2 text-accent">vs</span>
        {{ $match->awayTeam->name }}
        <div class="h-1 w-24 bg-accent mx-auto mt-4 rounded-full"></div>
    </h1>

    {{-- TIME LOGIC --}}
    @php
        $matchEndTime = $match->match_datetime->copy()->addMinutes($match->duration ?? 90);
        $hoursSinceEnd = $matchEndTime->diffInHours(now(), false);
        $canRate = $match->is_finished && $hoursSinceEnd <= 48;
    @endphp

    @auth
        @if ($canRate)
            <form wire:submit.prevent="submitPlayerReviews">
        @endif
    @endauth

    {{-- COACH --}}
    @if ($coach)
        @php
            $coachId = $coach->id;
            $average = $averageRatings[$coachId] ?? null;
            $avgClass = $average
                ? ($average >= 7
                    ? 'text-green-600 font-semibold'
                    : ($average >= 5
                        ? 'text-yellow-600'
                        : 'text-red-600'))
                : '';
        @endphp

        <div
            class="mt-16 max-w-xl mx-auto text-center bg-gradient-to-br from-white to-gray-50 p-8 rounded-2xl shadow-xl border border-accent/30">
            <h2 class="text-3xl font-bold text-primary mb-6 flex justify-center items-center gap-2">
                <i class="fas fa-chalkboard-teacher text-accent"></i> Треньор
            </h2>

            <img src="{{ $coach->avatar_url ?? asset('images/default-player.png') }}"
                alt="{{ $coach->name }}"
                class="w-28 h-28 rounded-full object-cover mx-auto mb-4 border-4 border-accent shadow-lg" />

            <p class="text-xl font-semibold text-gray-800">{{ $coach->name }}</p>
            <p class="text-sm text-gray-500 mt-1 italic">Старши треньор на ЦСКА</p>

            @if ($average)
                <p class="text-sm mt-3 {{ $avgClass }}">
                    Средна оценка досега:
                    <strong>{{ is_numeric($average) ? number_format($average, 1) : $average }}</strong>
                </p>
            @endif

            @auth
                @if (isset($existingReviews[$coachId]))
                    <p class="text-sm text-gray-500 mt-4">
                        Вече си оценил треньора:
                        <strong>{{ $existingReviews[$coachId] }}</strong>
                    </p>
                @elseif ($canRate)
                    <div x-data="{ selectedRating: '' }" class="mt-4">
                        <label class="text-sm text-accent-2 mt-1 block">Оцени треньора:</label>
                        <select x-model="selectedRating" wire:model.defer="ratings.{{ $coachId }}"
                            class="mt-1 w-full border-gray-300 rounded text-sm">
                            <option value="">– Избери –</option>
                            @for ($i = 1; $i <= 10; $i++)
                                <option value="{{ $i }}">{{ $i }}</option>
                            @endfor
                        </select>
                    </div>
                @elseif($match->is_finished && $hoursSinceEnd > 48)
                    <p class="text-xs text-gray-400 italic mt-3">
                        ⏳ Оценяването за този мач приключи.
                    </p>
                @endif
            @endauth

            @guest
                <p class="text-xs text-gray-500 mt-3">
                    Само регистрираните фенове могат да дават оценки. 🎯
                </p>
            @endguest
        </div>
    @endif

    {{-- PLAYERS --}}
    <div class="grid md:grid-cols-2 gap-10 mt-12">

        {{-- STARTERS --}}
        <div>
            <h2 class="text-2xl font-semibold text-text mb-5 border-b-2 border-accent pb-2">Стартов състав</h2>
            <ul class="space-y-4">
                @foreach ($match->lineup->where('is_starter', true) as $line)
                    @if ($line->player)
                        @php
                            $playerId = $line->player->id;
                            $average = $averageRatings[$playerId] ?? null;
                            $avgClass = $average
                                ? ($average >= 7
                                    ? 'text-green-600 font-semibold'
                                    : ($average >= 5
                                        ? 'text-yellow-600'
                                        : 'text-red-600'))
                                : '';
                        @endphp

                        <li
                            class="flex items-center gap-5 p-4 bg-white rounded-xl shadow-md border border-accent/30 hover:shadow-lg transition">
                            <img src="{{ $line->player?->avatar_url ?? asset('images/default-player.png') }}"
                                alt="{{ $line->player->name ?? 'Непознат играч' }}"
                                class="w-12 h-12 rounded-full object-cover border-2 border-accent shadow" />
                            <div class="text-text flex-1">
                                <div class="text-lg font-semibold">
                                    #{{ $line->player->number ?? '–' }}
                                    {{ $line->player->name ?? 'Неизвестен' }}
                                </div>

                                @if ($average)
                                    <p class="text-sm mt-2 {{ $avgClass }}">
                                        Средна оценка досега:
                                        <strong>{{ is_numeric($average) ? number_format($average, 1) : $average }}</strong>
                                    </p>
                                @endif

                                @auth
                                    @if (isset($existingReviews[$playerId]))
                                        <p class="text-sm text-gray-500 mt-1">
                                            Вече си оценил:
                                            <strong>{{ $existingReviews[$playerId] }}</strong>
                                        </p>
                                    @elseif ($canRate)
                                        <div x-data="{ selectedRating: '' }">
                                            <label class="text-sm text-accent-2 mt-1 block">Оцени играча:</label>
                                            <select x-model="selectedRating" wire:model.defer="ratings.{{ $playerId }}"
                                                class="mt-1 w-full border-gray-300 rounded text-sm">
                                                <option value="">– Избери –</option>
                                                @for ($i = 1; $i <= 10; $i++)
                                                    <option value="{{ $i }}">{{ $i }}</option>
                                                @endfor
                                            </select>
                                        </div>
                                    @elseif($match->is_finished && $hoursSinceEnd > 48)
                                        <p class="text-xs text-gray-400 italic mt-2">
                                            ⏳ Оценяването приключи.
                                        </p>
                                    @endif
                                @endauth

                                @guest
                                    <p class="text-xs text-gray-500 mt-3">
                                        Само регистрираните фенове могат да дават оценки. 🎯
                                    </p>
                                @endguest
                            </div>
                        </li>
                    @endif
                @endforeach
            </ul>
        </div>

        {{-- SUBSTITUTES --}}
        <div>
            <h2 class="text-2xl font-semibold text-text mb-5 border-b-2 border-accent pb-2">Смени</h2>
            <ul class="space-y-4">
                @foreach ($match->lineup->where('is_starter', false)->sortBy('minute_entered') as $line)
                    @if ($line->player)
                        @php
                            $playerId = $line->player->id;
                            $average = $averageRatings[$playerId] ?? null;
                            $avgClass = $average
                                ? ($average >= 7
                                    ? 'text-green-600 font-semibold'
                                    : ($average >= 5
                                        ? 'text-yellow-600'
                                        : 'text-red-600'))
                                : '';
                        @endphp

                        <li
                            class="flex items-center gap-5 p-4 bg-white rounded-xl shadow-md border border-accent/20 hover:shadow-lg transition">
                            <img src="{{ $line->player?->avatar_url ?? asset('images/default-player.png') }}"
                                alt="{{ $line->player->name ?? 'Непознат играч' }}"
                                class="w-16 h-16 rounded-full object-cover" />
                            <div class="text-text flex-1">
                                <div class="text-lg font-semibold">
                                    #{{ $line->player->number ?? '–' }}
                                    {{ $line->player->name ?? 'Неизвестен' }}
                                </div>

                                @if ($line->replacesPlayer)
                                    <p class="text-xs text-gray-500">Смени: {{ $line->replacesPlayer->name }}</p>
                                @endif

                                @if ($average)
                                    <p class="text-sm mt-2 {{ $avgClass }}">
                                        Средна оценка досега:
                                        <strong>{{ is_numeric($average) ? number_format($average, 1) : $average }}</strong>
                                    </p>
                                @endif

                                @auth
                                    @if (isset($existingReviews[$playerId]))
                                        <p class="text-sm text-gray-500 mt-1">
                                            Вече си оценил:
                                            <strong>{{ $existingReviews[$playerId] }}</strong>
                                        </p>
                                    @elseif ($canRate)
                                        <div x-data="{ selectedRating: '' }" class="mt-1">
                                            <label class="text-sm text-accent-2 block">Оцени играча:</label>
                                            <select x-model="selectedRating" wire:model.defer="ratings.{{ $playerId }}"
                                                class="mt-1 w-full border-gray-300 rounded text-sm">
                                                <option value="">– Избери –</option>
                                                @for ($i = 1; $i <= 10; $i++)
                                                    <option value="{{ $i }}">{{ $i }}</option>
                                                @endfor
                                            </select>
                                        </div>
                                    @elseif($match->is_finished && $hoursSinceEnd > 48)
                                        <p class="text-xs text-gray-400 italic mt-2">
                                            ⏳ Оценяването приключи.
                                        </p>
                                    @endif
                                @endauth

                                @guest
                                    <p class="text-xs text-gray-500 mt-3">
                                        Само регистрираните фенове могат да дават оценки. 🎯
                                    </p>
                                @endguest
                            </div>
                        </li>
                    @endif
                @endforeach
            </ul>
        </div>
    </div>

    {{-- SUBMIT BUTTON --}}
    @auth
        @if ($canRate)
            <div class="text-center mt-6">
                <button type="submit"
                    class="bg-primary text-white px-6 py-2 rounded-lg hover:bg-accent transition cursor-pointer font-semibold">
                    Изпрати оценки
                </button>
            </div>

            @if (session()->has('message'))
                <div class="mt-4 text-green-600 text-center font-semibold">
                    {{ session('message') }}
                </div>
            @endif
            </form>
        @endif
    @endauth

    {{-- EXTRA VIDEOS --}}
    @php
        $extraVideos = [
            '🎤 Гласът на ФЕНА' => $match->voice_of_the_fan_embed,
            '⏱️ Преди мача' => $match->before_match_embed,
            '🎙️ CSKA FAN TV TALK SHOW' => $match->talk_show_embed,
            '🔒 Специални стриймове за членове' => $match->member_stream_embed,
            '⭐ Именити червени фенове гостуват' => $match->celebrity_fans_embed,
            '🧓 Легендите говорят' => $match->legends_speak_embed,
            '🏆 Червена слава' => $match->red_glory_embed,
            '🌱 Бъдещето на ЦСКА' => $match->cska_future_embed,
            '👶 Децата на ЦСКА' => $match->cska_kids_embed,
            '📣 Отговори от гости' => $match->guest_answers_embed,
            '🏋️ Предсезонна подготовка' => $match->preseason_training_embed,
        ];
    @endphp

    @foreach ($extraVideos as $title => $embed)
        @if ($embed)
            <div class="mt-12">
                <div
                    class="bg-white rounded-xl shadow-xl border border-accent/20 p-4 sm:p-6 w-full max-w-full sm:max-w-4xl md:max-w-5xl lg:max-w-6xl mx-auto">
                    <h4
                        class="text-lg md:text-xl font-semibold text-primary mb-4 text-center flex items-center justify-center gap-2">
                        <i class="fas fa-play-circle text-red-500"></i>
                        {{ $title }}
                    </h4>

                    <div x-data x-init="$nextTick(() => {
                        const iframe = $el.querySelector('iframe');
                        if (iframe) {
                            iframe.removeAttribute('width');
                            iframe.removeAttribute('height');
                            iframe.classList.add(
                                'w-full',
                                'h-[230px]',
                                'sm:h-[320px]',
                                'md:h-[420px]',
                                'lg:h-[600px]',
                                'rounded-xl'
                            );
                        }
                    })"
                        class="w-full overflow-hidden ring-0 sm:ring-1 ring-accent/20 shadow-none sm:shadow-lg">
                        {!! $embed !!}
                    </div>
                </div>
            </div>
        @endif
    @endforeach
</div>
