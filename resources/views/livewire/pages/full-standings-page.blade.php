<section class="px-6 py-12 max-w-7xl mx-auto bg-card text-text">
    <h2 class="text-3xl text-primary font-extrabold uppercase mb-8 text-center">📊 Пълно класиране</h2>

    <div class="overflow-x-auto bg-white rounded-xl shadow-lg hidden sm:block">
        <table class="min-w-full text-sm text-left">
            <thead class="bg-accent text-cta uppercase tracking-wider">
                <tr>
                    @foreach ([['rank', '#'], ['bg_name', 'Отбор'], ['matches', 'М'], ['won', 'П'], ['drawn', 'Р'], ['lost', 'З'], ['goal_diff', 'ГР'], ['points', 'Т'], ['form', 'Форма']] as [$column, $label])
                        <th class="px-4 py-3 cursor-pointer" wire:click="sortBy('{{ $column }}')">
                            {{ $label }}
                            @if ($sortColumn === $column)
                                <i class="fas fa-sort-{{ $sortDirection === 'asc' ? 'up' : 'down' }} ml-1"></i>
                            @else
                                <i class="fas fa-sort ml-1 text-gray-400"></i>
                            @endif
                        </th>
                    @endforeach
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-100">
                @foreach ($standings as $i => $team)
                    <tr class="hover:bg-gray-100 transition">
                        <td class="px-4 py-3 font-semibold">{{ $team['rank'] ?? $i + 1 }}</td>
                        
                        <td
                            class="px-4 py-3 font-bold flex items-center gap-2 {{ $team['is_cska'] ? 'text-red-600' : 'text-black' }}">
                            @if (!empty($team['logo']))
                                <img src="{{ $team['logo'] }}" alt="{{ $team['bg_name'] ?? $team['name'] }}"
                                    class="w-6 h-6 rounded-full object-cover border border-gray-300" />
                            @endif
                            {{ $team['bg_name'] ?? ($team['name'] ?? '—') }}
                        </td>

                        <td class="px-4 py-3">{{ $team['matches'] ?? '-' }}</td>
                        <td class="px-4 py-3">{{ $team['won'] ?? '-' }}</td>
                        <td class="px-4 py-3">{{ $team['drawn'] ?? '-' }}</td>
                        <td class="px-4 py-3">{{ $team['lost'] ?? '-' }}</td>
                        <td class="px-4 py-3">
                            {{ $team['goals_scored'] ?? 0 }}:{{ $team['goals_conceded'] ?? 0 }}
                            ({{ $team['goal_diff'] ?? 0 }})
                        </td>
                        <td class="px-4 py-3 font-bold text-accent">{{ $team['points'] ?? 0 }}</td>

                        <td class="px-4 py-3">
                            <div class="flex gap-1">
                                @foreach ($team['form'] ?? [] as $result)
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

                <!-- Rank -->
                <div class="absolute top-2 left-2 bg-accent text-white text-xs px-2 py-0.5 rounded-full shadow">
                    #{{ $standing['rank'] ?? $i + 1 }}
                </div>

                <!-- Team -->
                <div class="flex items-center gap-3 mb-3">
                    @if (!empty($standing['logo']))
                        <img src="{{ $standing['logo'] }}" alt="{{ $standing['bg_name'] ?? $standing['name'] }}"
                            class="w-10 h-10 rounded-full object-cover ring-2 ring-accent" />
                    @endif
                    <div class="font-bold text-base {{ $standing['is_cska'] ? 'text-red-600' : 'text-black' }}">
                        {{ $standing['bg_name'] ?? ($standing['name'] ?? '—') }}
                    </div>
                </div>

                <!-- Stats -->
                <div class="grid grid-cols-4 text-xs text-gray-700 gap-y-1 mb-3">
                    <div><span class="font-semibold text-gray-500">М:</span> {{ $standing['matches'] ?? '-' }}</div>
                    <div><span class="font-semibold text-gray-500">П:</span> {{ $standing['won'] ?? '-' }}</div>
                    <div><span class="font-semibold text-gray-500">Р:</span> {{ $standing['drawn'] ?? '-' }}</div>
                    <div><span class="font-semibold text-gray-500">З:</span> {{ $standing['lost'] ?? '-' }}</div>

                    <div class="col-span-2">
                        <span class="font-semibold text-gray-500">ГР:</span>
                        {{ $standing['goals_scored'] ?? 0 }}:{{ $standing['goals_conceded'] ?? 0 }}
                        ({{ $standing['goal_diff'] ?? 0 }})
                    </div>

                    <div class="col-span-2 text-right">
                        <span class="font-semibold text-gray-500">Точки:</span>
                        <span class="text-accent font-bold text-sm">{{ $standing['points'] ?? 0 }}</span>
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

</section>
