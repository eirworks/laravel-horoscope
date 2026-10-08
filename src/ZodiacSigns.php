<?php

declare(strict_types=1);

namespace Horoscope\Horoscope;

/**
 * The catalogue of the twelve zodiac signs.
 */
final class ZodiacSigns
{
    /**
     * The emoji icon of each sign, keyed by codename.
     *
     * @var array<string, string>
     */
    private const array ICONS = [
        'aries' => '♈',
        'taurus' => '♉',
        'gemini' => '♊',
        'cancer' => '♋',
        'leo' => '♌',
        'virgo' => '♍',
        'libra' => '♎',
        'scorpio' => '♏',
        'sagittarius' => '♐',
        'capricorn' => '♑',
        'aquarius' => '♒',
        'pisces' => '♓',
    ];

    /**
     * The inclusive date range of each sign, keyed by codename.
     *
     * @var array<string, array{start: string, end: string}>
     */
    private const array RANGES = [
        'aries' => ['start' => '03-21', 'end' => '04-19'],
        'taurus' => ['start' => '04-20', 'end' => '05-20'],
        'gemini' => ['start' => '05-21', 'end' => '06-20'],
        'cancer' => ['start' => '06-21', 'end' => '07-22'],
        'leo' => ['start' => '07-23', 'end' => '08-22'],
        'virgo' => ['start' => '08-23', 'end' => '09-22'],
        'libra' => ['start' => '09-23', 'end' => '10-22'],
        'scorpio' => ['start' => '10-23', 'end' => '11-21'],
        'sagittarius' => ['start' => '11-22', 'end' => '12-21'],
        'capricorn' => ['start' => '12-22', 'end' => '01-19'],
        'aquarius' => ['start' => '01-20', 'end' => '02-18'],
        'pisces' => ['start' => '02-19', 'end' => '03-20'],
    ];

    /**
     * The classical element of each sign, keyed by codename.
     *
     * @var array<string, ZodiacElement>
     */
    private const array ELEMENTS = [
        'aries' => ZodiacElement::Fire,
        'taurus' => ZodiacElement::Earth,
        'gemini' => ZodiacElement::Air,
        'cancer' => ZodiacElement::Water,
        'leo' => ZodiacElement::Fire,
        'virgo' => ZodiacElement::Earth,
        'libra' => ZodiacElement::Air,
        'scorpio' => ZodiacElement::Water,
        'sagittarius' => ZodiacElement::Fire,
        'capricorn' => ZodiacElement::Earth,
        'aquarius' => ZodiacElement::Air,
        'pisces' => ZodiacElement::Water,
    ];

    /**
     * Get the data of every zodiac sign, in calendar order.
     *
     * @return list<ZodiacSignData>
     */
    public static function all(): array
    {
        return array_map(self::get(...), ZodiacSign::cases());
    }

    /**
     * Get the data of the given zodiac sign.
     */
    public static function get(ZodiacSign $sign): ZodiacSignData
    {
        return new ZodiacSignData(
            sign: $sign,
            name: $sign->label(),
            codename: $sign->value,
            icon: self::ICONS[$sign->value],
            startDate: self::RANGES[$sign->value]['start'],
            endDate: self::RANGES[$sign->value]['end'],
            element: self::ELEMENTS[$sign->value],
        );
    }
}
