<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Player;

class PlayerController extends Controller
{
    public function index()
    {
        return Player::query()
            ->whereNot('position', 'like', '%треньор%')
            ->orderByRaw("
        FIELD(position,
            'Вратар',
            'Десен бек',
            'Централен защитник',
            'Ляв бек',
            'Опорен халф',
            'Атакуващ халф',
            'Ляво крило',
            'Дясно крило',
            'Централен нападател'
        )
    ")
            ->get()
            ->map(function ($player) {
                $parts = explode(' ', trim($player->name));
                $second = $parts[1] ?? $parts[0];
                return [
                    'id' => $player->id,
                    'name' => $player->name,
                    'second_name' => $second,
                    'number' => $player->number,
                    'position' => $player->position,
                    'image_path' => $player->image_path,
                ];
            });
    }
}
