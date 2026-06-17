<div>
    @include('livewire.pages.archive._nav', ['active' => 'archive.matches'])

    <section class="px-6 py-10 bg-card text-text animate-fade-in">
        <div class="max-w-7xl mx-auto">
            <h2 class="text-3xl text-primary font-extrabold uppercase mb-8 text-center tracking-wide">
                ⚽ Мачове · {{ $season }}
            </h2>

            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-6">
                @forelse ($matches as $match)
                    <div wire:key="match-{{ $match->id }}"
                        class="bg-white rounded-xl shadow-lg overflow-hidden transition transform hover:scale-[1.02] duration-200">
                        <div class="bg-primary text-cta px-4 py-2 text-sm font-semibold tracking-wider">
                            {{ $match->match_datetime->timezone('Europe/Sofia')->locale('bg')->translatedFormat('d F Y • H:i \ч.') }}
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

                            <div class="flex justify-center items-center gap-4 mt-4">
                                <a href="{{ route('match.show', $match) }}" wire:navigate
                                    class="text-primary font-semibold hover:underline">
                                    Детайли за мача <i class="fas fa-arrow-right ml-1"></i>
                                </a>
                            </div>
                        </div>
                    </div>
                @empty
                    <p class="text-center col-span-3 text-gray-500">Няма изиграни мачове за този сезон.</p>
                @endforelse
            </div>
        </div>
    </section>
</div>
