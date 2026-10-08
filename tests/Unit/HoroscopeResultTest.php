<?php

declare(strict_types=1);

use Horoscope\Horoscope\HoroscopeResult;
use Horoscope\Horoscope\LuckyColor;
use Horoscope\Horoscope\ZodiacSign;

it('exposes the five category scores', function () {
    $result = new HoroscopeResult(
        love: 43,
        career: 80,
        money: 51,
        health: 66,
        social: 22,
        match: ZodiacSign::Virgo,
        luckyNumber: 42,
        luckyColor: new LuckyColor(name: 'gold', hex: '#FFD700'),
    );

    expect($result->love)->toBe(43)
        ->and($result->career)->toBe(80)
        ->and($result->money)->toBe(51)
        ->and($result->health)->toBe(66)
        ->and($result->social)->toBe(22);
});

it('exposes the match, lucky number, and lucky color', function () {
    $color = new LuckyColor(name: 'turquoise', hex: '#40E0D0');
    $result = new HoroscopeResult(
        love: 43,
        career: 80,
        money: 51,
        health: 66,
        social: 22,
        match: ZodiacSign::Virgo,
        luckyNumber: 7,
        luckyColor: $color,
    );

    expect($result->match)->toBe(ZodiacSign::Virgo)
        ->and($result->match->value)->toBe('virgo')
        ->and($result->luckyNumber)->toBe(7)
        ->and($result->luckyColor)->toBe($color)
        ->and($result->luckyColor->name)->toBe('turquoise')
        ->and($result->luckyColor->hex)->toBe('#40E0D0');
});

it('calculates the overall score as the rounded average', function () {
    $result = new HoroscopeResult(
        love: 43,
        career: 80,
        money: 51,
        health: 66,
        social: 22,
        match: ZodiacSign::Virgo,
        luckyNumber: 42,
        luckyColor: new LuckyColor(name: 'gold', hex: '#FFD700'),
    );

    expect($result->overall)->toBe(52);
});

it('converts to an array including the overall score, match, and lucky values', function () {
    $result = new HoroscopeResult(
        love: 43,
        career: 80,
        money: 51,
        health: 66,
        social: 22,
        match: ZodiacSign::Virgo,
        luckyNumber: 42,
        luckyColor: new LuckyColor(name: 'gold', hex: '#FFD700'),
    );

    expect($result->toArray())->toBe([
        'love' => 43,
        'career' => 80,
        'money' => 51,
        'health' => 66,
        'social' => 22,
        'overall' => 52,
        'match' => 'virgo',
        'lucky_number' => 42,
        'lucky_color' => ['name' => 'gold', 'hex' => '#FFD700'],
    ]);
});

it('round-trips between array and object', function () {
    $result = new HoroscopeResult(
        love: 10,
        career: 20,
        money: 30,
        health: 40,
        social: 50,
        match: ZodiacSign::Leo,
        luckyNumber: 13,
        luckyColor: new LuckyColor(name: 'navy', hex: '#000080'),
    );

    $restored = HoroscopeResult::fromArray($result->toArray());

    expect($restored->toArray())->toBe($result->toArray())
        ->and($restored->match)->toBe(ZodiacSign::Leo)
        ->and($restored->luckyColor->name)->toBe('navy')
        ->and($restored->luckyColor->hex)->toBe('#000080');
});

it('builds a reading from raw array values', function () {
    $result = HoroscopeResult::fromArray([
        'love' => 10,
        'career' => 20,
        'money' => 30,
        'health' => 40,
        'social' => 50,
        'match' => 'pisces',
        'lucky_number' => 99,
        'lucky_color' => ['name' => 'pink', 'hex' => '#FFC0CB'],
    ]);

    expect($result->match)->toBe(ZodiacSign::Pisces)
        ->and($result->luckyNumber)->toBe(99)
        ->and($result->luckyColor)->toBeInstanceOf(LuckyColor::class)
        ->and($result->luckyColor->name)->toBe('pink');
});

it('serializes to json', function () {
    $result = new HoroscopeResult(
        love: 43,
        career: 80,
        money: 51,
        health: 66,
        social: 22,
        match: ZodiacSign::Virgo,
        luckyNumber: 42,
        luckyColor: new LuckyColor(name: 'gold', hex: '#FFD700'),
    );

    expect(json_decode($result->toJson(), true))->toBe($result->toArray());
});
