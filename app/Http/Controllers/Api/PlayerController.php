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
            ->get();
    }
}
