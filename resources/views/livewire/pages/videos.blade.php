<section class="px-4 py-12 max-w-7xl mx-auto">
    <h2 class="text-3xl font-bold text-center text-primary mb-8">Видеогалерия</h2>

    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
        @foreach ($videos as $video)
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
        @endforeach
    </div>
</section>
