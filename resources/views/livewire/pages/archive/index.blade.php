<div>
    <section class="px-6 py-12 bg-card text-text animate-fade-in">
        <div class="max-w-7xl mx-auto">
            <h2 class="text-3xl md:text-4xl text-primary font-extrabold uppercase mb-3 text-center tracking-wide">
                📚 Архив
            </h2>
            <p class="text-center text-gray-500 mb-10 max-w-2xl mx-auto">
                Прегледай изиграните мачове, оценките на играчите, Залата на славата и класирането по прогнози по сезони.
            </p>

            @php
                $categories = [
                    ['route' => 'archive.matches', 'label' => 'Мачове', 'icon' => '⚽', 'desc' => 'Всички изиграни мачове с резултати'],
                    ['route' => 'archive.player-ratings', 'label' => 'Оценки на играчи', 'icon' => '📝', 'desc' => 'Средни оценки от феновете'],
                    ['route' => 'archive.hall-of-fame', 'label' => 'Зала на славата', 'icon' => '🏅', 'desc' => 'Играчите на месеца'],
                    ['route' => 'archive.prediction-rankings', 'label' => 'Класиране по прогнози', 'icon' => '🏆', 'desc' => 'Точки от прогнозите на потребителите'],
                ];
            @endphp

            @forelse ($seasons as $season)
                <div class="mb-12">
                    <h3 class="text-2xl font-bold text-accent mb-5 flex items-center gap-2">
                        <span class="inline-block w-2 h-7 bg-accent rounded"></span>
                        {{ \App\Support\Season::label($season) }}
                    </h3>

                    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-5">
                        @foreach ($categories as $category)
                            <a href="{{ route($category['route'], $season) }}" wire:navigate
                                class="group bg-white rounded-xl shadow-lg p-6 text-center hover:shadow-2xl hover:-translate-y-1 transition duration-200">
                                <div class="text-4xl mb-3">{{ $category['icon'] }}</div>
                                <h4 class="text-lg font-bold text-primary group-hover:text-accent transition">
                                    {{ $category['label'] }}
                                </h4>
                                <p class="text-sm text-gray-500 mt-2">{{ $category['desc'] }}</p>
                            </a>
                        @endforeach
                    </div>
                </div>
            @empty
                <p class="text-center text-gray-500">Все още няма архивирани сезони.</p>
            @endforelse
        </div>
    </section>
</div>
