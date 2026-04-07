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
            $directReds = $this->direct_red_note ? 1 : 0;
            return $directReds + (int) ($this->second_yellow_reds ?? 0);
        });
    }

    protected function hasDirectRed(): Attribute
    {
        return Attribute::get(fn () => !empty($this->direct_red_note));
    }
}
