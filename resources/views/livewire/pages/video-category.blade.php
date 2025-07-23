<section class="px-4 py-12 max-w-7xl mx-auto">
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
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
            <p class="text-center col-span-full text-gray-500">Няма видеа в тази категория.</p>
        @endforelse
    </div>
</section>
