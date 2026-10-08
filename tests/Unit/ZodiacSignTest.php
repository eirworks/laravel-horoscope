<?php

declare(strict_types=1);

use Carbon\Carbon;
use Carbon\CarbonImmutable;
use Horoscope\Horoscope\ZodiacSign;

it('exposes every zodiac sign', function (ZodiacSign $sign, string $value) {
    expect($sign->value)->toBe($value);
})->with([
    [ZodiacSign::Aries, 'aries'],
    [ZodiacSign::Taurus, 'taurus'],
    [ZodiacSign::Gemini, 'gemini'],
    [ZodiacSign::Cancer, 'cancer'],
    [ZodiacSign::Leo, 'leo'],
    [ZodiacSign::Virgo, 'virgo'],
    [ZodiacSign::Libra, 'libra'],
    [ZodiacSign::Scorpio, 'scorpio'],
    [ZodiacSign::Sagittarius, 'sagittarius'],
    [ZodiacSign::Capricorn, 'capricorn'],
    [ZodiacSign::Aquarius, 'aquarius'],
    [ZodiacSign::Pisces, 'pisces'],
]);

it('resolves a sign from its name, ignoring case and whitespace', function (string $name) {
    expect(ZodiacSign::fromName($name))->toBe(ZodiacSign::Virgo);
})->with([
    'virgo',
    'Virgo',
    'VIRGO',
    '  virgo  ',
]);

it('throws for an unknown sign name', function () {
    expect(fn () => ZodiacSign::fromName('ophiuchus'))
        ->toThrow(InvalidArgumentException::class, 'Unknown zodiac sign [ophiuchus].');
});

it('resolves the sign for a date', function (string $date, ZodiacSign $sign) {
    expect(ZodiacSign::fromDate(new DateTimeImmutable($date)))->toBe($sign);
})->with([
    'first day of aries' => ['2026-03-21', ZodiacSign::Aries],
    'last day of aries' => ['2026-04-19', ZodiacSign::Aries],
    'taurus' => ['2026-05-01', ZodiacSign::Taurus],
    'gemini' => ['2026-06-15', ZodiacSign::Gemini],
    'cancer' => ['2026-07-10', ZodiacSign::Cancer],
    'leo' => ['2026-08-01', ZodiacSign::Leo],
    'virgo' => ['2026-09-05', ZodiacSign::Virgo],
    'libra' => ['2026-10-07', ZodiacSign::Libra],
    'scorpio' => ['2026-11-01', ZodiacSign::Scorpio],
    'sagittarius' => ['2026-12-01', ZodiacSign::Sagittarius],
    'first day of capricorn' => ['2026-12-22', ZodiacSign::Capricorn],
    'last day of capricorn' => ['2026-01-19', ZodiacSign::Capricorn],
    'first day of aquarius' => ['2026-01-20', ZodiacSign::Aquarius],
    'last day of aquarius' => ['2026-02-18', ZodiacSign::Aquarius],
    'pisces' => ['2026-02-19', ZodiacSign::Pisces],
    'last day of pisces' => ['2026-03-20', ZodiacSign::Pisces],
    'leap day' => ['2028-02-29', ZodiacSign::Pisces],
]);

it('resolves the sign for a carbon date', function (string $date, ZodiacSign $sign) {
    $carbon = Carbon::parse($date);

    expect(ZodiacSign::fromDate($carbon))->toBe($sign)
        ->and(ZodiacSign::fromDate(CarbonImmutable::instance($carbon)))->toBe($sign);
})->with([
    ['2026-03-21', ZodiacSign::Aries],
    ['2026-12-22', ZodiacSign::Capricorn],
    ['2026-01-19', ZodiacSign::Capricorn],
    ['2026-02-19', ZodiacSign::Pisces],
]);

it('returns the translated label of a sign', function (ZodiacSign $sign, string $label) {
    expect($sign->label())->toBe($label);
})->with([
    [ZodiacSign::Aries, 'Aries'],
    [ZodiacSign::Virgo, 'Virgo'],
    [ZodiacSign::Pisces, 'Pisces'],
]);
