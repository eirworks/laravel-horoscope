<?php

declare(strict_types=1);

use Horoscope\Horoscope\Horoscope;

it('resolves the singleton', function () {
    expect(app(Horoscope::class))->toBeInstanceOf(Horoscope::class);
});

it('returns the same instance from the container', function () {
    expect(app(Horoscope::class))->toBe(app(Horoscope::class));
});

it('merges the package config', function () {
    expect(config('horoscope.placeholder'))->toBe('default');
});

it('loads the package translations', function () {
    expect(trans('horoscope::messages.placeholder'))->toBe('Horoscope placeholder translation.');
});

it('loads the package views', function () {
    expect(view()->exists('horoscope::placeholder'))->toBeTrue();
});

it('registers the artisan command', function () {
    $this->artisan('horoscope:placeholder')
        ->expectsOutputToContain('Horoscope placeholder command executed.')
        ->assertSuccessful();
});
