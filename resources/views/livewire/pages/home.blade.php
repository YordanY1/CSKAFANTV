<div class="space-y-20">

    {{-- HERO --}}
    <section class="relative bg-primary text-cta py-24 px-6 text-center overflow-hidden shadow-md">

        <div class="relative z-10 max-w-4xl mx-auto animate-scale-fade-in">
            <h1 class="text-3xl md:text-6xl font-extrabold uppercase tracking-wide mb-6">
                Добре дошли в<br>
                <span class="text-cta bg-accent px-4 py-1 rounded inline-block mt-2 shadow-lg">
                    CSKA FAN TV
                </span>
            </h1>
            <p class="text-lg md:text-xl text-accent mt-4 font-medium max-w-xl mx-auto">
                Официалната онлайн арена за всички „червени“ сърца — следи мачове, гласувай за играчи и участвай в
                томболи!
            </p>

            <div class="mt-8">
                <a href="{{ route('matches') }}" wire:navigate
                    class="inline-flex items-center px-6 py-3 bg-accent text-cta font-semibold rounded hover:bg-accent-2 transition">
                    <i class="fas fa-futbol mr-2"></i> Виж предстоящите мачове
                </a>
            </div>

        </div>
    </section>

    {{-- <section
        class="relative min-h-screen bg-cover bg-[center_top_25%] text-white text-center px-6 shadow-md overflow-hidden flex items-center justify-center"
        style="background-image: url('/images/logo/background.png');">

        <div class="absolute inset-0 bg-black/50 z-0"></div>


        <div class="relative z-10 max-w-4xl animate-scale-fade-in">
            <h1 class="text-4xl md:text-6xl font-extrabold uppercase tracking-wide mb-6 leading-tight drop-shadow-xl">
                Добре дошли в<br>
                <span class="bg-accent text-cta px-4 py-2 rounded shadow-lg inline-block mt-2 text-5xl">
                    CSKA FAN TV
                </span>
            </h1>

            <p class="text-lg md:text-xl text-white/90 mt-4 font-medium max-w-xl mx-auto leading-relaxed">
                Официалната онлайн арена за всички „червени“ сърца — следи мачове, гласувай за играчи и участвай в
                томболи!
            </p>

            <div class="mt-8">
                <a href="{{ route('matches') }}" wire:navigate
                    class="inline-flex items-center gap-2 px-6 py-3 bg-accent text-white font-semibold rounded-lg hover:bg-accent-2 transition shadow-lg hover:scale-105 duration-300">
                    <i class="fas fa-futbol"></i> Виж предстоящите мачове
                </a>
            </div>
        </div>
    </section> --}}

    {{-- LATEST MATCHES --}}
    <livewire:components.latest-matches />

    {{-- FEATURED PLAYERS --}}
    <livewire:components.featured-players />

    {{-- STANDINGS PREVIEW --}}
    <livewire:components.league-standings />

    {{-- TOP DISCIPLINED PLAYERS --}}
    <livewire:components.top-disciplined-players />

</div>
