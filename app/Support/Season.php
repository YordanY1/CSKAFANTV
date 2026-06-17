<?php

namespace App\Support;

use App\Models\FootballMatch;
use Carbon\Carbon;
use DateTimeInterface;

/**
 * Central helper for everything "season" related.
 *
 * A Bulgarian football season runs from July of one year to June of the next,
 * so it is labelled "YYYY-YYYY" (e.g. "2025-2026"). This class is the single
 * source of truth used by the front-end archive pages, the navigation menu and
 * the Filament admin resources.
 */
class Season
{
    /**
     * Calendar month (1-12) on which a new season starts. July = 7.
     */
    public const START_MONTH = 7;

    /**
     * Derive the season label from any date/time.
     */
    public static function fromDate(DateTimeInterface|string $date): string
    {
        $date = $date instanceof DateTimeInterface ? Carbon::instance(Carbon::parse($date)) : Carbon::parse($date);

        return self::fromYearMonth((int) $date->year, (int) $date->month);
    }

    /**
     * Derive the season label from a year + month pair (used for monthly awards).
     */
    public static function fromYearMonth(int $year, int $month): string
    {
        $startYear = $month >= self::START_MONTH ? $year : $year - 1;

        return $startYear.'-'.($startYear + 1);
    }

    /**
     * The season the current date falls into.
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
     * Start (inclusive) and end (exclusive) datetimes for a season label.
     *
     * @return array{0: Carbon, 1: Carbon}
     */
    public static function range(string $season): array
    {
        $startYear = (int) explode('-', $season)[0];
        $start = Carbon::create($startYear, self::START_MONTH, 1, 0, 0, 0);

        return [$start, $start->copy()->addYear()];
    }

    /**
     * Monotonic month-index bounds (year * 12 + month) for a season.
     * Useful for filtering tables that only store a year + month (monthly awards).
     *
     * @return array{0: int, 1: int} [inclusive start, exclusive end]
     */
    public static function monthIndexBounds(string $season): array
    {
        $startYear = (int) explode('-', $season)[0];

        return [
            $startYear * 12 + self::START_MONTH,
            ($startYear + 1) * 12 + self::START_MONTH,
        ];
    }

    /**
     * All seasons that have at least one match, newest first.
     *
     * @return array<int, string>
     */
    public static function all(): array
    {
        return FootballMatch::query()
            ->whereNotNull('season')
            ->distinct()
            ->orderByDesc('season')
            ->pluck('season')
            ->all();
    }

    /**
     * The most recent season that actually has data, falling back to the
     * current calendar season when the archive is still empty.
     */
    public static function latest(): string
    {
        return self::all()[0] ?? self::current();
    }

    /**
     * Season options for Filament selects/filters: ["2025-2026" => "Сезон 2025-2026", ...].
     *
     * @return array<string, string>
     */
    public static function options(): array
    {
        $seasons = collect(self::all());

        // Make sure recent seasons are always available even before any match exists.
        $currentStart = (int) explode('-', self::current())[0];
        for ($year = $currentStart - 2; $year <= $currentStart + 1; $year++) {
            $seasons->push($year.'-'.($year + 1));
        }

        return $seasons
            ->unique()
            ->sortDesc()
            ->mapWithKeys(fn (string $season) => [$season => self::label($season)])
            ->all();
    }
}
