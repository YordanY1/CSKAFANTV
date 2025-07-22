<?php

namespace App\Livewire\Pages;

use App\Models\Video;
use Livewire\Component;

class VideoCategory extends Component
{
    public ?string $category = null;


    public function mount(string $slug)
    {
        $this->category = Video::where('category_slug', $slug)->value('category');
    }

    public function render()
    {
        $videos = Video::where('category', $this->category)->latest()->get();

        return view('livewire.pages.video-category', [
            'videos' => $videos,
        ])->layout('layouts.app', [
            'title' => "Категория: $this->category",
        ]);
    }
}
