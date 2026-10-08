<?php

declare(strict_types=1);

namespace Horoscope\Horoscope;

use DateTimeInterface;
use InvalidArgumentException;

/**
 * The twelve zodiac signs.
 */
enum ZodiacSign: string
{
    case Aries = 'aries';
    case Taurus = 'taurus';
    case Gemini = 'gemini';
    case Cancer = 'cancer';
    case Leo = 'leo';
    case Virgo = 'virgo';
    case Libra = 'libra';
    case Scorpio = 'scorpio';
    case Sagittarius = 'sagittarius';
    case Capricorn = 'capricorn';
    case Aquarius = 'aquarius';
    case Pisces = 'pisces';

    /**
     * Resolve a sign from its name, ignoring case and surrounding whitespace.
     *
     * @throws InvalidArgumentException when the name is not a known sign.
     */
    public static function fromName(string $name): self
    {
        return self::tryFrom(mb_strtolower(trim($name)))
            ?? throw new InvalidArgumentException(sprintf('Unknown zodiac sign [%s].', $name));
    }

    /**
     * Resolve the sign that contains the given date.
     *
     * The western zodiac ranges are used, so 21 March is the first day of
     * Aries and 22 December the first day of Capricorn.
     */
    public static function fromDate(DateTimeInterface $date): self
    {
        // Zero padded month and day, compared as a single number: 0321, 1222.
        $monthDay = (int) $date->format('md');

        return match (true) {
            $monthDay >= 321 && $monthDay <= 419 => self::Aries,
            $monthDay >= 420 && $monthDay <= 520 => self::Taurus,
            $monthDay >= 521 && $monthDay <= 620 => self::Gemini,
            $monthDay >= 621 && $monthDay <= 722 => self::Cancer,
            $monthDay >= 723 && $monthDay <= 822 => self::Leo,
            $monthDay >= 823 && $monthDay <= 922 => self::Virgo,
            $monthDay >= 923 && $monthDay <= 1022 => self::Libra,
            $monthDay >= 1023 && $monthDay <= 1121 => self::Scorpio,
            $monthDay >= 1122 && $monthDay <= 1221 => self::Sagittarius,
            $monthDay >= 1222 || $monthDay <= 119 => self::Capricorn,
            $monthDay <= 218 => self::Aquarius,
            default => self::Pisces,
        };
    }

    /**
     * The translated, human readable name of the sign.
     */
    public function label(): string
    {
        return trans('horoscope::zodiac.'.$this->value);
    }
}
