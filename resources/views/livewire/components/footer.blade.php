<footer class="bg-primary text-cta py-10 px-6 mt-12">
    <div class="max-w-7xl mx-auto grid grid-cols-1 sm:grid-cols-4 gap-6 text-sm text-center sm:text-left">

        <div>
            <h3 class="text-lg font-bold uppercase tracking-wide mb-2">CSKA FAN TV</h3>
            <p class="text-accent">
                Онлайн домът на червените фенове. Мачове, прогнози, награди и още.
            </p>
        </div>

        <div>
            <h4 class="font-semibold uppercase mb-2">Навигация</h4>
            <ul class="space-y-1">
                <li><a href="{{ route('home') }}" wire:navigate class="hover:text-accent">Начало</a></li>
                <li><a href="{{ route('matches') }}" wire:navigate class="hover:text-accent">Мачове</a></li>
                <li><a href="{{ route('standings') }}" wire:navigate class="hover:text-accent">Класиране</a></li>
                <li><a href="{{ route('tactics') }}" wire:navigate class="hover:text-accent">Дъска</a></li>
                <li><a href="{{ route('contact') }}" wire:navigate class="hover:text-accent">Контакти</a></li>
            </ul>
        </div>

        <div>
            <h4 class="font-semibold uppercase mb-2">Още</h4>
            <ul class="space-y-1">
                <li><a href="{{ route('players') }}" wire:navigate class="hover:text-accent">Играчите</a></li>
                <li><a href="{{ route('cards') }}" wire:navigate class="hover:text-accent">Картони</a></li>
                <li><a href="{{ route('player.ratings') }}" wire:navigate class="hover:text-accent">Оценки</a></li>
                <li><a href="{{ route('predictions.rankings') }}" wire:navigate class="hover:text-accent">Прогнози
                        класация</a></li>
                <li><a href="{{ route('videos') }}" wire:navigate class="hover:text-accent">Видео галерия</a></li>
            </ul>
        </div>

        <!-- Socials -->
        <div>
            <h4 class="font-semibold uppercase mb-2">Последвай ни</h4>
            <div class="flex justify-center sm:justify-start space-x-4 mt-2 text-xl">
                <a href="#" class="hover:text-accent" title="Facebook"><i class="fab fa-facebook-f"></i></a>
                <a href="#" class="hover:text-accent" title="Instagram"><i class="fab fa-instagram"></i></a>
                <a href="#" class="hover:text-accent" title="YouTube"><i class="fab fa-youtube"></i></a>
                <a href="https://discord.gg/ТВОЯ_КАНАЛ" class="hover:text-accent" title="Discord"><i
                        class="fab fa-discord"></i></a>
            </div>

            <div class="mt-4">
                <a href="{{ route('privacy-policy') }}" wire:navigate class="block hover:text-accent text-xs">Политика
                    за поверителност</a>
                <a href="{{ route('cookie-policy') }}" wire:navigate class="block hover:text-accent text-xs">Политика
                    за бисквитки</a>
            </div>
        </div>

    </div>

    <div class="mt-8 text-center text-xs text-card">
        &copy; {{ date('Y') }} CSKA FAN TV. Всички права запазени.
    </div>
</footer>
