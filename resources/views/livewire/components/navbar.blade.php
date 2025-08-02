<nav x-data="{ open: false, openRegister: false, showLoginDropdown: false }" class="bg-primary text-cta shadow-md">
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
                            class="block px-4 py-2 hover:bg-accent hover:text-white transition">Играчи и щаб</a>
                        <a href="{{ route('cards') }}" wire:navigate
                            class="block px-4 py-2 hover:bg-accent hover:text-white transition">Картони</a>
                        <a href="{{ route('player.ratings') }}" wire:navigate
                            class="block px-4 py-2 hover:bg-accent hover:text-white transition">Оценки</a>
                        <a href="{{ route('hall.of.fame') }}" wire:navigate
                            class="block px-4 py-2 hover:bg-accent hover:text-white transition">
                            Зала на славата
                        </a>

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
                    <div class="flex items-center gap-1 py-2 px-3 rounded-md hover:text-accent cursor-pointer">
                        Видео
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path d="M19 9l-7 7-7-7" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" />
                        </svg>
                    </div>
                    <div
                        class="absolute left-0 mt-2 bg-white text-primary rounded-md shadow-lg z-50 py-2 w-56
        invisible opacity-0 group-hover:visible group-hover:opacity-100 transition duration-200">

                        @foreach ($videoCategories as $category)
                            <a href="{{ route('videos.category', ['slug' => $category['category_slug']]) }}"
                                class="block px-4 py-2 hover:bg-accent hover:text-white transition">
                                {{ $category['category'] }}
                            </a>
                        @endforeach

                        <div class="border-t border-gray-200 my-1"></div>
                        <a href="{{ route('videos') }}"
                            class="block px-4 py-2 hover:bg-accent hover:text-white transition">Всички видеа</a>
                    </div>
                </div>



                <a href="{{ route('contact') }}" wire:navigate
                    class="block py-2 px-3 rounded-md transition duration-200 {{ request()->routeIs('contact') ? 'bg-accent text-white font-bold hover:text-white' : 'hover:text-accent' }}">
                    Контакти
                </a>

                @guest
                    <button @click="showLoginDropdown = true"
                        class="text-sm font-medium text-white bg-accent hover:bg-primary px-4 py-2 rounded transition cursor-pointer ">
                        Вход
                    </button>

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
                        LIVE
                    </a>
                @endif


                <!-- Social Icons -->
                <div class="flex items-center space-x-4 text-2xl ml-4 text-white">
                    <a href="https://facebook.com/CSKAFENTV48" target="_blank" class="hover:text-blue-400 transition">
                        <i class="fab fa-facebook-square"></i>
                    </a>
                    <a href="https://instagram.com/cskafantv" target="_blank" class="hover:text-pink-400 transition">
                        <i class="fab fa-instagram"></i>
                    </a>
                    <a href="https://www.youtube.com/@CSKAFANTV48" target="_blank" title="Абонирай се в YouTube!"
                        class="animate-pulse ring-2 ring-red-500 rounded-full p-1 hover:scale-110 transition duration-300 shadow-lg">
                        <i class="fab fa-youtube text-white"></i>
                    </a>
                    <a href="https://discord.com/invite/GY29Xccj42" target="_blank"
                        class="hover:text-indigo-400 transition">
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
                    class="block py-2 px-3 rounded-md hover:bg-accent hover:text-white transition">Играчи и щаб</a>
                <a href="{{ route('cards') }}" wire:navigate
                    class="block py-2 px-3 rounded-md hover:bg-accent hover:text-white transition">Картони</a>
                <a href="{{ route('player.ratings') }}" wire:navigate
                    class="block py-2 px-3 rounded-md hover:bg-accent hover:text-white transition">Оценки</a>
                <a href="{{ route('hall.of.fame') }}" wire:navigate
                    class="block py-2 px-3 rounded-md hover:bg-accent hover:text-white transition">
                    Зала на славата
                </a>
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
                @php
                    $categories = \App\Models\Video::query()
                        ->select('category', 'category_slug')
                        ->groupBy('category', 'category_slug')
                        ->havingRaw('COUNT(*) > 0')
                        ->orderBy('category')
                        ->get();
                @endphp

                @foreach ($categories as $category)
                    <a href="{{ route('videos.category', ['slug' => $category->category_slug]) }}" wire:navigate
                        class="block py-2 px-3 rounded-md hover:bg-accent hover:text-white transition">
                        {{ $category->category }}
                    </a>
                @endforeach

                <div class="border-t border-gray-200 my-1"></div>
                <a href="{{ route('videos') }}" wire:navigate
                    class="block py-2 px-3 rounded-md hover:bg-accent hover:text-white transition">
                    Всички видеа
                </a>
            </div>
        </div>


        <a href="{{ route('contact') }}" wire:navigate
            class="block py-2 px-3 rounded-md transition duration-200
      {{ request()->routeIs('contact') ? 'bg-accent text-white font-bold hover:text-white' : 'hover:text-accent' }}">
            Контакти
        </a>

        @guest
            <button @click="showLoginDropdown = true"
                class="text-sm font-medium text-white bg-primary hover:bg-accent px-4 py-2 rounded transition cursor-pointer">
                Вход
            </button>

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
                LIVE
            </a>
        @endif
        <!-- Mobile Social Icons -->
        <div class="flex justify-center gap-4 text-2xl mt-4 sm:hidden text-white">
            <a href="https://facebook.com/CSKAFENTV48" target="_blank" rel="noopener noreferrer"
                class="hover:text-blue-500 transition" title="Facebook">
                <i class="fab fa-facebook-square"></i>
            </a>
            <a href="https://instagram.com/cskafantv" target="_blank" rel="noopener noreferrer"
                class="hover:text-pink-500 transition" title="Instagram">
                <i class="fab fa-instagram"></i>
            </a>
            <a href="https://www.youtube.com/@CSKAFANTV48" target="_blank" title="Абонирай се в YouTube!"
                class="animate-pulse ring-2 ring-red-500 rounded-full p-1 hover:scale-110 transition duration-300 shadow-lg">
                <i class="fab fa-youtube text-white"></i>
            </a>
            <a href="https://discord.com/invite/GY29Xccj42" target="_blank" rel="noopener noreferrer"
                class="hover:text-indigo-400 transition" title="Discord">
                <i class="fab fa-discord"></i>
            </a>
        </div>
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

                    <div class="space-y-4">
                        <!-- Google -->
                        <a href="{{ route('auth.google.redirect') }}"
                            class="inline-flex items-center justify-center w-full gap-2 py-3 px-6 bg-red-600 hover:bg-red-700 text-white font-semibold rounded-lg transition shadow-sm">
                            <i class="fab fa-google text-lg"></i>
                            Вход с Google
                        </a>

                        <div class="text-gray-400 text-sm text-center">или</div>
                        <livewire:auth.register />
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Login Modal -->
    <div x-cloak>
        <div x-show="showLoginDropdown" x-transition
            class="fixed inset-0 bg-black/60 backdrop-blur-sm z-50 flex items-center justify-center px-4">
            <div @click.away="showLoginDropdown = false"
                class="bg-white rounded-xl shadow-2xl max-w-md w-full p-6 sm:p-8 relative">

                <button @click="showLoginDropdown = false"
                    class="absolute top-3 right-3 text-gray-400 hover:text-red-500 transition">
                    <i class="fas fa-times"></i>
                </button>

                <div class="text-center">
                    <img src="{{ asset('images/logo/logo.jpg') }}" alt="Лого"
                        class="w-20 h-20 mx-auto rounded-full mb-4">
                    <h2 class="text-2xl font-bold text-gray-800 mb-2">Добре дошъл обратно!</h2>
                    <p class="text-sm text-gray-500 mb-6">Влез в акаунта си с един клик</p>

                    <div class="space-y-4">
                        <!-- Google Login -->
                        <a href="{{ route('auth.google.redirect') }}"
                            class="inline-flex items-center justify-center w-full gap-2 py-3 px-6 bg-red-600 hover:bg-red-700 text-white font-semibold rounded-lg transition shadow-sm cursor-pointer">
                            <i class="fab fa-google text-lg"></i>
                            Вход с Google
                        </a>

                        <div class="text-gray-400 text-sm text-center">или</div>

                        <!-- Livewire Login Form -->
                        <livewire:auth.login />

                        <div class="text-center text-sm mt-4">
                            <span class="text-gray-600">Нямаш акаунт?</span>
                            <button @click="showLoginDropdown = false; openRegister = true"
                                class="text-red-600 hover:underline ml-1 cursor-pointer font-medium">
                                Регистрирай се
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

</nav>
