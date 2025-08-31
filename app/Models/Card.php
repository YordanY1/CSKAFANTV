<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Casts\Attribute;


class Card extends Model
{
    protected $guarded = ['id'];

    public function player()
    {
        return $this->belongsTo(Player::class);
    }

    protected function totalReds(): Attribute
    {
        return Attribute::get(function () {
            return (int) ($this->red_cards ?? 0) + (int) ($this->second_yellow_reds ?? 0);
        });
    }
}
