<section class="px-6 py-12 bg-card text-text animate-fade-in">
    <div class="max-w-6xl mx-auto">


        <h2 class="text-3xl text-primary font-extrabold uppercase mb-8 text-center tracking-wide">
            Избрани играчи
        </h2>

        <div class="text-center mb-6">
            <a href="{{ route('player.ratings') }}" wire:navigate
                class="inline-block bg-accent text-white font-semibold px-6 py-2 rounded-lg shadow hover:bg-primary transition duration-200">
                🏆 Пълна класация
            </a>
        </div>

        @if ($playerOfMonth)
            <div class="mb-12 text-center">
                <h3 class="text-2xl font-bold text-accent mb-6">Играч на месеца</h3>
                <div class="flex justify-center">
                    <div
                        class="bg-white rounded-xl shadow-lg overflow-hidden text-center transform hover:scale-105 transition duration-300">
                        <img src="{{ $playerOfMonth['image'] }}" alt="{{ $playerOfMonth['name'] }}"
                            class="w-24 h-24 mx-auto mt-6 rounded-full object-cover ring-4 ring-primary" />
                        <div class="p-4">
                            <h3 class="text-lg font-bold text-primary">{{ $playerOfMonth['name'] }}</h3>
                            <p class="text-sm text-gray-600">#{{ $playerOfMonth['number'] }} |
                                {{ $playerOfMonth['position'] }}</p>
                            <p class="text-sm text-green-600 font-semibold mt-1">Средна: {{ $playerOfMonth['avg'] }}</p>
                        </div>
                    </div>
                </div>
            </div>
        @endif

        <div class="grid grid-cols-2 sm:grid-cols-2 md:grid-cols-4 gap-6">
            @foreach ($players as $player)
                <div
                    class="bg-white rounded-xl shadow-lg overflow-hidden text-center transform hover:scale-105 transition duration-300">
                    <img src="{{ $player['image'] }}" alt="{{ $player['name'] }}"
                        class="w-24 h-24 mx-auto mt-6 rounded-full object-cover ring-4 ring-accent" />

                    <div class="p-4">
                        <h3 class="text-lg font-bold text-primary">{{ $player['name'] }}</h3>
                        <p class="text-sm text-accent font-semibold mt-1">Средна: {{ $player['avg'] }}</p>
                    </div>
                </div>
            @endforeach
        </div>
    </div>
</section>
