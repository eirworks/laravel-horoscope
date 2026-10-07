<?php

declare(strict_types=1);

use Horoscope\Horoscope\Horoscope;
use Horoscope\Horoscope\HoroscopeResult;
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

it('generates a reading with every category score between 1 and 100', function () {
    $result = app(Horoscope::class)->generate(42, '2026-10-07');

    expect($result)->toBeInstanceOf(HoroscopeResult::class);

    foreach ($result->toArray() as $score) {
        expect($score)->toBeGreaterThanOrEqual(1)->toBeLessThanOrEqual(100);
    }
});

it('generates the same reading for the same number and date', function () {
    config()->set('horoscope.cache.enabled', false);

    $horoscope = app(Horoscope::class);

    $first = $horoscope->generate(42, '2026-10-07');
    $second = $horoscope->generate(42, '2026-10-07');

    expect($second)->not->toBe($first)
        ->and($second->toArray())->toBe($first->toArray());
});

it('generates a different reading for a different number or date', function () {
    config()->set('horoscope.cache.enabled', false);

    $horoscope = app(Horoscope::class);
    $base = $horoscope->generate(42, '2026-10-07');

    expect($horoscope->generate(43, '2026-10-07')->toArray())->not->toBe($base->toArray())
        ->and($horoscope->generate(42, '2026-10-08')->toArray())->not->toBe($base->toArray());
});

it('defaults the date to today', function () {
    config()->set('horoscope.cache.enabled', false);
    $this->travelTo(new DateTimeImmutable('2026-10-07 09:00:00'));

    $horoscope = app(Horoscope::class);

    expect($horoscope->generate(7)->toArray())
        ->toBe($horoscope->generate(7, '2026-10-07')->toArray());
});

it('accepts a date time instance', function () {
    config()->set('horoscope.cache.enabled', false);

    $horoscope = app(Horoscope::class);

    expect($horoscope->generate(7, new DateTimeImmutable('2026-10-07'))->toArray())
        ->toBe($horoscope->generate(7, '2026-10-07')->toArray());
});

it('returns the cached reading for the same number and date', function () {
    $horoscope = app(Horoscope::class);

    $first = $horoscope->generate(42, '2026-10-07');
    $second = $horoscope->generate(42, '2026-10-07');

    expect($second)->toBe($first);
});

it('regenerates and replaces the cached reading when forced', function () {
    $horoscope = app(Horoscope::class);

    $first = $horoscope->generate(42, '2026-10-07');
    $forced = $horoscope->generate(42, '2026-10-07', force: true);

    expect($forced)->not->toBe($first)
        ->and($horoscope->generate(42, '2026-10-07'))->toBe($forced);
});

it('does not use the cache when caching is disabled', function () {
    config()->set('horoscope.cache.enabled', false);

    $horoscope = app(Horoscope::class);

    $first = $horoscope->generate(42, '2026-10-07');
    $second = $horoscope->generate(42, '2026-10-07');

    expect($second)->not->toBe($first)
        ->and($second->toArray())->toBe($first->toArray());
});

it('caches the reading until the end of the reading date', function () {
    $this->travelTo(new DateTimeImmutable('2026-10-07 10:00:00'));

    app(Horoscope::class)->generate(42);

    expect(Cache::store('array')->has('horoscope:42:2026-10-07'))->toBeTrue();

    $this->travelTo(new DateTimeImmutable('2026-10-07 23:59:59'));
    expect(Cache::store('array')->has('horoscope:42:2026-10-07'))->toBeTrue();

    $this->travelTo(new DateTimeImmutable('2026-10-08 00:01:00'));
    expect(Cache::store('array')->has('horoscope:42:2026-10-07'))->toBeFalse();
});

it('respects a numeric cache ttl', function () {
    config()->set('horoscope.cache.ttl', 60);
    $this->travelTo(new DateTimeImmutable('2026-10-07 10:00:00'));

    app(Horoscope::class)->generate(42);

    expect(Cache::store('array')->has('horoscope:42:2026-10-07'))->toBeTrue();

    $this->travelTo(new DateTimeImmutable('2026-10-07 10:01:01'));
    expect(Cache::store('array')->has('horoscope:42:2026-10-07'))->toBeFalse();
});

it('caches the reading forever when the ttl is null', function () {
    config()->set('horoscope.cache.ttl', null);
    $this->travelTo(new DateTimeImmutable('2026-10-07 10:00:00'));

    app(Horoscope::class)->generate(42);

    $this->travelTo(new DateTimeImmutable('2030-10-07 10:00:00'));
    expect(Cache::store('array')->has('horoscope:42:2026-10-07'))->toBeTrue();
});
