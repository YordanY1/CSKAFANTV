<?php

namespace App\Support;

use App\Models\FootballMatch;
use Carbon\Carbon;
use DateTimeInterface;

/**
 * Central helper for everything "season" related.
 *
 * A season is labelled "YYYY-YYYY" and runs from June of one year to May of the
 * next (START_MONTH). The very first season the club tracks is 2025-2026: any
 * older data is folded into it, so no earlier season is ever shown. The season
 * the current date falls into is the "active" one (shown on the live pages);
 * every completed season before it lives only in the archive.
 */
class Season
{
    /**
     * Calendar month (1-12) on which a new season starts. June = 6, so the
     * cutover happens during the summer break between campaigns.
     */
    public const START_MONTH = 6;

    /**
     * The earliest season the site tracks. Everything older is folded into it.
     */
    public const FIRST = '2025-2026';

    /**
     * Derive the season label from any date/time (floored to FIRST).
     */
    public static function fromDate(DateTimeInterface|string $date): string
    {
        $date = $date instanceof DateTimeInterface ? Carbon::instance(Carbon::parse($date)) : Carbon::parse($date);

        return self::fromYearMonth((int) $date->year, (int) $date->month);
    }

    /**
     * Derive the season label from a year + month pair (floored to FIRST).
     */
    public static function fromYearMonth(int $year, int $month): string
    {
        $startYear = $month >= self::START_MONTH ? $year : $year - 1;

        return self::floor($startYear.'-'.($startYear + 1));
    }

    /**
     * Never return a season older than FIRST.
     */
    public static function floor(string $season): string
    {
        return $season < self::FIRST ? self::FIRST : $season;
    }

    /**
     * The active season — the one the current date falls into.
     */
    public static function current(): string
    {
        return self::fromDate(Carbon::now());
    }

    /**
     * Whether a string looks like a valid season label ("2025-2026").
     */
    public static function isValid(string $season): bool
    {
        return (bool) preg_match('/^\d{4}-\d{4}$/', $season);
    }

    /**
     * Human friendly label, e.g. "Сезон 2025-2026".
     */
    public static function label(string $season): string
    {
        return 'Сезон '.$season;
    }

    /**
     * Monotonic month-index bounds (year * 12 + month) for a season.
     * The first season starts at 0 so it absorbs every older monthly award.
     *
     * @return array{0: int, 1: int} [inclusive start, exclusive end]
     */
    public static function monthIndexBounds(string $season): array
    {
        $startYear = (int) explode('-', $season)[0];

        return [
            $season === self::FIRST ? 0 : $startYear * 12 + self::START_MONTH,
            ($startYear + 1) * 12 + self::START_MONTH,
        ];
    }

    /**
     * Archived (completed) seasons that have data — newest first.
     * Excludes the active season, which lives on the live pages.
     *
     * @return array<int, string>
     */
    public static function all(): array
    {
        $current = self::current();

        return FootballMatch::query()
            ->whereNotNull('season')
            ->distinct()
            ->orderByDesc('season')
            ->pluck('season')
            ->filter(fn (string $season) => $season >= self::FIRST && $season < $current)
            ->values()
            ->all();
    }

    /**
     * The most recent archived season, falling back to FIRST.
     */
    public static function latestArchived(): string
    {
        return self::all()[0] ?? self::FIRST;
    }

    /**
     * Archived season options for Filament archive filters.
     *
     * @return array<string, string>
     */
    public static function options(): array
    {
        $seasons = self::all() ?: [self::FIRST];

        return collect($seasons)
            ->mapWithKeys(fn (string $season) => [$season => self::label($season)])
            ->all();
    }

    /**
     * Season options for the match form: from the first season up to the season
     * after the active one (so a new match can be marked for the right season),
     * plus any season already stored. Newest first, defaulting to the current.
     *
     * @return array<string, string>
     */
    public static function formOptions(): array
    {
        $firstStartYear = (int) explode('-', self::FIRST)[0];
        $currentStartYear = (int) explode('-', self::current())[0];

        $seasons = collect();
        for ($year = $firstStartYear; $year <= $currentStartYear + 1; $year++) {
            $seasons->push($year.'-'.($year + 1));
        }

        $seasons = $seasons->merge(
            FootballMatch::query()->whereNotNull('season')->distinct()->pluck('season')
        );

        return $seasons
            ->unique()
            ->sortDesc()
            ->mapWithKeys(fn (string $season) => [$season => self::label($season)])
            ->all();
    }
}
