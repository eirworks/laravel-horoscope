<?php

declare(strict_types=1);

use Horoscope\Horoscope\LuckyColor;
use Horoscope\Horoscope\LuckyColors;

it('lists every lucky color from the color resource', function () {
    $colors = LuckyColors::all();

    expect($colors)->toHaveCount(15)
        ->and($colors)->each->toBeInstanceOf(LuckyColor::class);

    foreach ($colors as $color) {
        expect($color->name)->toBeString()->not->toBe('')
            ->and($color->hex)->toMatch('/^#[0-9A-F]{6}$/');
    }

    expect($colors[0]->toArray())->toBe(['name' => 'red', 'hex' => '#FF0000'])
        ->and(LuckyColors::fromName('gold')->toArray())->toBe(['name' => 'gold', 'hex' => '#FFD700']);
});

it('returns the lucky color at an index', function () {
    expect(LuckyColors::get(0)->name)->toBe('red')
        ->and(LuckyColors::get(14)->name)->toBe('gold');
});

it('throws for an unknown lucky color index', function () {
    expect(fn () => LuckyColors::get(15))
        ->toThrow(InvalidArgumentException::class, 'Unknown lucky color index [15].');
});

it('resolves a color from its name, ignoring case and whitespace', function (string $name) {
    expect(LuckyColors::fromName($name)->name)->toBe('turquoise');
})->with([
    'turquoise',
    'Turquoise',
    'TURQUOISE',
    '  turquoise  ',
]);

it('throws for an unknown color name', function () {
    expect(fn () => LuckyColors::fromName('chartreuse'))
        ->toThrow(InvalidArgumentException::class, 'Unknown lucky color [chartreuse].');
});

it('converts a lucky color to an array and json', function () {
    $color = new LuckyColor(name: 'maroon', hex: '#800000');

    expect($color->toArray())->toBe(['name' => 'maroon', 'hex' => '#800000'])
        ->and(json_decode($color->toJson(), true))->toBe($color->toArray())
        ->and(json_decode(json_encode($color, JSON_THROW_ON_ERROR), true))->toBe($color->toArray());
});
