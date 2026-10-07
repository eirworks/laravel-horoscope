<?php

declare(strict_types=1);

use Horoscope\Horoscope\ScoreLevel;
use Horoscope\Horoscope\ScoreReader;

$statLevels = [];

foreach (ScoreReader::STATS as $stat) {
    foreach (ScoreLevel::cases() as $level) {
        $statLevels[$stat.' '.$level->value] = [$stat, $level->minimum()];
    }
}

it('exposes the six stats', function () {
    expect(ScoreReader::STATS)->toBe(['love', 'career', 'money', 'health', 'social', 'overall']);
});

it('reads a score into its level', function (int $score, ScoreLevel $level) {
    expect((new ScoreReader)->read($score))->toBe($level);
})->with([
    [1, ScoreLevel::Terrible],
    [30, ScoreLevel::Bad],
    [50, ScoreLevel::Normal],
    [70, ScoreLevel::Good],
    [90, ScoreLevel::Excellent],
]);

it('provides exactly two luck texts for every stat and level', function (string $stat, int $score) {
    $texts = (new ScoreReader)->texts($stat, $score);

    expect($texts)->toHaveCount(2);

    foreach ($texts as $text) {
        expect($text)->toBeString()->not->toBe('');
    }
})->with($statLevels);

it('returns one of the candidate texts', function (string $stat, int $score) {
    $reader = new ScoreReader;

    expect($reader->texts($stat, $score))->toContain($reader->text($stat, $score));
})->with($statLevels);

it('picks the same text for the same seed', function () {
    $reader = new ScoreReader;

    expect($reader->text('love', 30, 12345))->toBe($reader->text('love', 30, 12345))
        ->and($reader->text('love', 30, 'seed'))->toBe($reader->text('love', 30, 'seed'));
});

it('picks a text without a seed', function () {
    $reader = new ScoreReader;

    expect($reader->text('career', 70))->toBe($reader->text('career', 70));
});

it('returns no texts for an unknown stat', function () {
    $reader = new ScoreReader;

    expect($reader->texts('unknown', 50))->toBe([])
        ->and($reader->text('unknown', 50))->toBe('');
});
