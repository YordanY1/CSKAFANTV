<div>
    @if ($isOpen)
        <div x-data="{ showSuccess: false }" x-init="@if (session('success')) showSuccess = true;
            setTimeout(() => {
                showSuccess = false;
                $wire.set('isOpen', false);
            }, 3000); @endif"
            class="fixed inset-0 z-50 bg-black/70 backdrop-blur-sm flex items-center justify-center px-4">

            <div
                class="relative bg-white text-gray-800 rounded-3xl shadow-2xl w-full max-w-lg p-8 space-y-6 border border-gray-200 animate-scale-fade-in">

                <h2 class="text-3xl font-extrabold text-center text-primary uppercase tracking-wide">Прогноза за мач</h2>

                <form wire:submit.prevent="save" class="space-y-6">
                    <div class="flex items-center justify-between gap-6">
                        @if ($match?->homeTeam)
                            <div class="flex items-center gap-3 flex-1">
                                <img src="{{ $match->homeTeam->logo_url }}" alt="{{ $match->homeTeam->name }}"
                                    class="w-12 h-12 rounded-full shadow ring-2 ring-accent">
                                <span class="text-base font-semibold text-primary">{{ $match->homeTeam->name }}</span>
                            </div>
                        @endif

                        <select wire:model="homeScore"
                            class="w-20 rounded-xl border border-gray-300 bg-white px-2 py-2 text-center text-base font-bold text-gray-700 focus:outline-none focus:ring-2 focus:ring-accent">
                            <option value="" disabled>–</option>
                            @for ($i = 0; $i <= 20; $i++)
                                <option value="{{ $i }}">{{ $i }}</option>
                            @endfor

                        </select>
                    </div>

                    <div class="flex items-center justify-between gap-6">
                        @if ($match?->awayTeam)
                            <div class="flex items-center gap-3 flex-1">
                                <img src="{{ $match->awayTeam->logo_url }}" alt="{{ $match->awayTeam->name }}"
                                    class="w-12 h-12 rounded-full shadow ring-2 ring-accent">
                                <span class="text-base font-semibold text-primary">{{ $match->awayTeam->name }}</span>
                            </div>
                        @endif

                        <select wire:model="awayScore"
                            class="w-20 rounded-xl border border-gray-300 bg-white px-2 py-2 text-center text-base font-bold text-gray-700 focus:outline-none focus:ring-2 focus:ring-accent">
                            <option value="" disabled>–</option>
                            @for ($i = 0; $i <= 20; $i++)
                                <option value="{{ $i }}">{{ $i }}</option>
                            @endfor

                        </select>
                    </div>

                    @error('empty')
                        <p class="text-red-500 text-sm mt-1 text-center">{{ $message }}</p>
                    @enderror

                    <div class="flex justify-end gap-4 pt-4">
                        <button type="button" wire:click="$set('isOpen', false)"
                            class="px-5 py-2.5 text-sm bg-gray-200 text-gray-700 rounded-lg hover:bg-gray-300 transition">
                            ❌ Отказ
                        </button>
                        <button type="submit"
                            class="px-6 py-2.5 text-sm bg-primary text-white rounded-lg hover:bg-accent transition shadow-md font-semibold">
                            💾 Запази прогноза
                        </button>
                    </div>
                </form>

                <div x-show="showSuccess" x-transition
                    class="fixed bottom-6 left-1/2 transform -translate-x-1/2 bg-green-500 text-white text-sm px-6 py-3 rounded-full shadow-lg ring-1 ring-green-700/50 flex items-center gap-2">
                    <i class="fas fa-check-circle"></i>
                    <span>{{ session('success') }}</span>
                </div>
            </div>
        </div>
    @endif
</div>
