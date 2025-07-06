<div class="max-w-7xl mx-auto px-6 pt-16 pb-12 bg-card rounded-2xl shadow-2xl border border-primary font-primary">
    <h1 class="text-4xl font-bold text-center text-primary tracking-wide mb-10 relative">
        {{ $match->homeTeam->name }}
        <span class="mx-2 text-accent">vs</span>
        {{ $match->awayTeam->name }}
        <div class="h-1 w-24 bg-accent mx-auto mt-4 rounded-full"></div>
    </h1>

    @auth
        @if ($match->is_finished)
            <form wire:submit.prevent="submitPlayerReviews">
        @endif
    @endauth

    <div class="grid md:grid-cols-2 gap-10">
        {{-- Starters --}}
        <div>
            <h2 class="text-2xl font-semibold text-text mb-5 border-b-2 border-accent pb-2">Стартов състав</h2>
            <ul class="space-y-4">
                @foreach ($match->lineup->where('is_starter', true) as $line)
                    @if ($line->player)
                        <li
                            class="flex items-center gap-5 p-4 bg-white rounded-xl shadow-md border border-accent/30 hover:shadow-lg transition">
                            <img src="{{ asset('storage/' . ($line->player->image_path ?? 'images/default-player.jpg')) }}"
                                alt="{{ $line->player->name ?? 'Непознат играч' }}"
                                class="w-12 h-12 rounded-full object-cover border-2 border-accent shadow" />
                            <div class="text-text flex-1">
                                <div class="text-lg font-semibold">
                                    #{{ $line->player->number ?? '–' }}
                                    {{ $line->player->name ?? 'Неизвестен' }}
                                </div>

                                @auth
                                    @if ($match->is_finished)
                                        @if (!isset($existingReviews[$line->player->id]))
                                            <label class="text-sm text-accent-2 mt-1 block">Оцени играча:</label>
                                            <select wire:model.defer="ratings.{{ $line->player->id }}"
                                                class="mt-1 w-full border-gray-300 rounded text-sm">
                                                <option value="">– Избери –</option>
                                                @for ($i = 1; $i <= 10; $i++)
                                                    <option value="{{ $i }}">{{ $i }}</option>
                                                @endfor
                                            </select>
                                        @else
                                            <p class="text-sm text-gray-500 mt-1">
                                                Вече си оценил:
                                                <strong>{{ $existingReviews[$line->player->id] }}</strong>
                                            </p>
                                        @endif
                                    @endif
                                @endauth
                            </div>
                        </li>
                    @endif
                @endforeach
            </ul>
        </div>

        {{-- Substitutes --}}
        <div>
            <h2 class="text-2xl font-semibold text-text mb-5 border-b-2 border-accent pb-2">Смени</h2>
            <ul class="space-y-4">
                @foreach ($match->lineup->where('is_starter', false)->sortBy('minute_entered') as $line)
                    @if ($line->player)
                        <li
                            class="flex items-center gap-5 p-4 bg-white rounded-xl shadow-md border border-accent/20 hover:shadow-lg transition">
                            <img src="{{ $line->player->image_path
                                ? asset('storage/' . $line->player->image_path)
                                : asset('images/default-player.jpg') }}"
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

                                @auth
                                    @if ($match->is_finished)
                                        @if (!isset($existingReviews[$line->player->id]))
                                            <label class="text-sm text-accent-2 mt-1 block">Оцени играча:</label>
                                            <select wire:model.defer="ratings.{{ $line->player->id }}"
                                                class="mt-1 w-full border-gray-300 rounded text-sm">
                                                <option value="">– Избери –</option>
                                                @for ($i = 1; $i <= 10; $i++)
                                                    <option value="{{ $i }}">{{ $i }}</option>
                                                @endfor
                                            </select>
                                        @else
                                            <p class="text-sm text-gray-500 mt-1">
                                                Вече си оценил:
                                                <strong>{{ $existingReviews[$line->player->id] }}</strong>
                                            </p>
                                        @endif
                                    @endif
                                @endauth
                            </div>
                        </li>
                    @endif
                @endforeach
            </ul>
        </div>
    </div>

    @auth
        @if ($match->is_finished)
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

    @if ($match->youtube_embed_url)
        <div class="mt-16 px-4">
            <div class="mt-16 sm:px-4 px-0">
                <div
                    class="bg-white rounded-none sm:rounded-2xl shadow-none sm:shadow-xl border-none sm:border border-accent/30 p-0 sm:p-6 w-full max-w-full sm:max-w-4xl md:max-w-5xl lg:max-w-6xl mx-auto">
                    <h3
                        class="text-xl md:text-2xl font-bold text-primary mb-4 text-center flex items-center justify-center gap-2">
                        <i class="fas fa-video text-red-500"></i>
                        Видео от мача
                    </h3>

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
                        {!! $match->youtube_embed_url !!}
                    </div>

                </div>
            </div>

        </div>
    @endif

</div>
