<?php

namespace App\Models;

use App\Support\Season;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Str;

class FootballMatch extends Model
{
    protected $table = 'football_matches';

    protected $guarded = ['id'];

    protected $casts = [
        'match_datetime' => 'datetime',
        'is_finished' => 'boolean',
        'started_at' => 'datetime',
        'stopped_at' => 'datetime',
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
                'label' => '⏳ Започва след '.$now->diffForHumans($start, ['parts' => 1, 'short' => true]),
                'class' => 'text-yellow-600',
            ];
        }

        return [
            'label' => '✅ Приключил '.$start->diffForHumans($now, ['parts' => 1, 'short' => true]),
            'class' => 'text-gray-500',
        ];
    }

    protected static function booted(): void
    {
        static::creating(function ($match) {
            $match->generateSlug();

            if (empty($match->season) && $match->match_datetime) {
                $match->season = Season::fromDate($match->match_datetime);
            }
        });

        static::updating(function ($match) {

            if ($match->isDirty(['home_team_id', 'away_team_id', 'match_datetime'])) {
                $match->generateSlug();
            }

            // Re-derive the season from the date when the date changed, but never
            // overwrite a season the admin has set explicitly in the same save.
            if ($match->isDirty('match_datetime') && ! $match->isDirty('season') && $match->match_datetime) {
                $match->season = Season::fromDate($match->match_datetime);
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

    public function scopeForSeason(Builder $query, string $season): Builder
    {
        return $query->where('season', $season);
    }
}
