<?php

namespace App\Livewire\Pages\Archive;

use App\Support\Season;
use Livewire\Component;

class ArchiveIndex extends Component
{
    public array $layoutData = [];

    public function mount(): void
    {
        $this->layoutData = [
            'title' => 'Архив | Мачове, оценки и класации по сезони – CSKA FAN TV',
            'description' => 'Архив на ЦСКА по сезони – всички изиграни мачове, оценки на играчите, Зала на славата и класиране по прогнози.',
            'robots' => 'index, follow',
            'canonical' => url('/archive'),
            'og_title' => 'Архив на CSKA FAN TV',
            'og_description' => 'Прегледай всички сезони – мачове, оценки на играчи, Зала на славата и класиране по прогнози.',
            'og_image' => asset('images/og-cska.png'),
            'og_url' => url('/archive'),
            'og_type' => 'website',
        ];
    }

    public function render()
    {
        return view('livewire.pages.archive.index', [
            'seasons' => Season::all(),
        ])->layout('layouts.app', $this->layoutData);
    }
}
