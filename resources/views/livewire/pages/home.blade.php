<div class="space-y-20">

    {{-- HERO --}}
    <section class="relative bg-primary text-cta py-24 px-6 text-center overflow-hidden shadow-md">

        <img src="{{ asset('images/logo/logo-2.png') }}"
            class="hidden md:block absolute left-[calc(40%-500px)] top-1/2 transform -translate-y-1/2 w-90 opacity-50 pointer-events-none"
            alt="Logo Left" />

        <img src="{{ asset('images/logo/logo-2.png') }}"
            class="hidden md:block absolute left-[calc(52%+300px)] top-1/2 transform -translate-y-1/2 w-90 opacity-50 pointer-events-none"
            alt="Logo Right" />

        <img src="{{ asset('images/logo/logo-2.png') }}"
            class="block md:hidden absolute top-1/2 left-1/2 transform -translate-x-1/2 -translate-y-1/2
    w-[300px] opacity-25 blur-[1px] mix-blend-soft-light pointer-events-none select-none"
            alt="Logo Background" />

        <div class="relative z-10 max-w-4xl mx-auto animate-scale-fade-in">
            <h1 class="text-3xl md:text-6xl font-extrabold uppercase tracking-wide mb-6 text-white">
                Добре дошли в<br>
                <span class="text-cta bg-accent px-4 py-1 rounded inline-block mt-2 shadow-lg">
                    CSKA FAN TV
                </span>
            </h1>

            <p class="text-lg md:text-xl text-white mt-4 font-medium max-w-xl mx-auto">
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


    {{-- LATEST MATCHES --}}
    <livewire:components.latest-matches />

    {{-- FEATURED PLAYERS --}}
    <livewire:components.featured-players />

    {{-- STANDINGS PREVIEW --}}
    <livewire:components.league-standings />

    {{-- TOP DISCIPLINED PLAYERS --}}
    <livewire:components.top-disciplined-players />

</div>
