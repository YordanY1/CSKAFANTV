<?php

namespace App\Livewire\Pages;

use App\Models\Video;
use Livewire\Component;

class Videos extends Component
{
    public string $search = '';
    public string $filterTag = '';

    public function render()
    {
        $videos = Video::query()
            ->when($this->search, fn($q) =>
            $q->where('title', 'like', "%{$this->search}%")
                ->orWhere('description', 'like', "%{$this->search}%"))
            ->when($this->filterTag, fn($q) =>
            $q->where('tags', 'like', "%{$this->filterTag}%"))
            ->latest()
            ->get();

        $allTags = Video::pluck('tags')
            ->filter()
            ->flatMap(fn($tags) => explode(',', $tags))
            ->map(fn($tag) => trim($tag))
            ->unique();

        return view('livewire.pages.videos', compact('videos', 'allTags'))
            ->layout('layouts.app', [
                'title' => 'Видеогалерия | CSKA FAN TV',
                'description' => 'Гледайте най-добрите моменти от мачовете на ЦСКА – голове, интервюта, репортажи и вълнуващи кадри от стадиона.',
                'robots' => 'index, follow',
                'canonical' => url('/videos'),
                'og_title' => 'Видео Архив на ЦСКА | CSKA FAN TV',
                'og_description' => 'Най-интересните видео моменти за червените фенове – от головете до интервютата с любимите играчи.',
                'og_image' => asset('images/og-cska.jpg'),
                'og_url' => url('/videos'),
                'og_type' => 'website',
            ]);
    }
}
