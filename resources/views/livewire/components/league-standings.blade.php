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
                        <th class="px-4 py-3">Форма</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100">
                    @foreach ($standings as $i => $standing)
                        <tr class="hover:bg-card transition">
                            <td class="px-4 py-3 font-semibold">{{ $i + 1 }}</td>
                            <td
                                class="px-4 py-3 font-bold flex items-center gap-2 {{ $standing['is_cska'] ? 'text-red-600' : 'text-black' }}">
                                @if (!empty($standing['logo']))
                                    <img src="{{ $standing['logo'] }}" alt="{{ $standing['name'] }}"
                                        class="w-6 h-6 rounded-full object-cover border border-gray-300" />
                                @endif
                                {{ $standing['name'] ?? '—' }}
                            </td>
                            <td class="px-4 py-3">{{ $standing['matches'] }}</td>
                            <td class="px-4 py-3">{{ $standing['won'] }}</td>
                            <td class="px-4 py-3">{{ $standing['drawn'] }}</td>
                            <td class="px-4 py-3">{{ $standing['lost'] }}</td>
                            <td class="px-4 py-3">
                                {{ $standing['goals_scored'] }}:{{ $standing['goals_conceded'] }}
                                ({{ $standing['goal_diff'] }})
                            </td>
                            <td class="px-4 py-3 font-bold text-accent">{{ $standing['points'] }}</td>
                            <td class="px-4 py-3">
                                <div class="flex gap-1">
                                    @foreach ($standing['form'] ?? [] as $result)
                                        <span
                                            class="w-5 h-5 flex items-center justify-center text-xs font-bold rounded-full
                                            @if ($result === 'W') bg-green-500 text-white
                                            @elseif ($result === 'D') bg-gray-400 text-white
                                            @elseif ($result === 'L') bg-red-500 text-white
                                            @else bg-gray-200 text-black @endif">
                                            {{ $result }}
                                        </span>
                                    @endforeach
                                </div>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>

        <!-- Mobile Card View -->
        <div class="sm:hidden space-y-4 mt-6">
            @foreach ($standings as $i => $standing)
                <div class="bg-white border border-gray-200 rounded-2xl shadow-md p-4 relative overflow-hidden">

                    <div class="absolute top-2 left-2 bg-accent text-white text-xs px-2 py-0.5 rounded-full shadow">
                        #{{ $i + 1 }}
                    </div>

                    <div class="flex items-center gap-3 mb-3">
                        @if (!empty($standing['logo']))
                            <img src="{{ $standing['logo'] }}" alt="{{ $standing['name'] }}"
                                class="w-10 h-10 rounded-full object-cover ring-2 ring-accent" />
                        @endif
                        <div class="font-bold text-base {{ $standing['is_cska'] ? 'text-red-600' : 'text-black' }}">
                            {{ $standing['name'] ?? '—' }}
                        </div>
                    </div>

                    <div class="grid grid-cols-4 text-xs text-gray-700 gap-y-1 mb-3">
                        <div><span class="font-semibold text-gray-500">М:</span> {{ $standing['matches'] }}</div>
                        <div><span class="font-semibold text-gray-500">П:</span> {{ $standing['won'] }}</div>
                        <div><span class="font-semibold text-gray-500">Р:</span> {{ $standing['drawn'] }}</div>
                        <div><span class="font-semibold text-gray-500">З:</span> {{ $standing['lost'] }}</div>
                        <div class="col-span-2">
                            <span class="font-semibold text-gray-500">ГР:</span>
                            {{ $standing['goals_scored'] }}:{{ $standing['goals_conceded'] }}
                            ({{ $standing['goal_diff'] }})
                        </div>
                        <div class="col-span-2 text-right">
                            <span class="font-semibold text-gray-500">Точки:</span>
                            <span class="text-accent font-bold text-sm">{{ $standing['points'] }}</span>
                        </div>
                    </div>

                    <div class="flex gap-1">
                        @foreach ($standing['form'] ?? [] as $result)
                            <span
                                class="w-6 h-6 flex items-center justify-center text-xs font-bold rounded-full
                                @if ($result === 'W') bg-green-500 text-white
                                @elseif ($result === 'D') bg-gray-400 text-white
                                @elseif ($result === 'L') bg-red-500 text-white
                                @else bg-gray-200 text-black @endif">
                                {{ $result }}
                            </span>
                        @endforeach
                    </div>
                </div>
            @endforeach
        </div>
    </div>
</section>
