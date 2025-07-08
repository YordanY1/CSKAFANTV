<section class="px-6 py-12 max-w-7xl mx-auto bg-card text-text">
    <h2 class="text-3xl text-primary font-extrabold uppercase mb-8 text-center">📊 Пълно класиране</h2>

    <!-- Table for Desktop -->
    <div class="overflow-x-auto bg-white rounded-xl shadow-lg hidden sm:block">
        <table class="min-w-full text-sm text-left">
            <thead class="bg-accent text-cta uppercase tracking-wider">
                <tr>
                    @foreach ([['manual_rank', '#'], ['team', 'Отбор'], ['played', 'М'], ['wins', 'П'], ['draws', 'Р'], ['losses', 'З'], ['goal_diff', 'ГР'], ['points', 'Т']] as [$column, $label])
                        <th class="px-4 py-3 {{ $column ? 'cursor-pointer' : '' }}"
                            @if ($column) wire:click="sortBy('{{ $column }}')" @endif>
                            {{ $label }}
                            @if ($sortColumn === $column)
                                <i class="fas fa-sort-{{ $sortDirection === 'asc' ? 'up' : 'down' }} ml-1"></i>
                            @elseif ($column)
                                <i class="fas fa-sort ml-1 text-gray-400"></i>
                            @endif
                        </th>
                    @endforeach
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-100">
                @foreach ($standings as $i => $team)
                    @if (str_contains(strtolower($team->team?->name), strtolower($search)))
                        <tr class="hover:bg-gray-100 transition">
                            <td class="px-4 py-3 font-semibold">{{ $team->manual_rank ?? $i + 1 }}</td>
                            <td class="px-4 py-3 font-bold text-primary flex items-center gap-2">
                                @if ($team->team?->logo)
                                    <img src="{{ asset('storage/' . $team->team->logo) }}" alt="{{ $team->team->name }}"
                                        class="w-6 h-6 rounded-full object-cover border border-gray-300" />
                                @endif
                                {{ $team->team->name ?? '—' }}
                            </td>
                            <td class="px-4 py-3">{{ $team->played }}</td>
                            <td class="px-4 py-3">{{ $team->wins }}</td>
                            <td class="px-4 py-3">{{ $team->draws }}</td>
                            <td class="px-4 py-3">{{ $team->losses }}</td>
                            <td class="px-4 py-3">
                                {{ $team->goals_scored }}:{{ $team->goals_conceded }}
                                ({{ $team->goals_scored - $team->goals_conceded }})
                            </td>

                            <td class="px-4 py-3 font-bold text-accent">{{ $team->calculated_points }}</td>
                        </tr>
                    @endif
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
                        <img src="{{ asset('storage/' . $standing->team->logo) }}" alt="{{ $standing->team->name }}"
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
                    <div class="col-span-2">
                        <span class="font-semibold text-gray-500">ГР:</span>
                        {{ $standing->goals_scored }}:{{ $standing->goals_conceded }}
                        ({{ $standing->goals_scored - $standing->goals_conceded }})
                    </div>

                    <div class="col-span-2 text-right">
                        <span class="font-semibold text-gray-500">Точки:</span>
                        <span class="text-accent font-bold text-sm">{{ $standing->calculated_points }}</span>
                    </div>
                </div>
            </div>
        @endforeach
    </div>

</section>
