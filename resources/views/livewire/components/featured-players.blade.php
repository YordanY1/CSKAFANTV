<section class="px-6 py-12 bg-card text-text animate-fade-in">
    <div class="max-w-6xl mx-auto">
        <h2 class="text-3xl text-primary font-extrabold uppercase mb-8 text-center tracking-wide">
            Избрани играчи
        </h2>

        <div class="grid grid-cols-2 sm:grid-cols-2 md:grid-cols-4 gap-6">
            @foreach ($players as $player)
                <div
                    class="bg-white rounded-xl shadow-lg overflow-hidden text-center transform hover:scale-105 transition duration-300">
                    <img src="{{ $player['image'] }}" alt="{{ $player['name'] }}"
                        class="w-24 h-24 mx-auto mt-6 rounded-full object-cover ring-4 ring-accent" />

                    <div class="p-4">
                        <h3 class="text-lg font-bold text-primary">{{ $player['name'] }}</h3>
                        <p class="text-sm text-gray-600">#{{ $player['number'] }} | {{ $player['position'] }}</p>
                    </div>
                </div>
            @endforeach
        </div>
    </div>
</section>
