<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Casts\Attribute;
use App\Models\Team;
use App\Models\PlayerReview;
use Illuminate\Support\Str;


class FootballMatch extends Model
{
    protected $table = 'football_matches';
    protected $guarded = ['id'];
    protected $casts = [
        'match_datetime' => 'datetime',
        'is_finished' => 'boolean',
    ];

    public function homeTeam(): BelongsTo
    {
        return $this->belongsTo(Team::class, 'home_team_id');
    }

    public function awayTeam(): BelongsTo
    {
        return $this->belongsTo(Team::class, 'away_team_id');
    }

    public function reviews()
    {
        return $this->hasMany(PlayerReview::class);
    }

    public function lineup()
    {
        return $this->hasMany(MatchLineup::class);
    }

    public function getMatchStatusDataAttribute(): array
    {
        $now = now();
        $start = $this->match_datetime;

        if (! $this->is_finished) {
            return [
                'label' => '⏳ Започва след ' . $now->diffForHumans($start, ['parts' => 1, 'short' => true]),
                'class' => 'text-yellow-600',
            ];
        }

        return [
            'label' => '✅ Приключил ' . $start->diffForHumans($now, ['parts' => 1, 'short' => true]),
            'class' => 'text-gray-500',
        ];
    }

    protected static function booted(): void
    {
        static::creating(function ($match) {
            $match->generateSlug();
        });

        static::updating(function ($match) {

            if ($match->isDirty(['home_team_id', 'away_team_id', 'match_datetime'])) {
                $match->generateSlug();
            }
        });

        static::saved(function (FootballMatch $match) {
            if ($match->is_finished && $match->home_score !== null && $match->away_score !== null) {
                \Artisan::call('predictions:calculate-points');
            }
        });
    }

    public function generateSlug(): void
    {
        $home = optional($this->homeTeam)->name ?? 'home';
        $away = optional($this->awayTeam)->name ?? 'away';
        $date = optional($this->match_datetime)?->format('Y-m-d') ?? now()->format('Y-m-d');

        $this->slug = Str::slug("{$home}-vs-{$away}-{$date}");

        $originalSlug = $this->slug;
        $counter = 1;

        while (static::where('slug', $this->slug)->where('id', '!=', $this->id)->exists()) {
            $this->slug = "{$originalSlug}-{$counter}";
            $counter++;
        }
    }

    public function getRouteKeyName(): string
    {
        return 'slug';
    }
}
