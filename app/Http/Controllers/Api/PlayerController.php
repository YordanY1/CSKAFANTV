<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Player;

class PlayerController extends Controller
{
    public function index()
    {
        return Player::query()
            ->where('is_coach', false)
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
            // In PHP because SQL LIKE/LOWER are not reliably
            // case-insensitive for Cyrillic across drivers.
            ->reject(function ($player) {
                $position = mb_strtolower($player->position ?? '');

                return str_contains($position, 'треньор')
                    || str_contains($position, 'анализатор');
            })
            ->values()
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
                    'image_thumb_path' => $player->image_thumb_path,
                ];
            });
    }
}
