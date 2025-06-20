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
                        <th class="px-4 py-3">Изиграни</th>
                        <th class="px-4 py-3">Победи</th>
                        <th class="px-4 py-3">Равни</th>
                        <th class="px-4 py-3">Загуби</th>
                        <th class="px-4 py-3">Точки</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100">
                    @foreach ([['name' => 'ЦСКА', 'played' => 18, 'win' => 14, 'draw' => 3, 'loss' => 1, 'points' => 45], ['name' => 'Левски', 'played' => 18, 'win' => 13, 'draw' => 2, 'loss' => 3, 'points' => 41], ['name' => 'Берое', 'played' => 18, 'win' => 10, 'draw' => 5, 'loss' => 3, 'points' => 35]] as $i => $team)
                        <tr class="hover:bg-card transition">
                            <td class="px-4 py-3 font-semibold">{{ $i + 1 }}</td>
                            <td class="px-4 py-3 font-bold text-primary">{{ $team['name'] }}</td>
                            <td class="px-4 py-3">{{ $team['played'] }}</td>
                            <td class="px-4 py-3">{{ $team['win'] }}</td>
                            <td class="px-4 py-3">{{ $team['draw'] }}</td>
                            <td class="px-4 py-3">{{ $team['loss'] }}</td>
                            <td class="px-4 py-3 font-bold text-accent">{{ $team['points'] }}</td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
</section>
