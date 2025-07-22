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
                    <p class="text-sm text-gray-600 mt-2">{{ $video->description }}</p>
                </div>
            </div>
        @empty
            <p class="text-center col-span-full text-gray-500">Няма видеа в тази категория.</p>
        @endforelse
    </div>
</section>
