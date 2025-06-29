<div class="max-w-7xl mx-auto px-6 pt-16 pb-12 bg-card rounded-2xl shadow-2xl border border-primary font-primary">
    <h1 class="text-4xl font-bold text-center text-primary tracking-wide mb-10 relative">
        {{ $match->homeTeam->name }}
        <span class="mx-2 text-accent">vs</span>
        {{ $match->awayTeam->name }}
        <div class="h-1 w-24 bg-accent mx-auto mt-4 rounded-full"></div>
    </h1>

    <div class="md:flex md:gap-10">
        {{-- Left: Match Info --}}
        <div class="flex-1 space-y-10">
            {{-- Lineups --}}
            <div class="grid md:grid-cols-2 gap-10">
                {{-- Starters --}}
                {{-- Starters --}}
                <div>
                    <h2 class="text-2xl font-semibold text-text mb-5 border-b-2 border-accent pb-2">Стартов състав</h2>
                    <ul class="space-y-4">
                        @foreach ($match->lineup->where('is_starter', true) as $line)
                            @if ($line->player)
                                <li
                                    class="flex items-center gap-5 p-4 bg-white rounded-xl shadow-md border border-accent/30 hover:shadow-lg transition">
                                    <img src="{{ asset($line->player->image_path ?? 'images/default-player.jpg') }}"
                                        alt="{{ $line->player->name ?? 'Непознат играч' }}"
                                        class="w-12 h-12 rounded-full object-cover border-2 border-accent shadow" />
                                    <div class="text-text">
                                        <div class="text-lg font-semibold">
                                            #{{ $line->player->number ?? '–' }}
                                            {{ $line->player->name ?? 'Неизвестен' }}
                                        </div>
                                        <div class="text-sm text-accent-2">
                                            Позиция: {{ $line->player->position ?? '–' }}
                                        </div>
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
                                    <img src="{{ asset($line->player->image_path ?? 'images/default-player.jpg') }}"
                                        alt="{{ $line->player->name ?? 'Непознат играч' }}"
                                        class="w-12 h-12 rounded-full object-cover border-2 border-accent shadow" />
                                    <div class="text-text">
                                        <div class="text-lg font-semibold">
                                            #{{ $line->player->number ?? '–' }}
                                            {{ $line->player->name ?? 'Неизвестен' }}
                                        </div>
                                        <div class="text-sm text-accent-2">
                                            Позиция: {{ $line->player->position ?? '–' }}
                                        </div>
                                    </div>
                                </li>
                            @endif
                        @endforeach
                    </ul>
                </div>

                {{-- Ratings --}}
                @auth
                    @if ($match->is_finished && $match->lineup->count())
                        <div class="bg-white p-6 rounded-xl shadow-lg border border-accent/30">
                            <h3 class="text-xl font-bold text-primary text-center mb-6">Оцени Играчите</h3>

                            <form wire:submit.prevent="submitPlayerReviews">
                                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 max-h-[60vh] overflow-y-auto">
                                    @foreach ($match->lineup as $line)
                                        <div class="flex items-center gap-3 p-3 bg-card rounded-lg">
                                            <img src="{{ asset($line->player->image_path) }}"
                                                class="w-10 h-10 rounded-full object-cover border border-accent">
                                            <div>
                                                <div class="font-semibold text-text">{{ $line->player->name }}</div>
                                                <select wire:model.defer="ratings.{{ $line->player->id }}"
                                                    class="mt-1 w-full border-gray-300 rounded">
                                                    <option value="">Избери оценка</option>
                                                    @for ($i = 1; $i <= 5; $i++)
                                                        <option value="{{ $i }}">{{ $i }}</option>
                                                    @endfor
                                                </select>
                                            </div>
                                        </div>
                                    @endforeach
                                </div>

                                <div class="text-center mt-6">
                                    <button type="submit"
                                        class="bg-primary text-white px-6 py-2 rounded-lg hover:bg-accent transition cursor-pointer font-semibold">
                                        Изпрати оценки
                                    </button>
                                </div>
                            </form>

                            @if (session()->has('message'))
                                <div class="mt-4 text-green-600 text-center font-semibold">
                                    {{ session('message') }}
                                </div>
                            @endif
                        </div>
                    @endif
                @endauth
            </div>

            {{-- Right: Video --}}
            @if ($match->youtube_embed_url)
                <div class="flex-1 flex flex-col justify-center">
                    <div class="bg-white rounded-xl shadow-lg border border-accent/20 p-4">
                        <h3 class="text-xl font-semibold text-primary mb-4 text-center">Видео от мача</h3>
                        <div class="aspect-video rounded overflow-hidden">
                            {!! $match->youtube_embed_url !!}
                        </div>
                    </div>
                </div>
            @endif
        </div>
    </div>
