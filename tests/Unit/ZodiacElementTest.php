<?php

declare(strict_types=1);

use Horoscope\Horoscope\ZodiacElement;

it('exposes every zodiac element', function (ZodiacElement $element, string $value) {
    expect($element->value)->toBe($value);
})->with([
    [ZodiacElement::Fire, 'fire'],
    [ZodiacElement::Earth, 'earth'],
    [ZodiacElement::Air, 'air'],
    [ZodiacElement::Water, 'water'],
]);

it('returns the translated label of an element', function (ZodiacElement $element, string $label) {
    expect($element->label())->toBe($label);
})->with([
    [ZodiacElement::Fire, 'Fire'],
    [ZodiacElement::Earth, 'Earth'],
    [ZodiacElement::Air, 'Air'],
    [ZodiacElement::Water, 'Water'],
]);

it('returns the emoji icon of an element', function (ZodiacElement $element, string $icon) {
    expect($element->icon())->toBe($icon);
})->with([
    [ZodiacElement::Fire, '🔥'],
    [ZodiacElement::Earth, '🌍'],
    [ZodiacElement::Air, '💨'],
    [ZodiacElement::Water, '💧'],
]);

it('converts an element to an array', function () {
    expect(ZodiacElement::Water->toArray())->toBe([
        'name' => 'Water',
        'icon' => '💧',
    ]);
});

it('serializes an element to json', function () {
    $element = ZodiacElement::Fire;

    expect(json_decode($element->toJson(), true))->toBe($element->toArray())
        ->and(json_decode(json_encode($element, JSON_THROW_ON_ERROR), true))->toBe($element->toArray());
});
