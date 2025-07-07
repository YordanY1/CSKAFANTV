<section class="px-6 py-12 bg-card text-text animate-fade-in">
    <div class="max-w-6xl mx-auto">
        <h2 class="text-3xl text-primary font-extrabold uppercase mb-8 text-center tracking-wide">
            📝 Оценки на играчите
        </h2>

        <div class="overflow-x-auto bg-white rounded-xl shadow-lg">
            <table class="min-w-full text-sm text-left">
                <thead class="bg-accent text-cta uppercase tracking-wider">
                    <tr>
                        <th class="px-4 py-3">#</th>
                        <th class="px-4 py-3">Играч</th>
                        <th class="px-4 py-3">Средна оценка за сезона</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100">
                    @foreach ($ratings as $index => $data)
                        <tr class="hover:bg-card transition">
                            <td class="px-4 py-3 font-semibold">
                                {{ $index + 1 }}
                            </td>
                            <td class="px-4 py-3 font-bold text-primary flex items-center gap-2">
                                @if ($data['player']?->image_path)
                                    <img src="{{ asset('storage/' . $data['player']->image_path) }}"
                                        alt="{{ $data['player']->name }}"
                                        class="w-10 h-10 rounded-full object-cover border border-gray-300" />
                                @endif
                                {{ $data['player']->name }}
                            </td>
                            <td class="px-4 py-3 text-accent font-semibold">
                                {{ $data['avg_rating'] }}
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
</section>
