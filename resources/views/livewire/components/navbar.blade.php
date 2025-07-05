<nav x-data="{ open: false, openRegister: false }" class="bg-primary text-cta shadow-md">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="flex justify-between items-center py-2">
            <!-- Logo -->
            <a href="/" wire:navigate class="flex items-center space-x-2 hover:text-accent">
                <img src="{{ asset('images/logo/logo.jpg') }}" alt="CSKA FAN TV"
                    class="h-14 w-auto object-contain rounded-full ring-2 ring-accent transition duration-200" />
                <span class="text-xl font-extrabold uppercase tracking-wide">CSKA FAN TV</span>
            </a>

            <!-- Desktop Menu -->
            <div class="hidden md:flex space-x-6 items-center">
                <a href="/" wire:navigate
                    class="block py-2 px-3 rounded-md transition duration-200 {{ request()->routeIs('home') ? 'bg-accent text-white font-bold hover:text-white' : 'hover:text-accent' }}">
                    Начало
                </a>

                <!-- Team Dropdown -->
                <div class="relative group">
                    <div
                        class="flex items-center gap-1 py-2 px-3 rounded-md hover:text-accent transition duration-200 cursor-pointer">
                        Отбор
                        <svg class="w-4 h-4 transition-transform" fill="none" stroke="currentColor" stroke-width="2"
                            viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M19 9l-7 7-7-7" />
                        </svg>
                    </div>
                    <div
                        class="absolute left-0 mt-2 bg-white text-primary rounded-md shadow-lg z-50 py-2 w-40 invisible opacity-0 group-hover:visible group-hover:opacity-100 transition duration-200">
                        <a href="{{ route('players') }}" wire:navigate
                            class="block px-4 py-2 hover:bg-accent hover:text-white transition">Играчите</a>
                        <a href="{{ route('cards') }}" wire:navigate
                            class="block px-4 py-2 hover:bg-accent hover:text-white transition">Картони</a>
                        <a href="{{ route('player.ratings') }}" wire:navigate
                            class="block px-4 py-2 hover:bg-accent hover:text-white transition">Оценки</a>
                    </div>
                </div>

                <!-- Matches Dropdown -->
                <div class="relative group">
                    <div
                        class="flex items-center gap-1 py-2 px-3 rounded-md hover:text-accent transition duration-200 cursor-pointer">
                        Мачове
                        <svg class="w-4 h-4 transition-transform" fill="none" stroke="currentColor" stroke-width="2"
                            viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M19 9l-7 7-7-7" />
                        </svg>
                    </div>
                    <div
                        class="absolute left-0 mt-2 bg-white text-primary rounded-md shadow-lg z-50 py-2 w-40 invisible opacity-0 group-hover:visible group-hover:opacity-100 transition duration-200">
                        <a href="{{ route('matches') }}" wire:navigate
                            class="block px-4 py-2 hover:bg-accent hover:text-white transition">Програма</a>
                        <a href="{{ route('tactics') }}" wire:navigate
                            class="block px-4 py-2 hover:bg-accent hover:text-white transition">Дъска</a>
                        <a href="{{ route('standings') }}" wire:navigate
                            class="block px-4 py-2 hover:bg-accent hover:text-white transition">Класиране</a>
                        <a href="{{ route('predictions.rankings') }}" wire:navigate
                            class="block px-4 py-2 hover:bg-accent hover:text-white transition">Прогнози класация</a>
                    </div>
                </div>

                <!-- Video Dropdown -->
                <div class="relative group">
                    <div
                        class="flex items-center gap-1 py-2 px-3 rounded-md hover:text-accent transition duration-200 cursor-pointer">
                        Видео
                        <svg class="w-4 h-4 transition-transform" fill="none" stroke="currentColor" stroke-width="2"
                            viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M19 9l-7 7-7-7" />
                        </svg>
                    </div>
                    <div
                        class="absolute left-0 mt-2 bg-white text-primary rounded-md shadow-lg z-50 py-2 w-40 invisible opacity-0 group-hover:visible group-hover:opacity-100 transition duration-200">
                        <a href="{{ route('videos') }}" wire:navigate
                            class="block px-4 py-2 hover:bg-accent hover:text-white transition">Галерия</a>
                    </div>
                </div>

                <a href="{{ route('contact') }}" wire:navigate
                    class="block py-2 px-3 rounded-md transition duration-200 {{ request()->routeIs('contact') ? 'bg-accent text-white font-bold hover:text-white' : 'hover:text-accent' }}">
                    Контакти
                </a>

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

                @if ($liveMatchYoutubeUrl)
                    <a href="{{ $liveMatchYoutubeUrl }}" target="_blank" rel="noopener noreferrer"
                        class="inline-flex items-center gap-2 bg-red-600 text-white font-semibold text-sm px-4 py-2 rounded-lg animate-pulse shadow hover:bg-red-700 transition">
                        <i class="fab fa-youtube text-lg"></i>
                        НА ЖИВО
                    </a>
                @endif


                <!-- Social Icons -->
                <div class="flex items-center space-x-4 text-2xl ml-4">
                    <a href="https://facebook.com" target="_blank" class="text-blue-600 hover:scale-110 transition"
                        title="Facebook">
                        <i class="fab fa-facebook-square"></i>
                    </a>
                    <a href="https://instagram.com" target="_blank" class="text-pink-500 hover:scale-110 transition"
                        title="Instagram">
                        <i class="fab fa-instagram"></i>
                    </a>
                    <a href="https://youtube.com" target="_blank" class="text-red-600 hover:scale-110 transition"
                        title="YouTube">
                        <i class="fab fa-youtube"></i>
                    </a>
                    <a href="https://discord.gg/ТВОЯ_КАНАЛ" target="_blank"
                        class="text-indigo-500 hover:scale-110 transition" title="Discord">
                        <i class="fab fa-discord"></i>
                    </a>
                </div>
            </div>

            <!-- Mobile Toggle -->
            <div class="md:hidden">
                <button @click="open = !open" class="focus:outline-none">
                    <i class="fas fa-bars text-xl"></i>
                </button>
            </div>
        </div>
    </div>
    </div>


    <!-- Mobile Menu -->
    <div x-show="open" x-transition
        class="md:hidden bg-primary px-4 pb-4 flex flex-col items-center space-y-2 text-center">
        <a href="/" wire:navigate
            class="block py-2 px-3 rounded-md transition duration-200
      {{ request()->routeIs('home') ? 'bg-accent text-white font-bold hover:text-white' : 'hover:text-accent' }}">
            Начало
        </a>

        <!--  Dropdown Mobile -->
        <div x-data="{ openTeam: false }" class="w-full">
            <button @click="openTeam = !openTeam"
                class="w-full py-2 px-3 rounded-md hover:text-accent transition duration-200 flex justify-center items-center gap-2">
                Отбор
                <svg :class="{ 'rotate-180': openTeam }" class="w-4 h-4 transform transition-transform" fill="none"
                    stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M19 9l-7 7-7-7" />
                </svg>
            </button>
            <div x-show="openTeam" x-collapse class="space-y-2">
                <a href="{{ route('players') }}" wire:navigate
                    class="block py-2 px-3 rounded-md hover:bg-accent hover:text-white transition">Играчите</a>
                <a href="{{ route('cards') }}" wire:navigate
                    class="block py-2 px-3 rounded-md hover:bg-accent hover:text-white transition">Картони</a>
                <a href="{{ route('player.ratings') }}" wire:navigate
                    class="block py-2 px-3 rounded-md hover:bg-accent hover:text-white transition">Оценки</a>
            </div>
        </div>

        <!-- Dropdown Mobile -->
        <div x-data="{ openMatches: false }" class="w-full">
            <button @click="openMatches = !openMatches"
                class="w-full py-2 px-3 rounded-md hover:text-accent transition duration-200 flex justify-center items-center gap-2">
                Мачове
                <svg :class="{ 'rotate-180': openMatches }" class="w-4 h-4 transform transition-transform"
                    fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M19 9l-7 7-7-7" />
                </svg>
            </button>
            <div x-show="openMatches" x-collapse class="space-y-2">
                <a href="{{ route('matches') }}" wire:navigate
                    class="block py-2 px-3 rounded-md hover:bg-accent hover:text-white transition">Програма</a>
                <a href="{{ route('standings') }}" wire:navigate
                    class="block py-2 px-3 rounded-md hover:bg-accent hover:text-white transition">Класиране</a>
                <a href="{{ route('predictions.rankings') }}" wire:navigate
                    class="block py-2 px-3 rounded-md hover:bg-accent hover:text-white transition">Прогнози
                    класация</a>
            </div>
        </div>

        <!-- Dropdown Mobile -->
        <div x-data="{ openVideo: false }" class="w-full">
            <button @click="openVideo = !openVideo"
                class="w-full py-2 px-3 rounded-md hover:text-accent transition duration-200 flex justify-center items-center gap-2">
                Видео
                <svg :class="{ 'rotate-180': openVideo }" class="w-4 h-4 transform transition-transform"
                    fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M19 9l-7 7-7-7" />
                </svg>
            </button>
            <div x-show="openVideo" x-collapse class="space-y-2">
                <a href="{{ route('videos') }}" wire:navigate
                    class="block py-2 px-3 rounded-md hover:bg-accent hover:text-white transition">Галерия</a>
                @if ($liveMatchYoutubeUrl)
                    <a href="{{ $liveMatchYoutubeUrl }}" target="_blank"
                        class="block py-2 px-3 rounded-md hover:bg-accent hover:text-white transition">🔴 На Живо</a>
                @endif
            </div>
        </div>

        <a href="{{ route('contact') }}" wire:navigate
            class="block py-2 px-3 rounded-md transition duration-200
      {{ request()->routeIs('contact') ? 'bg-accent text-white font-bold hover:text-white' : 'hover:text-accent' }}">
            Контакти
        </a>

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

        @if ($liveMatchYoutubeUrl)
            <a href="{{ $liveMatchYoutubeUrl }}" target="_blank" rel="noopener noreferrer"
                class="w-full flex items-center justify-center gap-2 bg-red-600 text-white font-semibold text-sm px-4 py-2 rounded-lg animate-pulse shadow hover:bg-red-700 transition">
                <i class="fab fa-youtube text-lg"></i>
                НА ЖИВО
            </a>
        @endif
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
                    <p class="text-sm text-gray-500 mb-6">Влез в CSKA FAN TV с един клик</p>

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
