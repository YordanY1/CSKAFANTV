<nav x-data="{ open: false }" class="bg-primary text-cta shadow-md">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="flex justify-between items-center py-2">
            {{-- Logo --}}
            <a href="/" class="flex items-center space-x-2 hover:text-accent">
                <img src="{{ asset('images/logo/logo.jpg') }}" alt="ЦСКА Лого"
                    class="h-14 w-auto object-contain rounded-full ring-2 ring-accent transition duration-200" />
                <span class="text-xl font-extrabold uppercase tracking-wide">ЦСКА ФЕН ТВ</span>
            </a>

            {{-- Desktop Menu --}}
            <div class="hidden md:flex space-x-6">
                <a href="/" class="hover:text-accent">Начало</a>
                <a href="#matches" class="hover:text-accent">Мачове</a>
                <a href="#standings" class="hover:text-accent">Класиране</a>
                <a href="#players" class="hover:text-accent">Играчите</a>
                <a href="#contact" class="hover:text-accent">Контакти</a>
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
    <div x-show="open" x-transition class="md:hidden bg-primary px-4 pb-4">
        <a href="/" class="block py-2 hover:text-accent">Начало</a>
        <a href="#matches" class="block py-2 hover:text-accent">Мачове</a>
        <a href="#standings" class="block py-2 hover:text-accent">Класиране</a>
        <a href="#players" class="block py-2 hover:text-accent">Играчите</a>
        <a href="#contact" class="block py-2 hover:text-accent">Контакти</a>
    </div>
</nav>
