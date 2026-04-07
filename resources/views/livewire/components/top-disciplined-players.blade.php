<section class="bg-white py-12 px-6 rounded-xl shadow-md">
    <h2 class="text-3xl text-primary font-extrabold uppercase mb-8 text-center tracking-wide">
        Картони на играчи
    </h2>

    <div class="mt-10 mb-10 text-center">
        <a href="/cards" wire:navigate
            class="inline-block bg-accent text-white font-semibold px-6 py-2 rounded-lg shadow hover:bg-red-700 transition duration-200">
            📋 Виж всички картони
        </a>
    </div>

    <div class="bg-red-50 border-l-4 border-red-400 p-6 rounded-lg max-w-2xl mx-auto">
        <h3 class="text-xl font-semibold text-red-700 mb-4">⚠️ Най-много картони</h3>

        <ul class="space-y-3 mb-6">
            @foreach ($mostCards as $card)
                @if (!empty($card->player?->name))
                    <li>
                        <div class="flex items-center justify-between">
                            <span class="text-gray-800 font-medium">
                                {{ $card->player->name }}
                                @if ($card->has_direct_red)
                                    <span class="text-red-600" title="Директен червен картон">★</span>
                                @endif
                            </span>
                            <span class="text-sm font-bold">
                                <span class="text-yellow-500">🟨 {{ $card->yellow_cards }}</span> /
                                <span class="text-red-600">🟥 {{ $card->total_reds }}</span>
                            </span>
                        </div>
                        @if ($card->has_direct_red)
                            <div class="text-xs text-red-600 mt-1 leading-snug italic">{{ $card->direct_red_note }}</div>
                        @endif
                    </li>
                @endif
            @endforeach
        </ul>

        <div class="text-xs text-gray-600 border-t pt-4 mt-6">
            <div class="flex items-center justify-between">
                <div><span class="text-yellow-500">🟨</span> Жълт картон</div>
                <div><span class="text-red-600">🟥</span> Червен картон</div>
                <div><span class="text-red-600">★</span> Директен червен</div>
            </div>
        </div>
    </div>
</section>
