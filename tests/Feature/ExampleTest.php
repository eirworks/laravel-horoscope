<?php

declare(strict_types=1);

use Horoscope\Horoscope\Horoscope;

it('resolves the singleton', function () {
    expect(app(Horoscope::class))->toBeInstanceOf(Horoscope::class);
});

it('returns the same instance from the container', function () {
    expect(app(Horoscope::class))->toBe(app(Horoscope::class));
});

it('loads the package translations', function () {
    expect(trans('horoscope::messages.placeholder'))->toBe('Horoscope placeholder translation.');
});
