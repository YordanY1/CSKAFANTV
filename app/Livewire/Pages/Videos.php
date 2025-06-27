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
            ->when($this->search, fn($q) => $q->where('title', 'like', "%{$this->search}%")
                ->orWhere('description', 'like', "%{$this->search}%"))
            ->when($this->filterTag, fn($q) => $q->where('tags', 'like', "%{$this->filterTag}%"))
            ->latest()
            ->get();

        $allTags = Video::pluck('tags')->filter()->flatMap(fn($tags) => explode(',', $tags))->unique();

        return view('livewire.pages.videos', compact('videos', 'allTags'))->layout('layouts.app', [
            'title' => 'Видео Галерия',
            'description' => 'Гледайте най-добрите моменти от футболните мачове, включително голове, асистенции и други вълнуващи моменти.',
        ]);
    }
}
