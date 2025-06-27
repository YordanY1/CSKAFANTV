<section class="px-6 py-12 bg-card text-text animate-fade-in">
    <div class="max-w-7xl mx-auto">
        <h2 class="text-3xl text-primary font-extrabold uppercase mb-8 text-center tracking-wide">
            Мачове
        </h2>

        <div class="flex justify-center mb-10 space-x-4">
            <button wire:click="setFilter('upcoming')"
                class="px-6 py-2 rounded-full text-sm font-semibold transition
                    {{ $filter === 'upcoming' ? 'bg-accent text-white shadow-lg' : 'bg-gray-200 text-gray-700 hover:bg-gray-300' }}">
                Предстоящи
            </button>
            <button wire:click="setFilter('completed')"
                class="px-6 py-2 rounded-full text-sm font-semibold transition
                    {{ $filter === 'completed' ? 'bg-accent text-white shadow-lg' : 'bg-gray-200 text-gray-700 hover:bg-gray-300' }}">
                Приключени
            </button>
        </div>

        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-6">
            @forelse ($matches as $match)
                <div
                    class="bg-white rounded-xl shadow-lg overflow-hidden transition transform hover:scale-[1.02] duration-200">
                    <div class="bg-primary text-cta px-4 py-2 text-sm font-semibold tracking-wider">
                        {{ $match->match_datetime->locale('bg')->translatedFormat('d F Y • H:i \ч.') }}
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

                            @if ($filter === 'completed')
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

                        <a href="{{ route('match.show', $match->id) }}" wire:navigate
                            class="block mt-4 text-center text-primary font-semibold hover:underline">
                            Детайли за мача <i class="fas fa-arrow-right ml-1"></i>
                        </a>
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
