<div>
    @include('livewire.pages.archive._nav', ['active' => 'archive.prediction-rankings'])

    <section class="px-6 py-10 max-w-6xl mx-auto bg-card text-text">
        <h2 class="text-3xl text-primary font-extrabold uppercase mb-8 text-center">
            🏆 Класиране по прогнози · {{ $season }}
        </h2>

        <div class="overflow-x-auto bg-white rounded-xl shadow-lg">
            <table class="min-w-full text-sm text-left">
                <thead class="bg-accent text-cta uppercase tracking-wider">
                    <tr>
                        <th class="px-4 py-3">#</th>
                        <th class="px-4 py-3">Потребител</th>
                        <th class="px-4 py-3">Опити</th>
                        <th class="px-4 py-3">Точки</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100">
                    @forelse ($rankings as $i => $entry)
                        <tr class="hover:bg-gray-100 transition">
                            <td class="px-4 py-3 font-semibold">{{ $i + 1 }}</td>
                            <td class="px-4 py-3 font-bold text-primary">{{ $entry->name }}</td>
                            <td class="px-4 py-3">{{ $entry->attempts }}</td>
                            <td class="px-4 py-3 text-accent font-bold">{{ $entry->total_points }}</td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="4" class="px-4 py-6 text-center text-gray-500">
                                Няма класиране по прогнози за този сезон.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </section>
</div>
