@props([
    'url',
    'label' => 'Вход с Google',
])

{{--
    Wraps the Google OAuth link with an in-app browser guard. Inside Facebook,
    Instagram, TikTok and similar embedded WebViews, Google blocks OAuth with
    `Error 403: disallowed_useragent`. When such a browser is detected, the
    click opens a modal that redirects the user to Chrome (Android) or Safari
    (iOS) so the sign-in flow can complete.
--}}
<div x-data="inAppBrowserGuard(@js($url))" class="w-full">
    <a href="{{ $url }}" @click="handleClick($event)"
        class="inline-flex items-center justify-center w-full gap-2 py-3 px-6 bg-red-600 hover:bg-red-700 text-white font-semibold rounded-lg transition shadow-sm cursor-pointer">
        <i class="fab fa-google text-lg"></i>
        {{ $label }}
    </a>

    <div x-cloak x-show="showModal" x-transition
        class="fixed inset-0 bg-black/70 backdrop-blur-sm z-[60] flex items-center justify-center px-4">
        <div @click.away="showModal = false"
            class="bg-white rounded-xl shadow-2xl max-w-md w-full p-6 sm:p-8 relative text-center text-text">

            <button type="button" @click="showModal = false"
                class="absolute top-3 right-3 text-gray-400 hover:text-red-500 transition">
                <i class="fas fa-times"></i>
            </button>

            <div class="text-4xl text-red-600 mb-3">
                <i class="fas fa-shield-halved"></i>
            </div>
            <h3 class="text-xl font-bold text-gray-800 mb-2">
                Отвори в истински браузър
            </h3>
            <p class="text-sm text-gray-600 mb-5 leading-relaxed">
                Влизаш през вграден браузър (Facebook, Instagram, TikTok и подобни).
                Google не разрешава вход през такива браузъри по съображения за
                сигурност. За да продължиш с регистрацията, отвори страницата в
                <span x-text="os === 'ios' ? 'Safari' : 'Chrome'"></span>.
            </p>

            <button type="button" @click="openExternal()"
                class="w-full bg-red-600 hover:bg-red-700 text-white font-semibold py-3 px-4 rounded-lg transition cursor-pointer">
                <span x-show="os === 'android'">Отвори в Chrome</span>
                <span x-show="os === 'ios'">Отвори в Safari</span>
                <span x-show="os !== 'android' && os !== 'ios'">Отвори в браузър</span>
            </button>

            <div x-show="os === 'ios'" class="text-left text-xs text-gray-700 bg-gray-50 border border-gray-200 rounded-lg p-3 mt-4">
                <p class="font-semibold mb-1 text-gray-800">Ако бутонът горе не сработи:</p>
                <ol class="list-decimal list-inside space-y-1">
                    <li>Тапни иконата с три точки <strong>⋯</strong> (или <strong>...</strong>) горе вдясно.</li>
                    <li>Избери <strong>„Отвори в Safari"</strong> (Open in Safari).</li>
                </ol>
            </div>

            <div x-show="os === 'android'" class="text-left text-xs text-gray-700 bg-gray-50 border border-gray-200 rounded-lg p-3 mt-4">
                <p class="font-semibold mb-1 text-gray-800">Ако нямаш Chrome:</p>
                <p>Линкът ще се отвори автоматично в системния ти браузър.</p>
            </div>
        </div>
    </div>
</div>
