<div>
    @include('livewire.pages.archive._nav', ['active' => 'archive.hall-of-fame'])

    <section class="px-6 py-10 text-gray-800 animate-fade-in">
        <div class="max-w-7xl mx-auto">
            <h2 class="text-3xl md:text-4xl font-extrabold text-center uppercase text-primary mb-12 tracking-wide">
                🏅 Зала на славата · {{ $season }}
            </h2>

            <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 xl:grid-cols-4 gap-10">
                @forelse ($awards->filter(fn($a) => $a->player) as $award)
                    @php
                        $monthName = \Carbon\Carbon::createFromDate($award->year, $award->month, 1)
                            ->locale('bg')
                            ->translatedFormat('F Y');
                    @endphp

                    <div
                        class="relative bg-white border border-gray-200 rounded-2xl shadow-xl p-6 text-center hover:shadow-2xl transition duration-300">
                        <div
                            class="absolute -top-4 left-1/2 -translate-x-1/2 bg-accent text-white px-4 py-1 rounded-full shadow text-sm font-semibold uppercase tracking-wide">
                            {{ $monthName }}
                        </div>

                        <img src="{{ $award->player->avatar_url ?? asset('images/default-player.png') }}" alt="{{ $award->player->name }}"
                            class="w-28 h-28 mx-auto mt-6 mb-4 rounded-full object-cover ring-4 ring-accent shadow" />

                        <h3 class="text-xl font-bold text-primary mb-1">{{ $award->player->name }}</h3>
                        <p class="text-sm text-gray-600 mb-2">
                            #{{ $award->player->number }} | {{ $award->player->position }}
                        </p>

                        <p class="text-lg text-green-600 font-bold">
                            ⭐ Средна оценка: {{ $award->average_rating }}
                        </p>
                    </div>
                @empty
                    <p class="text-center col-span-4 text-gray-500">Няма записани играчи на месеца за този сезон.</p>
                @endforelse
            </div>
        </div>
    </section>
</div>
