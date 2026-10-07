<?php

declare(strict_types=1);

namespace Horoscope\Horoscope;

/**
 * The qualitative band a numeric horoscope score falls into.
 */
enum ScoreLevel: string
{
    case Terrible = 'terrible';
    case Bad = 'bad';
    case Normal = 'normal';
    case Good = 'good';
    case Excellent = 'excellent';

    /**
     * Resolve the level for a numeric score between 1 and 100.
     */
    public static function fromScore(int $score): self
    {
        return match (true) {
            $score <= 20 => self::Terrible,
            $score <= 40 => self::Bad,
            $score <= 60 => self::Normal,
            $score <= 80 => self::Good,
            default => self::Excellent,
        };
    }

    /**
     * The lowest score included in the level.
     */
    public function minimum(): int
    {
        return match ($this) {
            self::Terrible => 1,
            self::Bad => 21,
            self::Normal => 41,
            self::Good => 61,
            self::Excellent => 81,
        };
    }

    /**
     * The highest score included in the level.
     */
    public function maximum(): int
    {
        return match ($this) {
            self::Terrible => 20,
            self::Bad => 40,
            self::Normal => 60,
            self::Good => 80,
            self::Excellent => 100,
        };
    }

    /**
     * Determine whether the given score falls into this level.
     */
    public function contains(int $score): bool
    {
        return $score >= $this->minimum() && $score <= $this->maximum();
    }

    /**
     * The translated, human readable name of the level.
     */
    public function label(): string
    {
        return trans('horoscope::luck.levels.'.$this->value);
    }
}
