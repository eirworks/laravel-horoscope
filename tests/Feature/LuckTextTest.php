<?php

declare(strict_types=1);

use Horoscope\Horoscope\Facades\Horoscope;
use Horoscope\Horoscope\HoroscopeResult;
use Horoscope\Horoscope\ScoreReader;

it('produces a luck text for every score of a generated reading', function () {
    $reading = Horoscope::generate(42, '2026-10-07');

    expect($reading)->toBeInstanceOf(HoroscopeResult::class);

    $reader = new ScoreReader;

    foreach (ScoreReader::STATS as $stat) {
        expect($reader->text($stat, $reading->{$stat}))->toBeString()->not->toBe('');
    }
});
