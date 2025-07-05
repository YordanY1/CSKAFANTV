<?php

namespace App\Livewire\Pages;

use App\Models\Standing;
use Livewire\Component;
use Livewire\WithPagination;

class FullStandingsPage extends Component
{
    use WithPagination;

    public $search = '';
    public $sortColumn = 'points';
    public $sortDirection = 'desc';

    public array $layoutData = [];

    public function mount()
    {
        $this->layoutData = [
            'title' => 'Класиране – Таблица на отборите | CSKA FAN TV',
            'description' => 'Виж текущото класиране на отборите: точки, победи, загуби и форма. Подробна таблица със сортиране и търсене.',
            'robots' => 'index, follow',
            'canonical' => url('/standings'),
            'og_title' => 'Класиране на отборите | CSKA FAN TV',
            'og_description' => 'Актуална таблица с класирането на всички отбори. Проследи битката за върха и анализирай формата на любимия си тим.',
            'og_image' => asset('images/og-cska.jpg'),
            'og_url' => url('/standings'),
            'og_type' => 'website',
        ];
    }

    public function sortBy($column)
    {
        if ($this->sortColumn === $column) {
            $this->sortDirection = $this->sortDirection === 'asc' ? 'desc' : 'asc';
        } else {
            $this->sortColumn = $column;
            $this->sortDirection = 'asc';
        }
    }

    public function render()
    {
        $standings = Standing::with('team')
            ->get()
            ->filter(function ($item) {
                return str_contains(strtolower($item->team?->name), strtolower($this->search));
            })
            ->sortBy(function ($item) {
                return $this->sortColumn === 'team'
                    ? strtolower($item->team?->name)
                    : $item->{$this->sortColumn};
            }, SORT_REGULAR, $this->sortDirection === 'desc')
            ->values();

        return view('livewire.pages.full-standings-page', [
            'standings' => $standings,
        ])->layout('layouts.app', $this->layoutData);
    }
}
