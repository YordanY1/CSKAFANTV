<section class="bg-white py-12 px-6 rounded-xl shadow-md">
    <h2 class="text-3xl font-bold text-center text-primary mb-8">Картони на играчи</h2>
    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">

        <div class="bg-red-50 border-l-4 border-red-400 p-6 rounded-lg">
            <h3 class="text-xl font-semibold text-red-700 mb-4">⚠️ Най-много картони</h3>
            <ul class="space-y-3">
                @foreach ($mostCards as $card)
                    <li class="flex items-center justify-between">
                        <span class="text-gray-800 font-medium">
                            {{ $card->player->name }}
                        </span>
                        <span class="text-sm font-bold text-red-600">
                            ЖК: {{ $card->yellow_cards }} /
                            ЧК: {{ $card->red_cards }} /
                            2xЖК: {{ $card->second_yellow_reds }}
                        </span>
                    </li>
                @endforeach
            </ul>
        </div>

        <div class="bg-green-50 border-l-4 border-green-400 p-6 rounded-lg">
            <h3 class="text-xl font-semibold text-green-700 mb-4">✅ Най-дисциплинирани</h3>
            <ul class="space-y-3">
                @foreach ($leastCards as $card)
                    <li class="flex items-center justify-between">
                        <span class="text-gray-800 font-medium">
                            {{ $card->player->name }}
                        </span>
                        <span class="text-sm font-bold text-green-600">
                            ЖК: {{ $card->yellow_cards }} /
                            ЧК: {{ $card->red_cards }} /
                            2xЖК: {{ $card->second_yellow_reds }}
                        </span>
                    </li>
                @endforeach
            </ul>
        </div>
    </div>
</section>
