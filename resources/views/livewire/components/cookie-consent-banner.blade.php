<div x-data="{
    show: localStorage.getItem('cookie_consent') === null,
    accept() {
        localStorage.setItem('cookie_consent', 'accepted');
        this.show = false;
    },
    reject() {
        localStorage.setItem('cookie_consent', 'rejected');
        this.show = false;
    }
}" x-show="show" x-transition x-transition x-cloak
    class="fixed bottom-4 left-4 right-4 max-w-xl mx-auto bg-white text-black border border-gray-300 shadow-lg rounded-lg p-4 z-50">

    <div class="text-sm leading-relaxed">
        <p>
            Ние използваме бисквитки, за да подобрим вашето преживяване.
            Можете да научите повече в нашите
            <a href="{{ route('cookie-policy') }}" wire:navigate
                class="text-red-600 underline hover:text-red-800">Политика за бисквитки</a>
            и
            <a href="{{ route('privacy-policy') }}" wire:navigate
                class="text-red-600 underline hover:text-red-800">Политика за поверителност</a>.
        </p>
    </div>

    <div class="mt-4 flex justify-end gap-2">
        <button @click="reject"
            class="px-4 py-2 bg-gray-200 text-gray-700 rounded hover:bg-gray-300 transition cursor-pointer">
            Отказвам
        </button>

        <button @click="accept"
            class="px-4 py-2 bg-red-600 text-white rounded hover:bg-red-700 transition cursor-pointer">
            Приемам
        </button>
    </div>
</div>
