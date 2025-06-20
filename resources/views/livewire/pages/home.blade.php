<div class="space-y-20">

    {{-- HERO --}}
    <section class="relative bg-primary text-cta py-24 px-6 text-center overflow-hidden shadow-md">
        <div
            class="absolute inset-0 opacity-10 bg-[url('/images/football-texture.svg')] bg-cover bg-center pointer-events-none">
        </div>

        <div class="relative z-10 max-w-4xl mx-auto animate-scale-fade-in">
            <h1 class="text-5xl md:text-6xl font-extrabold uppercase tracking-wide mb-6">
                Добре дошли в<br>
                <span class="text-cta bg-accent px-4 py-1 rounded inline-block mt-2 shadow-lg">
                    ЦСКА ФЕН ТВ
                </span>
            </h1>
            <p class="text-lg md:text-xl text-accent mt-4 font-medium max-w-xl mx-auto">
                Официалната онлайн арена за всички „червени“ сърца — следи мачове, гласувай за играчи и участвай в
                томболи!
            </p>

            <div class="mt-8">
                <a href="#matches"
                    class="inline-flex items-center px-6 py-3 bg-accent text-cta font-semibold rounded hover:bg-accent-2 transition">
                    <i class="fas fa-futbol mr-2"></i> Виж предстоящите мачове
                </a>
            </div>
        </div>
    </section>

    {{-- LATEST MATCHES --}}
    <livewire:components.latest-matches />


    {{-- STANDINGS PREVIEW --}}
    <livewire:components.league-standings />


    {{-- FEATURED PLAYERS --}}
    <livewire:components.featured-players />

</div>
