<nav x-data="{ open: false, openRegister: false }" class="bg-primary text-cta shadow-md">

    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="flex justify-between items-center py-2">
            {{-- Logo --}}
            <a href="/" class="flex items-center space-x-2 hover:text-accent">
                <img src="{{ asset('images/logo/logo.jpg') }}" alt="ЦСКА Лого"
                    class="h-14 w-auto object-contain rounded-full ring-2 ring-accent transition duration-200" />
                <span class="text-xl font-extrabold uppercase tracking-wide">ЦСКА ФЕН ТВ</span>
            </a>

            {{-- Desktop Menu --}}
            <div class="hidden md:flex space-x-6 items-center">
                <a href="/" class="hover:text-accent">Начало</a>
                <a href="#matches" class="hover:text-accent">Мачове</a>
                <a href="#standings" class="hover:text-accent">Класиране</a>
                <a href="#players" class="hover:text-accent">Играчите</a>
                <a href="#contact" class="hover:text-accent">Контакти</a>
                @guest
                    <button @click="openRegister = true"
                        class="text-sm font-medium text-white bg-accent hover:bg-primary px-4 py-2 rounded transition cursor-pointer">
                        Регистрация
                    </button>
                @endguest

                @auth
                    <a href="{{ route('profile') }}" wire:navigate
                        class="text-sm font-medium text-white bg-accent hover:bg-primary px-4 py-2 rounded transition">
                        Профил
                    </a>
                @endauth

            </div>

            {{-- Mobile toggle --}}
            <div class="md:hidden">
                <button @click="open = !open" class="focus:outline-none">
                    <i class="fas fa-bars text-xl"></i>
                </button>
            </div>
        </div>
    </div>

    {{-- Mobile Menu --}}
    <div x-show="open" x-transition class="md:hidden bg-primary px-4 pb-4 flex flex-col items-center space-y-2">
        <a href="/" class="block py-2 hover:text-accent">Начало</a>
        <a href="#matches" class="block py-2 hover:text-accent">Мачове</a>
        <a href="#standings" class="block py-2 hover:text-accent">Класиране</a>
        <a href="#players" class="block py-2 hover:text-accent">Играчите</a>
        <a href="#contact" class="block py-2 hover:text-accent">Контакти</a>
        @guest
            <button @click="openRegister = true"
                class="text-sm font-medium text-white bg-accent hover:bg-primary px-4 py-2 rounded transition cursor-pointer">
                Регистрация
            </button>
        @endguest

        @auth
            <a href="{{ route('profile') }}" wire:navigate
                class="text-sm font-medium text-white bg-accent hover:bg-primary px-4 py-2 rounded transition">
                Профил
            </a>
        @endauth

    </div>


    <!-- Register Modal -->
    <div x-cloak>
        <div x-show="openRegister" x-transition
            class="fixed inset-0 bg-black/60 backdrop-blur-sm z-50 flex items-center justify-center px-4">
            <div @click.away="openRegister = false"
                class="bg-white rounded-xl shadow-2xl max-w-md w-full p-6 sm:p-8 relative">

                <button @click="openRegister = false"
                    class="absolute top-3 right-3 text-gray-400 hover:text-red-500 transition">
                    <i class="fas fa-times"></i>
                </button>

                <div class="text-center">
                    <img src="{{ asset('images/logo/logo.jpg') }}" alt="Лого"
                        class="w-20 h-20 mx-auto rounded-full mb-4">
                    <h2 class="text-2xl font-bold text-gray-800 mb-2">Добре дошъл!</h2>
                    <p class="text-sm text-gray-500 mb-6">Влез в ЦСКА ФЕН ТВ с един клик</p>

                    <a href="{{ route('auth.google.redirect') }}"
                        class="inline-flex items-center justify-center w-full gap-2 py-3 px-6 bg-red-600 hover:bg-red-700 text-white font-semibold rounded-lg transition shadow-sm">
                        <i class="fab fa-google text-lg"></i>
                        Вход с Google
                    </a>
                </div>
            </div>
        </div>
    </div>
</nav>
