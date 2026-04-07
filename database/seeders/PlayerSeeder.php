<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\File;
use App\Models\Player;

class PlayerSeeder extends Seeder
{
    public function run(): void
    {
        $images = collect(File::files(storage_path('app/public/players')))
            ->map(fn ($file) => 'players/' . $file->getFilename())
            ->values()
            ->all();

        $players = [
            ['name' => 'Harry Kane',        'number' => 9,  'position' => 'Forward'],
            ['name' => 'Lionel Messi',      'number' => 10, 'position' => 'Forward'],
            ['name' => 'Cristiano Ronaldo', 'number' => 7,  'position' => 'Forward'],
            ['name' => 'Vion Petrov',       'number' => 6,  'position' => 'Midfielder'],
            ['name' => 'Georgi Ivanov',     'number' => 8,  'position' => 'Midfielder'],
            ['name' => 'Petar Stoyanov',    'number' => 5,  'position' => 'Defender'],
            ['name' => 'Todor Hristov',     'number' => 4,  'position' => 'Defender'],
            ['name' => 'Aleks Petkov',      'number' => 3,  'position' => 'Defender'],
            ['name' => 'Ivan Georgiev',     'number' => 2,  'position' => 'Right Back'],
            ['name' => 'Dimitar Spasov',    'number' => 11, 'position' => 'Left Back'],
            ['name' => 'Kaloyan Mihaylov',  'number' => 1,  'position' => 'Goalkeeper'],
            ['name' => 'Martin Kolev',      'number' => 12, 'position' => 'Goalkeeper'],
            ['name' => 'Stefan Yordanov',   'number' => 13, 'position' => 'Right Back'],
            ['name' => 'Aleksandar Marinov','number' => 14, 'position' => 'Defender'],
            ['name' => 'Nikolay Dimitrov',  'number' => 15, 'position' => 'Left Back'],
            ['name' => 'Bozhidar Rusev',    'number' => 16, 'position' => 'Midfielder'],
            ['name' => 'Valentin Markov',   'number' => 17, 'position' => 'Midfielder'],
            ['name' => 'Kristiyan Todorov', 'number' => 18, 'position' => 'Winger'],
            ['name' => 'Daniel Iliev',      'number' => 19, 'position' => 'Forward'],
            ['name' => 'Milen Vasilev',     'number' => 20, 'position' => 'Forward'],
            ['name' => 'Tihomir Genchev',   'number' => 21, 'position' => 'Midfielder'],
            ['name' => 'Rumen Nikolov',     'number' => 22, 'position' => 'Winger'],
        ];

        foreach ($players as $i => $data) {
            $image = $images[$i % count($images)] ?? null;

            Player::updateOrCreate(
                ['name' => $data['name']],
                [
                    'number'     => $data['number'],
                    'position'   => $data['position'],
                    'team_id'    => 1,
                    'image_path' => $image,
                ]
            );
        }
    }
}
