<div id="contact-form" class="max-w-3xl mx-auto px-4 py-12 font-primary">
    <div class="flex justify-center mb-6">
        <img src="{{ asset('images/logo/logo.jpg') }}" alt="CSKA FAN TV logo" class="h-40">
    </div>

    <h1 class="text-4xl font-extrabold text-center text-red-700 mb-6 tracking-wide">
        Свържи се с нас
    </h1>

    @if (session()->has('success'))
        <div class="mb-6 p-4 bg-green-100 text-green-800 rounded shadow text-center font-medium">
            {{ session('success') }}
        </div>
    @endif

    @if (session()->has('error'))
        <div class="mb-6 p-4 bg-red-100 text-red-800 rounded shadow text-center font-medium">
            {{ session('error') }}
        </div>
    @endif

    <form wire:submit.prevent="submit"
        class="space-y-6 bg-white bg-opacity-90 p-8 rounded-2xl shadow-xl border border-red-200 backdrop-blur-md">

        <input type="hidden" id="recaptcha_response" name="g-recaptcha-response">

        <div>
            <label class="block font-semibold text-gray-700 mb-1">Име</label>
            <input type="text" wire:model.defer="name"
                class="w-full rounded-lg border border-gray-300 focus:border-red-500 focus:ring-red-500 px-4 py-3 shadow-sm transition" />
            @error('name')
                <span class="text-red-600 text-sm">{{ $message }}</span>
            @enderror
        </div>

        <div>
            <label class="block font-semibold text-gray-700 mb-1">Имейл</label>
            <input type="email" wire:model.defer="email"
                class="w-full rounded-lg border border-gray-300 focus:border-red-500 focus:ring-red-500 px-4 py-3 shadow-sm transition" />
            @error('email')
                <span class="text-red-600 text-sm">{{ $message }}</span>
            @enderror
        </div>

        <div>
            <label class="block font-semibold text-gray-700 mb-1">Съобщение</label>
            <textarea rows="5" wire:model.defer="message"
                class="w-full rounded-lg border border-gray-300 focus:border-red-500 focus:ring-red-500 px-4 py-3 shadow-sm transition resize-none"></textarea>
            @error('message')
                <span class="text-red-600 text-sm">{{ $message }}</span>
            @enderror
        </div>

        <div class="text-center">
            <div class="g-recaptcha inline-block" data-sitekey="{{ env('RECAPTCHA_SITE_KEY') }}"
                data-callback="onRecaptchaSuccess" data-error-callback="onRecaptchaError">
            </div>
        </div>

        <div class="text-center">
            <button type="submit"
                class="inline-flex items-center bg-red-600 text-white px-8 py-3 rounded-lg font-semibold hover:bg-red-700 transition duration-200 shadow-md cursor-pointer">
                <i class="fas fa-paper-plane mr-2"></i> Изпрати съобщение
            </button>
        </div>
    </form>
</div>
