<?php

namespace App\Livewire\Pages;

use Livewire\Component;
use Livewire\WithPagination;
use App\Services\LiveScoreService;

class FullStandingsPage extends Component
{
    use WithPagination;

    public $search = '';
    public $sortColumn = 'rank'; // 🔹 По подразбиране сортираме по rank (позиция)
    public $sortDirection = 'asc'; // 🔹 Най-отгоре са първите отбори
    public string $league = 'first'; // 'first' = Първа лига, 'second' = Втора лига

    public array $layoutData = [];

    public function mount(): void
    {
        $this->layoutData = [
            'title'           => 'Класиране – Таблица на отборите | CSKA FAN TV',
            'description'     => 'Виж текущото класиране на отборите: точки, победи, загуби и форма.',
            'robots'          => 'index, follow',
            'canonical'       => url('/standings'),
            'og_title'        => 'Класиране на отборите | CSKA FAN TV',
            'og_description'  => 'Актуална таблица с класирането на всички отбори.',
            'og_image'        => asset('images/og-cska.png'),
            'og_url'          => url('/standings'),
            'og_type'         => 'website',
        ];
    }

    public function sortBy(string $column): void
    {
        if ($this->sortColumn === $column) {
            $this->sortDirection = $this->sortDirection === 'asc' ? 'desc' : 'asc';
        } else {
            $this->sortColumn = $column;
            $this->sortDirection = 'asc';
        }
    }

    public function setLeague(string $league): void
    {
        $this->league = $league;
        $this->resetPage(); // ако има пагинация
    }

    public function render(LiveScoreService $service)
    {
        $all = $this->league === 'second'
            ? collect($service->getSecondLeagueStandings())
            : collect($service->getStandingsWithTeams(71));

        if (!empty($this->search)) {
            $all = $all->filter(
                fn($item) =>
                str_contains(mb_strtolower($item['bg_name']), mb_strtolower($this->search))
            );
        }
        
        switch ($this->sortColumn) {
            case 'rank':
                $all = $all->sortBy(
                    fn($item) => (int) $item['rank'],
                    SORT_NUMERIC,
                    $this->sortDirection === 'desc'
                );
                break;

            case 'points':
                $all = $all->sort(function ($a, $b) {
                    $multiplier = $this->sortDirection === 'asc' ? 1 : -1;

                    if ((int) $a['points'] !== (int) $b['points']) {
                        return $multiplier * ((int) $a['points'] <=> (int) $b['points']);
                    }

                    if ((int) $a['goal_diff'] !== (int) $b['goal_diff']) {
                        return $multiplier * ((int) $a['goal_diff'] <=> (int) $b['goal_diff']);
                    }

                    if ((int) $a['goals_scored'] !== (int) $b['goals_scored']) {
                        return $multiplier * ((int) $a['goals_scored'] <=> (int) $b['goals_scored']);
                    }

                    return $multiplier * strcmp(
                        mb_strtolower($a['bg_name'] ?? $a['name']),
                        mb_strtolower($b['bg_name'] ?? $b['name'])
                    );
                });
                break;

            default:
                $all = $all->sortBy(function ($item) {
                    return match ($this->sortColumn) {
                        'team'      => mb_strtolower($item['bg_name'] ?? $item['name']),
                        'goal_diff' => (int) $item['goal_diff'],
                        'won'       => (int) $item['won'],
                        'drawn'     => (int) $item['drawn'],
                        'lost'      => (int) $item['lost'],
                        default     => $item[$this->sortColumn] ?? null,
                    };
                }, SORT_REGULAR, $this->sortDirection === 'desc');
                break;
        }

        $standings = $all->values();

        return view('livewire.pages.full-standings-page', [
            'standings' => $standings,
            'league'    => $this->league,
        ])->layout('layouts.app', $this->layoutData);
    }
}
