<?php

declare(strict_types=1);

namespace Horoscope\Horoscope;

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
     * The translated, human readable name of the sign.
     */
    public function label(): string
    {
        return trans('horoscope::zodiac.'.$this->value);
    }
}
