<?php

namespace App\Livewire\Pages;

use App\Models\Video;
use Livewire\Component;

class Videos extends Component
{
    public string $filterCategorySlug = '';

    public $queryString = [
        'filterCategorySlug' => ['except' => ''],
    ];

    public function render()
    {

        $videos = Video::query()
            ->when($this->filterCategorySlug !== '', function ($query) {
                $query->where('category_slug', $this->filterCategorySlug);
            })
            ->latest()
            ->get();

        // logger('ВИДЕА ВЪРНАТИ ЗА КАТЕГОРИЯТА:', [
        //     'filterCategorySlug' => $this->filterCategorySlug,
        //     'video_ids' => $videos->pluck('id'),
        //     'category_slugs' => $videos->pluck('category_slug')->unique(),
        // ]);

        $allCategories = Video::select('category', 'category_slug')
            ->get()
            ->unique('category_slug')
            ->values();

        return view('livewire.pages.videos', compact('videos', 'allCategories'))
            ->layout('layouts.app', [
                'title' => 'Видеогалерия | CSKA FAN TV',
                'description' => 'Гледайте най-добрите моменти от мачовете на ЦСКА – голове, интервюта, репортажи и вълнуващи кадри от стадиона.',
                'robots' => 'index, follow',
                'canonical' => url('/videos'),
                'og_title' => 'Видео Архив на ЦСКА | CSKA FAN TV',
                'og_description' => 'Най-интересните видео моменти за червените фенове – от головете до интервютата с любимите играчи.',
                'og_image' => asset('images/og-cska.png'),
                'og_url' => url('/videos'),
                'og_type' => 'website',
            ]);
    }
}
