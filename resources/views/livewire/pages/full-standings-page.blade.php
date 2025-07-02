<section class="px-6 py-12 max-w-7xl mx-auto bg-card text-text">
    <h2 class="text-3xl text-primary font-extrabold uppercase mb-8 text-center">Пълно класиране</h2>

    <div class="overflow-x-auto bg-white rounded-xl shadow-lg">
        <table class="min-w-full text-sm text-left">
            <thead class="bg-accent text-cta uppercase tracking-wider">
                <tr>
                    <th class="px-4 py-3 cursor-pointer" wire:click="sortBy('manual_rank')">
                        #
                        @if ($sortColumn === 'manual_rank')
                            <i class="fas fa-sort-{{ $sortDirection === 'asc' ? 'up' : 'down' }} ml-1"></i>
                        @else
                            <i class="fas fa-sort ml-1 text-gray-400"></i>
                        @endif
                    </th>
                    <th class="px-4 py-3 cursor-pointer" wire:click="sortBy('team')">
                        Отбор
                        <i class="fas fa-sort ml-1 text-gray-400"></i>
                    </th>
                    <th class="px-4 py-3 cursor-pointer" wire:click="sortBy('played')">
                        И
                        @if ($sortColumn === 'played')
                            <i class="fas fa-sort-{{ $sortDirection === 'asc' ? 'up' : 'down' }} ml-1"></i>
                        @else
                            <i class="fas fa-sort ml-1 text-gray-400"></i>
                        @endif
                    </th>
                    <th class="px-4 py-3 cursor-pointer" wire:click="sortBy('wins')">
                        П
                        @if ($sortColumn === 'wins')
                            <i class="fas fa-sort-{{ $sortDirection === 'asc' ? 'up' : 'down' }} ml-1"></i>
                        @else
                            <i class="fas fa-sort ml-1 text-gray-400"></i>
                        @endif
                    </th>
                    <th class="px-4 py-3 cursor-pointer" wire:click="sortBy('draws')">
                        Р
                        @if ($sortColumn === 'draws')
                            <i class="fas fa-sort-{{ $sortDirection === 'asc' ? 'up' : 'down' }} ml-1"></i>
                        @else
                            <i class="fas fa-sort ml-1 text-gray-400"></i>
                        @endif
                    </th>
                    <th class="px-4 py-3 cursor-pointer" wire:click="sortBy('losses')">
                        З
                        @if ($sortColumn === 'losses')
                            <i class="fas fa-sort-{{ $sortDirection === 'asc' ? 'up' : 'down' }} ml-1"></i>
                        @else
                            <i class="fas fa-sort ml-1 text-gray-400"></i>
                        @endif
                    </th>
                    <th class="px-4 py-3">ГР</th>
                    <th class="px-4 py-3 cursor-pointer" wire:click="sortBy('points')">
                        Т
                        @if ($sortColumn === 'points')
                            <i class="fas fa-sort-{{ $sortDirection === 'asc' ? 'up' : 'down' }} ml-1"></i>
                        @else
                            <i class="fas fa-sort ml-1 text-gray-400"></i>
                        @endif
                    </th>
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
                            <td class="px-4 py-3">({{ $team->goals_scored - $team->goals_conceded }})</td>
                            <td class="px-4 py-3 font-bold text-accent">{{ $team->points }}</td>
                        </tr>
                    @endif
                @endforeach
            </tbody>
        </table>
    </div>
</section>
