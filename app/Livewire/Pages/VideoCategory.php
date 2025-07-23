<?php

namespace App\Livewire\Pages;

use App\Models\Video;
use Livewire\Component;

class VideoCategory extends Component
{
    public ?string $categorySlug = null;

    public function mount(string $slug)
    {
        $this->categorySlug = $slug;
    }

    public function render()
    {
        $videos = Video::where('category_slug', $this->categorySlug)->latest()->get();

        return view('livewire.pages.video-category', [
            'videos' => $videos,
        ])->layout('layouts.app', [
            'title' => "Категория: " . ucfirst(str_replace('-', ' ', $this->categorySlug)),
        ]);
    }
}
