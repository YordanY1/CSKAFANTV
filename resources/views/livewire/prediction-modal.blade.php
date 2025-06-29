<div>
    @if ($isOpen)
        <div x-data="{ showSuccess: false }" x-init="@if (session('success')) showSuccess = true;
             setTimeout(() => {
                 showSuccess = false;
                 $wire.set('isOpen', false);
             }, 3000); @endif"
            class="fixed inset-0 bg-black/70 backdrop-blur-sm z-50 flex items-center justify-center px-4">

            <div
                class="relative bg-card text-text rounded-2xl shadow-xl w-full max-w-md p-6 space-y-6 border border-red-200 animate-scale-fade-in font-primary">

                <h2 class="text-2xl font-bold text-center text-primary">Направи прогноза</h2>

                <form wire:submit.prevent="save" class="space-y-4">
                    <div>
                        <label for="homeScore" class="block text-sm font-medium mb-1 text-primary">
                            Резултат за домакина:
                        </label>
                        <input type="number" id="homeScore" wire:model="homeScore" min="0" max="20"
                            class="w-full rounded-lg border border-red-200 bg-white px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-accent focus:border-accent" />
                        @error('homeScore')
                            <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label for="awayScore" class="block text-sm font-medium mb-1 text-primary">
                            Резултат за госта:
                        </label>
                        <input type="number" id="awayScore" wire:model="awayScore" min="0" max="20"
                            class="w-full rounded-lg border border-red-200 bg-white px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-accent focus:border-accent" />
                        @error('awayScore')
                            <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label class="block text-sm font-medium mb-1 text-primary">
                            Прогнозиран знак:
                        </label>
                        <div class="flex gap-3 flex-wrap">
                            <label class="flex items-center gap-1">
                                <input type="radio" wire:model="resultSign" value=""
                                    class="text-primary focus:ring-accent" />
                                <span>Без знак</span>
                            </label>
                            <label class="flex items-center gap-1">
                                <input type="radio" wire:model="resultSign" value="1"
                                    class="text-primary focus:ring-accent" />
                                <span>1</span>
                            </label>
                            <label class="flex items-center gap-1">
                                <input type="radio" wire:model="resultSign" value="X"
                                    class="text-primary focus:ring-accent" />
                                <span>Х</span>
                            </label>
                            <label class="flex items-center gap-1">
                                <input type="radio" wire:model="resultSign" value="2"
                                    class="text-primary focus:ring-accent" />
                                <span>2</span>
                            </label>
                        </div>
                        @error('resultSign')
                            <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                        @enderror
                        @error('empty')
                            <p class="text-red-500 text-sm mt-2">{{ $message }}</p>
                        @enderror
                    </div>

                    <div class="flex justify-end gap-3 pt-4">
                        <button type="button" wire:click="$set('isOpen', false)"
                            class="px-4 py-2 text-sm border border-red-200 text-text rounded-lg hover:bg-accent-2 transition cursor-pointer">
                            Отказ
                        </button>
                        <button type="submit"
                            class="px-5 py-2.5 text-sm bg-accent text-cta rounded-lg hover:bg-primary transition shadow-sm cursor-pointer">
                            Запази
                        </button>
                    </div>
                </form>

                <div x-show="showSuccess" x-transition
                    class="mt-6 bg-accent text-cta text-sm px-6 py-3 rounded-xl shadow-2xl flex items-center gap-3 border border-red-300 ring-1 ring-red-400/50 max-w-full">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 flex-shrink-0 text-cta" fill="none"
                        viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7" />
                    </svg>
                    <span class="font-medium whitespace-nowrap truncate">
                        {{ session('success') }}
                    </span>
                </div>

            </div>
        </div>
    @endif
</div>
