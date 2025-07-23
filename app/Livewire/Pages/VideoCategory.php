<?php

namespace App\Livewire\Pages;

use App\Models\Video;
use Livewire\Component;

class VideoCategory extends Component
{
    public ?string $categorySlug = null;
    public ?string $categoryName = null;
    public array $layoutData = [];

    public function mount(string $slug)
    {
        $this->categorySlug = $slug;

        $video = Video::where('category_slug', $slug)->latest()->first();
        $this->categoryName = $video->category ?? ucfirst(str_replace('-', ' ', $slug));

        $this->layoutData = [
            'title' => "Категория: {$this->categoryName} | CSKA FAN TV",
            'description' => "Гледай всички видеа от категорията '{$this->categoryName}' в CSKA FAN TV – подкасти, интервюта и още.",
            'robots' => 'index, follow',
            'canonical' => url("/videos/category/{$slug}"),
            'og_title' => "{$this->categoryName} – Видеа | CSKA FAN TV",
            'og_description' => "Видеа от рубриката '{$this->categoryName}', включваща най-интересните моменти и фенско съдържание.",
            'og_image' => asset('images/og-cska.png'), 
            'og_url' => url("/videos/category/{$slug}"),
            'og_type' => 'website',
        ];
    }

    public function render()
    {
        $videos = Video::where('category_slug', $this->categorySlug)->latest()->get();

        return view('livewire.pages.video-category', [
            'videos' => $videos,
            'layoutData' => $this->layoutData,
        ])->layout('layouts.app', $this->layoutData);
    }
}
