<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Spatie\Sitemap\Sitemap;
use Spatie\Sitemap\Tags\Url;
use App\Models\FootballMatch;
use App\Models\Video;

class GenerateSitemap extends Command
{
    protected $signature = 'generate:sitemap';
    protected $description = 'Генерира sitemap.xml само със страници, които имат съдържание.';

    public function handle(): void
    {
        $sitemap = Sitemap::create();

        $staticRoutes = [
            ['url' => '/', 'priority' => 1.0],
            ['url' => '/matches', 'priority' => 0.9],
            ['url' => '/players', 'priority' => 0.9],
            ['url' => '/videos', 'priority' => 0.8],
            ['url' => '/tactics', 'priority' => 0.7],
            ['url' => '/hall-of-fame', 'priority' => 0.7],
            ['url' => '/contact', 'priority' => 0.6],
            ['url' => '/cards', 'priority' => 0.6],
            ['url' => '/standings', 'priority' => 0.5],
            ['url' => '/player-ratings', 'priority' => 0.5],
            ['url' => '/predictions/rankings', 'priority' => 0.5],
            ['url' => '/privacy-policy', 'priority' => 0.3],
            ['url' => '/cookie-policy', 'priority' => 0.3],
        ];


        foreach ($staticRoutes as $route) {
            $sitemap->add(Url::create($route['url'])->setPriority($route['priority']));
        }

        if (FootballMatch::count() > 0) {
            foreach (FootballMatch::all() as $match) {
                $sitemap->add(
                    Url::create(route('match.show', ['match' => $match->slug], false))
                        ->setLastModificationDate($match->updated_at ?? now())
                        ->setPriority(0.8)
                );
            }
        }

        $categories = Video::select('category_slug')
            ->groupBy('category_slug')
            ->havingRaw('count(*) > 0')
            ->pluck('category_slug');

        foreach ($categories as $slug) {
            $sitemap->add(
                Url::create(route('videos.category', ['slug' => $slug], false))
                    ->setPriority(0.6)
            );
        }

        $sitemap->writeToFile(public_path('sitemap.xml'));

        $this->info('✅ sitemap.xml е създаден.');
    }
}
