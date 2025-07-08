<div
    class="flex flex-col items-center justify-center min-h-[60vh] space-y-6 bg-white dark:bg-gray-900 rounded-lg p-6 shadow">

    <h2 class="text-3xl font-bold text-gray-800 dark:text-white tracking-wide">🎁 Теглене на победител</h2>

    <div wire:loading.remove>
        <button wire:click="draw"
            class="px-8 py-4 text-xl font-semibold  dark:text-white bg-gradient-to-r from-rose-600 to-pink-500 hover:from-rose-700 hover:to-pink-600
           dark:from-rose-500 dark:to-pink-400 dark:hover:from-rose-600 dark:hover:to-pink-500
           transition-all duration-300 rounded-full shadow-lg hover:scale-105">
            🎉 Натисни и виж кой печели!
        </button>

    </div>

    <div wire:loading class="flex flex-col items-center space-y-4 animate-pulse text-gray-800 dark:text-gray-200">
        <svg class="w-12 h-12 text-pink-500 animate-spin" fill="none" stroke="currentColor" stroke-width="4"
            viewBox="0 0 24 24">
            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" />
            <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8v8H4z" />
        </svg>
        <p class="text-lg font-medium">Въртим голямата томбола... 🎲</p>
    </div>

    @if ($lastWinner)
        <div
            class="mt-6 bg-green-50 dark:bg-green-900 border border-green-400 dark:border-green-600 text-green-800 dark:text-green-200 p-6 rounded-lg shadow-md w-full max-w-md text-center">
            <h3 class="text-2xl font-bold">🏆 Победител:</h3>
            <p class="text-xl mt-2 font-semibold">{{ $lastWinner->name }}</p>
            <p class="text-sm text-gray-700 dark:text-gray-300">{{ $lastWinner->email }}</p>
        </div>
    @endif

</div>
