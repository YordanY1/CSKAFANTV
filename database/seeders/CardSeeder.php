<?php

namespace Database\Seeders;

use App\Models\Card;
use App\Models\Player;
use Illuminate\Database\Seeder;

class CardSeeder extends Seeder
{
    public function run(): void
    {
        $players = Player::all();

        if ($players->isEmpty()) {
            $this->command->warn('No players found. Run PlayerSeeder first.');
            return;
        }

        $cardsData = [
            ['player_index' => 0, 'yellow_cards' => 7, 'second_yellow_reds' => 1, 'direct_red_note' => null],
            ['player_index' => 1, 'yellow_cards' => 5, 'second_yellow_reds' => 0, 'direct_red_note' => 'Директен червен за грубо влизане срещу Лудогорец (15-и кръг)'],
            ['player_index' => 2, 'yellow_cards' => 3, 'second_yellow_reds' => 0, 'direct_red_note' => null],
            ['player_index' => 3, 'yellow_cards' => 6, 'second_yellow_reds' => 0, 'direct_red_note' => null],
            ['player_index' => 4, 'yellow_cards' => 4, 'second_yellow_reds' => 1, 'direct_red_note' => null],
            ['player_index' => 5, 'yellow_cards' => 8, 'second_yellow_reds' => 2, 'direct_red_note' => 'Директен червен за удар с лакът срещу Левски (22-и кръг)'],
            ['player_index' => 6, 'yellow_cards' => 2, 'second_yellow_reds' => 0, 'direct_red_note' => null],
            ['player_index' => 7, 'yellow_cards' => 3, 'second_yellow_reds' => 0, 'direct_red_note' => 'Фаул на последния защитник срещу Ботев Пловдив (8-и кръг)'],
            ['player_index' => 8, 'yellow_cards' => 1, 'second_yellow_reds' => 0, 'direct_red_note' => null],
            ['player_index' => 9, 'yellow_cards' => 4, 'second_yellow_reds' => 1, 'direct_red_note' => null],
        ];

        foreach ($cardsData as $data) {
            if (!isset($players[$data['player_index']])) {
                continue;
            }

            Card::updateOrCreate(
                ['player_id' => $players[$data['player_index']]->id],
                [
                    'yellow_cards' => $data['yellow_cards'],
                    'second_yellow_reds' => $data['second_yellow_reds'],
                    'direct_red_note' => $data['direct_red_note'],
                ]
            );
        }
    }
}
