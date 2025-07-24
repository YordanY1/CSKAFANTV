<section class="px-4 py-12 max-w-7xl mx-auto">
    <h2 class="text-3xl font-bold text-center text-primary mb-8">Видеогалерия</h2>

    <div class="flex flex-col md:flex-row gap-6">
        <!-- Sidebar with filters -->
        <div class="md:w-1/4" x-data="{ open: false }">
            <!-- Mobile Toggle -->
            <button @click="open = !open" class="md:hidden mb-4 w-full bg-primary text-white px-4 py-2 rounded">
                <span x-show="!open">Филтрирай по категория</span>
                <span x-show="open">Затвори филтрите</span>
            </button>

            <div :class="{ 'hidden': !open }" class="md:block space-y-2">
                <button wire:click="$set('filterCategorySlug', '')"
                    class="block w-full text-left px-4 py-2 rounded
        {{ $filterCategorySlug === '' ? 'bg-primary text-white' : 'bg-white text-primary border border-primary hover:bg-primary hover:text-white' }}">
                    Всички
                </button>

                @foreach ($allCategories as $cat)
                    <button wire:click="$set('filterCategorySlug', '{{ $cat->category_slug }}')"
                        class="block w-full text-left px-4 py-2 rounded
            {{ $filterCategorySlug === $cat->category_slug ? 'bg-primary text-white' : 'bg-white text-primary border border-primary hover:bg-primary hover:text-white' }}">
                        {{ $cat->category }}
                    </button>
                @endforeach
            </div>
        </div>

        <!-- Video Grid -->
        <div class="md:w-3/4 grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-6"
            wire:key="videos-{{ md5($filterCategorySlug) }}">
            @forelse ($videos as $video)
                <div class="rounded-lg shadow hover:shadow-lg transition bg-white overflow-hidden">
                    <a href="https://www.youtube.com/watch?v={{ $video->youtube_id }}" target="_blank">
                        <img class="w-full aspect-video object-cover"
                            src="https://img.youtube.com/vi/{{ $video->youtube_id }}/hqdefault.jpg"
                            alt="{{ $video->title }}" />
                    </a>
                    <div class="p-4">
                        <h3 class="font-bold text-lg text-primary">{{ $video->title }}</h3>

                        <div x-data="{ expanded: false }" class="text-sm text-gray-600 mt-2">
                            <div x-show="!expanded" x-html="`{!! \Str::limit($video->description, 150, '...') !!}`"></div>
                            <div x-show="expanded" x-html="`{!! $video->description !!}`"></div>

                            <button @click="expanded = !expanded"
                                class="mt-3 inline-block text-sm font-semibold text-red-700 hover:text-red-800 transition cursor-pointer">
                                <span x-text="expanded ? 'Покажи по-малко' : 'Прочети още'"></span>
                            </button>
                        </div>
                    </div>
                </div>
            @empty
                <p class="col-span-full text-center text-gray-500">Няма видеа в тази категория.</p>
            @endforelse
        </div>

    </div>
</section>
