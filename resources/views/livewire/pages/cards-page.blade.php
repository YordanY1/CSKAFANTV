<section class="bg-white py-12 px-6 rounded-xl shadow-md max-w-5xl mx-auto">
    <h2 class="text-3xl font-bold text-center text-primary mb-8">📋 Всички картони по играчи</h2>

    <table class="min-w-full bg-white border border-gray-300 rounded-lg overflow-hidden">
        <thead class="bg-gray-100 text-left">
            <tr>
                <th class="px-4 py-3">Играч</th>
                <th class="px-4 py-3 text-yellow-700">🟨 ЖК</th>
                <th class="px-4 py-3 text-rose-800">🟥 2xЖК</th>
                <th class="px-4 py-3 text-red-700">⬛ ЧК</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($cards as $card)
                <tr class="border-t hover:bg-gray-50">
                    <td class="px-4 py-3 font-medium text-gray-800">
                        {{ $card->player->name }}
                    </td>
                    <td class="px-4 py-3 font-bold text-yellow-600">
                        {{ $card->yellow_cards }}
                    </td>
                    <td class="px-4 py-3 font-bold text-rose-800">
                        {{ $card->second_yellow_reds }}
                    </td>
                    <td class="px-4 py-3 font-bold text-red-600">
                        {{ $card->red_cards }}
                    </td>
                </tr>
            @endforeach
        </tbody>
    </table>

    <div class="mt-6 border-t pt-4 text-sm text-gray-600 max-w-md mx-auto">
        <div class="flex justify-between items-center">
            <div><span class="text-yellow-500">🟨</span> Жълт картон</div>
            <div><span class="text-rose-800">🟥</span> Втори жълт → червен</div>
            <div><span class="text-red-600">⬛</span> Директен червен картон</div>
        </div>
    </div>
</section>
