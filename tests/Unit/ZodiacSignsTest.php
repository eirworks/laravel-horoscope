<?php

declare(strict_types=1);

use Horoscope\Horoscope\ZodiacSign;
use Horoscope\Horoscope\ZodiacSignData;
use Horoscope\Horoscope\ZodiacSigns;

it('lists every zodiac sign in calendar order', function () {
    $signs = ZodiacSigns::all();

    expect($signs)->toHaveCount(12)
        ->and($signs)->each->toBeInstanceOf(ZodiacSignData::class)
        ->and(array_map(static fn (ZodiacSignData $data): ZodiacSign => $data->sign, $signs))
        ->toBe(ZodiacSign::cases());
});

it('returns the data of a sign', function (
    ZodiacSign $sign,
    string $name,
    string $codename,
    string $icon,
    string $startDate,
    string $endDate,
) {
    $data = ZodiacSigns::get($sign);

    expect($data->sign)->toBe($sign)
        ->and($data->name)->toBe($name)
        ->and($data->codename)->toBe($codename)
        ->and($data->icon)->toBe($icon)
        ->and($data->startDate)->toBe($startDate)
        ->and($data->endDate)->toBe($endDate);
})->with([
    [ZodiacSign::Aries, 'Aries', 'aries', '♈', '03-21', '04-19'],
    [ZodiacSign::Taurus, 'Taurus', 'taurus', '♉', '04-20', '05-20'],
    [ZodiacSign::Gemini, 'Gemini', 'gemini', '♊', '05-21', '06-20'],
    [ZodiacSign::Cancer, 'Cancer', 'cancer', '♋', '06-21', '07-22'],
    [ZodiacSign::Leo, 'Leo', 'leo', '♌', '07-23', '08-22'],
    [ZodiacSign::Virgo, 'Virgo', 'virgo', '♍', '08-23', '09-22'],
    [ZodiacSign::Libra, 'Libra', 'libra', '♎', '09-23', '10-22'],
    [ZodiacSign::Scorpio, 'Scorpio', 'scorpio', '♏', '10-23', '11-21'],
    [ZodiacSign::Sagittarius, 'Sagittarius', 'sagittarius', '♐', '11-22', '12-21'],
    [ZodiacSign::Capricorn, 'Capricorn', 'capricorn', '♑', '12-22', '01-19'],
    [ZodiacSign::Aquarius, 'Aquarius', 'aquarius', '♒', '01-20', '02-18'],
    [ZodiacSign::Pisces, 'Pisces', 'pisces', '♓', '02-19', '03-20'],
]);

it('uses a downcased alphanumeric dash codename', function () {
    foreach (ZodiacSigns::all() as $data) {
        expect($data->codename)->toMatch('/^[a-z0-9]+(?:-[a-z0-9]+)*$/');
    }
});

it('converts the data of a sign to an array', function () {
    expect(ZodiacSigns::get(ZodiacSign::Virgo)->toArray())->toBe([
        'sign' => 'virgo',
        'name' => 'Virgo',
        'codename' => 'virgo',
        'icon' => '♍',
        'start_date' => '08-23',
        'end_date' => '09-22',
    ]);
});

it('serializes the data of a sign to json', function () {
    $data = ZodiacSigns::get(ZodiacSign::Virgo);

    expect(json_decode($data->toJson(), true))->toBe($data->toArray())
        ->and(json_decode(json_encode($data, JSON_THROW_ON_ERROR), true))->toBe($data->toArray());
});

it('uses date ranges that match the date resolution', function () {
    $year = 2026;

    foreach (ZodiacSigns::all() as $data) {
        expect(ZodiacSign::fromDate(new DateTimeImmutable($year.'-'.$data->startDate)))->toBe($data->sign)
            ->and(ZodiacSign::fromDate(new DateTimeImmutable($year.'-'.$data->endDate)))->toBe($data->sign);
    }
});
