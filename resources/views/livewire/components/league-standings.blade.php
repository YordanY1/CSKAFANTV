<section class="px-6 py-12 bg-card text-text animate-fade-in">
    <div class="max-w-5xl mx-auto">
        <h2 class="text-3xl text-primary font-extrabold uppercase mb-8 text-center tracking-wide">
            Класиране
        </h2>

        <div class="overflow-x-auto bg-white rounded-xl shadow-lg">
            <table class="min-w-full text-sm text-left">
                <thead class="bg-accent text-cta uppercase tracking-wider">
                    <tr>
                        <th class="px-4 py-3">#</th>
                        <th class="px-4 py-3">Отбор</th>
                        <th class="px-4 py-3">И</th>
                        <th class="px-4 py-3">П</th>
                        <th class="px-4 py-3">Р</th>
                        <th class="px-4 py-3">З</th>
                        <th class="px-4 py-3">В:Д</th>
                        <th class="px-4 py-3">ГР</th>
                        <th class="px-4 py-3">Т</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100">
                    @foreach ($standings as $i => $standing)
                        <tr class="hover:bg-card transition">
                            <td class="px-4 py-3 font-semibold">
                                {{ $standing->manual_rank ?? $i + 1 }}
                            </td>
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
                            <td class="px-4 py-3">
                                {{ $standing->goals_scored }}:{{ $standing->goals_conceded }}
                            </td>
                            <td class="px-4 py-3">
                                {{ $standing->goals_scored - $standing->goals_conceded }}
                            </td>
                            <td class="px-4 py-3 font-bold text-accent">{{ $standing->points }}</td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
</section>
