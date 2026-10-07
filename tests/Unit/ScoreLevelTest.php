<?php

declare(strict_types=1);

use Horoscope\Horoscope\ScoreLevel;

it('resolves the level for every score boundary', function (int $score, ScoreLevel $level) {
    expect(ScoreLevel::fromScore($score))->toBe($level);
})->with([
    [1, ScoreLevel::Terrible],
    [20, ScoreLevel::Terrible],
    [21, ScoreLevel::Bad],
    [40, ScoreLevel::Bad],
    [41, ScoreLevel::Normal],
    [60, ScoreLevel::Normal],
    [61, ScoreLevel::Good],
    [80, ScoreLevel::Good],
    [81, ScoreLevel::Excellent],
    [100, ScoreLevel::Excellent],
]);

it('exposes the minimum and maximum score of each level', function (ScoreLevel $level, int $minimum, int $maximum) {
    expect($level->minimum())->toBe($minimum)
        ->and($level->maximum())->toBe($maximum);
})->with([
    [ScoreLevel::Terrible, 1, 20],
    [ScoreLevel::Bad, 21, 40],
    [ScoreLevel::Normal, 41, 60],
    [ScoreLevel::Good, 61, 80],
    [ScoreLevel::Excellent, 81, 100],
]);

it('knows whether a score is contained in a level', function () {
    expect(ScoreLevel::Bad->contains(21))->toBeTrue()
        ->and(ScoreLevel::Bad->contains(40))->toBeTrue()
        ->and(ScoreLevel::Bad->contains(20))->toBeFalse()
        ->and(ScoreLevel::Bad->contains(41))->toBeFalse();
});

it('returns the translated label of a level', function (ScoreLevel $level, string $label) {
    expect($level->label())->toBe($label);
})->with([
    [ScoreLevel::Terrible, 'Terrible'],
    [ScoreLevel::Bad, 'Bad'],
    [ScoreLevel::Normal, 'Normal'],
    [ScoreLevel::Good, 'Good'],
    [ScoreLevel::Excellent, 'Excellent'],
]);
