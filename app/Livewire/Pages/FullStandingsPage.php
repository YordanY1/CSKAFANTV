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
            'og_image' => asset('images/og-cska.png'),
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
        $all = Standing::with('team')
            ->get()
            ->filter(fn($item) => str_contains(strtolower($item->team?->name), strtolower($this->search)));

        $auto = $all->filter(fn($s) => $s->manual_rank === null);

        if ($this->sortColumn === 'points') {
            $auto = $auto->sort(function ($a, $b) {
                $multiplier = $this->sortDirection === 'asc' ? 1 : -1;

                if ($a->calculated_points !== $b->calculated_points) {
                    return $multiplier * ($a->calculated_points <=> $b->calculated_points);
                }

                if ($a->goal_difference !== $b->goal_difference) {
                    return $multiplier * ($a->goal_difference <=> $b->goal_difference);
                }

                if ($a->goals_scored !== $b->goals_scored) {
                    return $multiplier * ($a->goals_scored <=> $b->goals_scored);
                }

                return $multiplier * strcmp(strtolower($a->team?->name), strtolower($b->team?->name));
            });
        } else {
            $auto = $auto->sortBy(function ($item) {
                return match ($this->sortColumn) {
                    'team' => strtolower($item->team?->name),
                    'goal_diff' => $item->goal_difference,
                    'manual_rank' => $item->manual_rank,
                    default => $item->{$this->sortColumn},
                };
            }, SORT_REGULAR, $this->sortDirection === 'desc');
        }

        $auto = $auto->values();
        $manuals = $all->filter(fn($s) => $s->manual_rank !== null);

        $final = collect();

        foreach ($manuals as $m) {
            $final->put($m->manual_rank - 1, $m);
        }

        $i = 0;
        foreach ($auto as $team) {
            while ($final->has($i)) {
                $i++;
            }
            $final->put($i, $team);
            $i++;
        }

        $standings = $final->sortKeys()->values();

        return view('livewire.pages.full-standings-page', [
            'standings' => $standings,
        ])->layout('layouts.app', $this->layoutData);
    }
}
