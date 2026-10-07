<?php

declare(strict_types=1);

use Horoscope\Horoscope\HoroscopeResult;

it('exposes the five category scores', function () {
    $result = new HoroscopeResult(love: 43, career: 80, money: 51, health: 66, social: 22);

    expect($result->love)->toBe(43)
        ->and($result->career)->toBe(80)
        ->and($result->money)->toBe(51)
        ->and($result->health)->toBe(66)
        ->and($result->social)->toBe(22);
});

it('calculates the overall score as the rounded average', function () {
    $result = new HoroscopeResult(love: 43, career: 80, money: 51, health: 66, social: 22);

    expect($result->overall)->toBe(52);
});

it('converts to an array including the overall score', function () {
    $result = new HoroscopeResult(love: 43, career: 80, money: 51, health: 66, social: 22);

    expect($result->toArray())->toBe([
        'love' => 43,
        'career' => 80,
        'money' => 51,
        'health' => 66,
        'social' => 22,
        'overall' => 52,
    ]);
});

it('round-trips between array and object', function () {
    $result = new HoroscopeResult(love: 10, career: 20, money: 30, health: 40, social: 50);

    expect(HoroscopeResult::fromArray($result->toArray())->toArray())->toBe($result->toArray());
});

it('serializes to json', function () {
    $result = new HoroscopeResult(love: 43, career: 80, money: 51, health: 66, social: 22);

    expect(json_decode($result->toJson(), true))->toBe($result->toArray());
});
