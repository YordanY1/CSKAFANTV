@php
    /** @var string $season */
    /** @var array $seasons */
    /** @var string $active */
    $categories = [
        'archive.matches' => 'Мачове',
        'archive.player-ratings' => 'Оценки на играчи',
        'archive.hall-of-fame' => 'Зала на славата',
        'archive.prediction-rankings' => 'Класиране по прогнози',
    ];
@endphp

<div class="max-w-7xl mx-auto px-6 pt-8">
    <nav class="text-sm text-gray-500 mb-4 flex items-center gap-2 flex-wrap">
        <a href="{{ route('archive.index') }}" wire:navigate class="hover:text-accent">Архив</a>
        <span>/</span>
        <span class="text-primary font-semibold">{{ \App\Support\Season::label($season) }}</span>
    </nav>

    {{-- Season switcher --}}
    @if (count($seasons) > 1)
        <div class="flex flex-wrap gap-2 mb-4">
            @foreach ($seasons as $s)
                <a href="{{ route($active, $s) }}" wire:navigate
                    class="px-3 py-1 rounded-full text-sm font-medium transition
                    {{ $s === $season ? 'bg-accent text-white shadow-md' : 'bg-gray-200 text-gray-700 hover:bg-gray-300' }}">
                    {{ $s }}
                </a>
            @endforeach
        </div>
    @endif

    {{-- Category tabs --}}
    <div class="flex flex-wrap gap-2 border-b border-gray-200 pb-3">
        @foreach ($categories as $route => $label)
            <a href="{{ route($route, $season) }}" wire:navigate
                class="px-4 py-1.5 rounded-xl text-sm font-medium transition
                {{ $active === $route ? 'bg-primary text-white shadow' : 'bg-gray-100 text-gray-700 hover:bg-gray-200' }}">
                {{ $label }}
            </a>
        @endforeach
    </div>
</div>
