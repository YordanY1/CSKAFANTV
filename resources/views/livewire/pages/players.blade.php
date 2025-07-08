<div class="max-w-7xl mx-auto px-4 py-10 font-primary">
    @if ($coaches->count())
        <div class="max-w-4xl mx-auto mb-12">
            <h2 class="text-2xl text-center font-bold text-primary mb-6">Треньорски щаб</h2>

            <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 gap-6">
                @foreach ($coaches as $coach)
                    <div
                        class="relative group rounded-2xl overflow-hidden shadow-md bg-white hover:shadow-xl transition duration-300">
                        <div class="relative">
                            <img src="{{ $coach->image_path ? asset('storage/' . $coach->image_path) : asset('images/default-player.png') }}"
                                alt="{{ $coach->name }}"
                                class="w-full h-[300px] object-cover transition-transform duration-300 group-hover:scale-105" />
                            <div class="absolute inset-0 bg-black/30 group-hover:bg-black/40 transition duration-300">
                            </div>

                            <div class="absolute bottom-0 w-full p-4 text-white">
                                <h3 class="text-lg font-semibold">{{ $coach->name }}</h3>
                                <p class="text-sm text-cta italic">{{ $coach->position }}</p>
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>
        </div>
    @endif

    <h1 class="text-3xl font-bold text-center text-primary mb-8">Отбор</h1>

    <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-6">
        @foreach ($players as $player)
            <div
                class="relative group rounded-2xl overflow-hidden shadow-md bg-card hover:shadow-xl transition duration-300">
                <div class="relative">
                    <img src="{{ $player->image_path ? asset('storage/' . $player->image_path) : asset('images/default-player.png') }}"
                        alt="{{ $player->name }}"
                        class="max-h-full max-w-full object-contain transition-transform duration-300 group-hover:scale-105" />

                    <div class="absolute inset-0 bg-black/30 group-hover:bg-black/40 transition duration-300"></div>

                    <div class="absolute bottom-0 w-full p-4 text-cta">
                        <h2 class="text-xl font-semibold">{{ $player->name }}</h2>
                        <p class="text-sm text-accent">№ {{ $player->number }}</p>
                        <p class="text-sm text-cta italic">{{ $player->position }}</p>
                    </div>
                </div>
            </div>
        @endforeach
    </div>
</div>
