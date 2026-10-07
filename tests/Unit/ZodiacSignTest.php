<?php

declare(strict_types=1);

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

it('returns the translated label of a sign', function (ZodiacSign $sign, string $label) {
    expect($sign->label())->toBe($label);
})->with([
    [ZodiacSign::Aries, 'Aries'],
    [ZodiacSign::Virgo, 'Virgo'],
    [ZodiacSign::Pisces, 'Pisces'],
]);
