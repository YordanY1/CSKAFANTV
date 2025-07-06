<section class="px-6 py-12 bg-card text-text animate-fade-in">
    <div class="max-w-5xl mx-auto">
        <h2 class="text-3xl text-primary font-extrabold uppercase mb-8 text-center tracking-wide">
            Класиране
        </h2>

        <div class="text-center mt-6 mb-5">
            <a href="{{ route('standings') }}" wire:navigate
                class="inline-block bg-accent text-white font-semibold px-6 py-2 rounded-lg shadow hover:bg-primary transition duration-200">
                📊 Пълно класиране
            </a>
        </div>

        <!-- Desktop -->
        <div class="overflow-x-auto bg-white rounded-xl shadow-lg hidden sm:block">
            <table class="min-w-full text-sm text-left">
                <thead class="bg-accent text-cta uppercase tracking-wider">
                    <tr>
                        <th class="px-4 py-3">#</th>
                        <th class="px-4 py-3">Отбор</th>
                        <th class="px-4 py-3">М</th>
                        <th class="px-4 py-3">П</th>
                        <th class="px-4 py-3">Р</th>
                        <th class="px-4 py-3">З</th>
                        <th class="px-4 py-3">ГР</th>
                        <th class="px-4 py-3">Т</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100">
                    @foreach ($standings as $standing)
                        <tr class="hover:bg-card transition">
                            <td class="px-4 py-3 font-semibold">{{ $loop->iteration }}</td>
                            <td class="px-4 py-3 font-bold text-primary flex items-center gap-2">
                                @if ($standing->team?->logo)
                                    <img src="{{ asset('storage/' . $standing->team->logo) }}"
                                        alt="{{ $standing->team->name }}"
                                        class="w-6 h-6 rounded-full object-cover border border-gray-300" />
                                @endif
                                {{ $standing->team?->name ?? '—' }}
                            </td>
                            <td class="px-4 py-3">{{ $standing->played }}</td>
                            <td class="px-4 py-3">{{ $standing->wins }}</td>
                            <td class="px-4 py-3">{{ $standing->draws }}</td>
                            <td class="px-4 py-3">{{ $standing->losses }}</td>
                            <td class="px-4 py-3">{{ $standing->goals_scored }}:{{ $standing->goals_conceded }}</td>
                            <td class="px-4 py-3 font-bold text-accent">{{ $standing->calculated_points }}</td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>

        <!-- Mobile Card View -->
        <div class="sm:hidden space-y-4 mt-6">
            @foreach ($standings as $standing)
                <div class="bg-white border border-gray-200 rounded-2xl shadow-md p-4 relative overflow-hidden">

                    <div class="absolute top-2 left-2 bg-accent text-white text-xs px-2 py-0.5 rounded-full shadow">
                        #{{ $loop->iteration }}
                    </div>

                    <div class="flex items-center gap-3 mb-3">
                        @if ($standing->team?->logo)
                            <img src="{{ asset('storage/' . $standing->team->logo) }}"
                                alt="{{ $standing->team->name }}"
                                class="w-10 h-10 rounded-full object-cover ring-2 ring-accent" />
                        @endif
                        <div>
                            <div class="font-bold text-primary text-base">{{ $standing->team?->name ?? '—' }}</div>
                        </div>
                    </div>

                    <div class="grid grid-cols-4 text-xs text-gray-700 gap-y-1">
                        <div><span class="font-semibold text-gray-500">М:</span> {{ $standing->played }}</div>
                        <div><span class="font-semibold text-gray-500">П:</span> {{ $standing->wins }}</div>
                        <div><span class="font-semibold text-gray-500">Р:</span> {{ $standing->draws }}</div>
                        <div><span class="font-semibold text-gray-500">З:</span> {{ $standing->losses }}</div>
                        <div class="col-span-2"><span class="font-semibold text-gray-500">ГР:</span>
                            {{ $standing->goal_difference }}</div>
                        <div class="col-span-2 text-right">
                            <span class="font-semibold text-gray-500">Точки:</span>
                            <span class="text-accent font-bold text-sm">{{ $standing->calculated_points }}</span>
                        </div>
                    </div>
                </div>
            @endforeach
        </div>

    </div>
</section>
