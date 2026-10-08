<?php

declare(strict_types=1);

use Horoscope\Horoscope\Horoscope;
use Horoscope\Horoscope\HoroscopeResult;
use Horoscope\Horoscope\ZodiacSign;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Cache;

beforeEach(function () {
    config()->set('horoscope.cache.store', 'array');
    config()->set('horoscope.cache.enabled', true);
    config()->set('horoscope.cache.ttl', 'end-of-day');

    Cache::store('array')->clear();
});

afterEach(function () {
    Carbon::setTestNow();
    Cache::store('array')->clear();
});

it('generates a reading for a zodiac sign with every category score between 1 and 100', function () {
    $result = app(Horoscope::class)->generateForSign(42, 'virgo');

    expect($result)->toBeInstanceOf(HoroscopeResult::class);

    foreach (['love', 'career', 'money', 'health', 'social'] as $stat) {
        expect($result->{$stat})->toBeGreaterThanOrEqual(1)->toBeLessThanOrEqual(100);
    }

    expect($result->overall)->toBeGreaterThanOrEqual(1)->toBeLessThanOrEqual(100);
});

it('never matches a sign reading with its own sign', function (ZodiacSign $sign) {
    config()->set('horoscope.cache.enabled', false);

    $result = app(Horoscope::class)->generateForSign(42, $sign);

    expect($result->match)->not->toBe($sign);
})->with(ZodiacSign::cases());

it('accepts a zodiac sign enum', function () {
    config()->set('horoscope.cache.enabled', false);

    $horoscope = app(Horoscope::class);

    expect($horoscope->generateForSign(42, ZodiacSign::Virgo)->toArray())
        ->toBe($horoscope->generateForSign(42, 'virgo')->toArray());
});

it('generates the same reading for the same number and sign', function () {
    config()->set('horoscope.cache.enabled', false);

    $horoscope = app(Horoscope::class);

    $first = $horoscope->generateForSign(42, 'virgo');
    $second = $horoscope->generateForSign(42, 'virgo');

    expect($second)->not->toBe($first)
        ->and($second->toArray())->toBe($first->toArray());
});

it('generates a different reading for a different number or sign', function () {
    config()->set('horoscope.cache.enabled', false);

    $horoscope = app(Horoscope::class);
    $base = $horoscope->generateForSign(42, 'virgo');

    expect($horoscope->generateForSign(43, 'virgo')->toArray())->not->toBe($base->toArray())
        ->and($horoscope->generateForSign(42, 'leo')->toArray())->not->toBe($base->toArray());
});

it('throws for an unknown sign name', function () {
    app(Horoscope::class)->generateForSign(42, 'ophiuchus');
})->throws(InvalidArgumentException::class);

it('generates the same match, lucky number, and lucky color for the same number and sign', function () {
    config()->set('horoscope.cache.enabled', false);

    $horoscope = app(Horoscope::class);

    $first = $horoscope->generateForSign(42, 'virgo');
    $second = $horoscope->generateForSign(42, 'virgo');

    expect($second->match)->toBe($first->match)
        ->and($second->luckyNumber)->toBe($first->luckyNumber)
        ->and($second->luckyColor)->toEqual($first->luckyColor);
});

it('returns the cached reading for the same number and sign', function () {
    $horoscope = app(Horoscope::class);

    $first = $horoscope->generateForSign(42, 'virgo');
    $second = $horoscope->generateForSign(42, 'virgo');

    expect($second)->toBe($first);
});

it('regenerates and replaces the cached reading when forced', function () {
    $horoscope = app(Horoscope::class);

    $first = $horoscope->generateForSign(42, 'virgo');
    $forced = $horoscope->generateForSign(42, 'virgo', force: true);

    expect($forced)->not->toBe($first)
        ->and($horoscope->generateForSign(42, 'virgo'))->toBe($forced);
});

it('does not use the cache when caching is disabled', function () {
    config()->set('horoscope.cache.enabled', false);

    $horoscope = app(Horoscope::class);

    $first = $horoscope->generateForSign(42, 'virgo');
    $second = $horoscope->generateForSign(42, 'virgo');

    expect($second)->not->toBe($first)
        ->and($second->toArray())->toBe($first->toArray());
});

it('caches the sign reading under the number and sign key until the end of the day', function () {
    $this->travelTo(new DateTimeImmutable('2026-10-07 10:00:00'));

    app(Horoscope::class)->generateForSign(42, 'virgo');

    expect(Cache::store('array')->has('horoscope:42:virgo'))->toBeTrue();

    $this->travelTo(new DateTimeImmutable('2026-10-07 23:59:59'));
    expect(Cache::store('array')->has('horoscope:42:virgo'))->toBeTrue();

    $this->travelTo(new DateTimeImmutable('2026-10-08 00:01:00'));
    expect(Cache::store('array')->has('horoscope:42:virgo'))->toBeFalse();
});

it('respects a numeric cache ttl for sign readings', function () {
    config()->set('horoscope.cache.ttl', 60);
    $this->travelTo(new DateTimeImmutable('2026-10-07 10:00:00'));

    app(Horoscope::class)->generateForSign(42, 'virgo');

    expect(Cache::store('array')->has('horoscope:42:virgo'))->toBeTrue();

    $this->travelTo(new DateTimeImmutable('2026-10-07 10:01:01'));
    expect(Cache::store('array')->has('horoscope:42:virgo'))->toBeFalse();
});

it('caches the sign reading forever when the ttl is null', function () {
    config()->set('horoscope.cache.ttl', null);
    $this->travelTo(new DateTimeImmutable('2026-10-07 10:00:00'));

    app(Horoscope::class)->generateForSign(42, 'virgo');

    $this->travelTo(new DateTimeImmutable('2030-10-07 10:00:00'));
    expect(Cache::store('array')->has('horoscope:42:virgo'))->toBeTrue();
});
