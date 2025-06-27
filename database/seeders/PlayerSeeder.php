<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Player;

class PlayerSeeder extends Seeder
{
    public function run(): void
    {
        $players = [
            ['name' => 'Harry Kane',        'number' => 9,  'position' => 'Forward',     'image' => 'kane.png'],
            ['name' => 'Lionel Messi',      'number' => 10, 'position' => 'Forward',     'image' => 'messi.png'],
            ['name' => 'Cristiano Ronaldo', 'number' => 7,  'position' => 'Forward',     'image' => 'ronaldo.png'],
            ['name' => 'Vion Petrov',       'number' => 6,  'position' => 'Midfielder',  'image' => 'vion.png'],
            ['name' => 'Georgi Ivanov',     'number' => 8,  'position' => 'Midfielder',  'image' => 'messi.png'],
            ['name' => 'Petar Stoyanov',    'number' => 5,  'position' => 'Defender',    'image' => 'ronaldo.png'],
            ['name' => 'Todor Hristov',     'number' => 4,  'position' => 'Defender',    'image' => 'kane.png'],
            ['name' => 'Aleks Petkov',      'number' => 3,  'position' => 'Defender',    'image' => 'vion.png'],
            ['name' => 'Ivan Georgiev',     'number' => 2,  'position' => 'Right Back',  'image' => 'ronaldo.png'],
            ['name' => 'Dimitar Spasov',    'number' => 11, 'position' => 'Left Back',   'image' => 'messi.png'],
            ['name' => 'Kaloyan Mihaylov',  'number' => 1,  'position' => 'Goalkeeper',  'image' => 'kane.png'],

            ['name' => 'Martin Kolev',      'number' => 12, 'position' => 'Goalkeeper',  'image' => 'vion.png'],
            ['name' => 'Stefan Yordanov',   'number' => 13, 'position' => 'Right Back',  'image' => 'ronaldo.png'],
            ['name' => 'Aleksandar Marinov', 'number' => 14, 'position' => 'Defender',    'image' => 'kane.png'],
            ['name' => 'Nikolay Dimitrov',  'number' => 15, 'position' => 'Left Back',   'image' => 'messi.png'],
            ['name' => 'Bozhidar Rusev',    'number' => 16, 'position' => 'Midfielder',  'image' => 'ronaldo.png'],
            ['name' => 'Valentin Markov',   'number' => 17, 'position' => 'Midfielder',  'image' => 'vion.png'],
            ['name' => 'Kristiyan Todorov', 'number' => 18, 'position' => 'Winger',      'image' => 'kane.png'],
            ['name' => 'Daniel Iliev',      'number' => 19, 'position' => 'Forward',     'image' => 'messi.png'],
            ['name' => 'Milen Vasilev',     'number' => 20, 'position' => 'Forward',     'image' => 'ronaldo.png'],
            ['name' => 'Tihomir Genchev',   'number' => 21, 'position' => 'Midfielder',  'image' => 'kane.png'],
            ['name' => 'Rumen Nikolov',     'number' => 22, 'position' => 'Winger',      'image' => 'vion.png'],
        ];

        foreach ($players as $data) {
            Player::create([
                'name'       => $data['name'],
                'number'     => $data['number'],
                'position'   => $data['position'],
                'team_id'    => 1,
                'image_path' => 'images/players/' . $data['image'],
            ]);
        }
    }
}
