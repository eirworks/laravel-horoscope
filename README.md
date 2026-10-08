# Horoscope

> Horoscope is **not published on Packagist yet**, so Composer has to install it
directly from the Git repository.

## Description

Horoscope is a Laravel package that generates **deterministic daily horoscope
readings**. Every reading scores five categories — **love**, **career**,
**money**, **health**, and **social** — plus an **overall** score (the rounded
average of the five). Readings are cached per identifier and date.

The same identifier and date (or zodiac sign) always produce the same scores, so
a reading is stable for the entire day. The package also ships a `ScoreReader`
that turns raw scores into qualitative levels and ready-to-display luck texts
that you can translate or replace.

## Requirements

- PHP 8.3+
- Laravel 12 or 13

## Installation

Register the Git repository and install the package with Composer:

```bash
composer config repositories.horoscope vcs https://github.com/eirworks/horoscope
composer require eirworks/horoscope
```

Or add the repository to your application's `composer.json` manually:

```json
{
    "repositories": [
        {
            "type": "vcs",
            "url": "https://github.com/eirworks/horoscope"
        }
    ],
    "require": {
        "eirworks/horoscope": "dev-main"
    }
}
```

Then run `composer update eirworks/horoscope`

While the package is unreleased, pin the `dev-main` branch (or a tagged
version once one exists) instead of relying on a stable constraint.

The service provider and `Horoscope` facade are registered automatically. The
configuration is merged, so the package works with sensible defaults out of the
box. To customize it, publish the config file:

```bash
php artisan vendor:publish --tag=horoscope-config
```

You can also publish the language files to translate the luck texts and zodiac
labels:

```bash
php artisan vendor:publish --tag=horoscope-lang
```

## Usage

Resolve the generator from the container or use the facade:

```php
use Horoscope\Horoscope\Facades\Horoscope;

$reading = Horoscope::generate(
    number: $user->id,      // any stable identifier
    date: '2026-10-07',     // \DateTimeInterface|string|null, defaults to today
    force: false,           // true ignores and replaces the cached reading
);

$reading->love;         // int 1-100
$reading->career;       // int 1-100
$reading->money;        // int 1-100
$reading->health;       // int 1-100
$reading->social;       // int 1-100
$reading->overall;      // int 1-100, rounded average of the five scores
$reading->match;        // ZodiacSign enum, e.g. ZodiacSign::Virgo
$reading->luckyNumber;  // int 1-99
$reading->luckyColor;   // LuckyColor value object
$reading->luckyColor->name; // 'gold'
$reading->luckyColor->hex;  // '#FFD700'
```

Every reading also includes a matched zodiac sign, a lucky number from 1 to 99,
and a lucky color drawn from `resources/colors/colors.php` with its name and hex
code. `match` is a `ZodiacSign` enum, and `luckyColor` is a `LuckyColor` value
object exposing `name` and `hex`.

The same number and date always produce the same scores, so a reading is stable
for the whole day. Pass `force: true` to roll a new reading and replace the
cached one.

### Generate by zodiac sign

Use `generateForSign()` to generate a reading from a zodiac sign instead of a
date. The sign is the stable input, so the same number and sign always produce
the same scores:

```php
use Horoscope\Horoscope\ZodiacSign;

$reading = Horoscope::generateForSign(
    number: $user->id,   // any stable identifier
    sign: 'virgo',       // ZodiacSign|string, case-insensitive
    force: false,        // true ignores and replaces the cached reading
);

// Or pass the enum directly:
$reading = Horoscope::generateForSign($user->id, ZodiacSign::Virgo);
```

`ZodiacSign` lists the twelve signs (`aries` through `pisces`), resolves a name
with `ZodiacSign::fromName('virgo')`, resolves a birth date with
`ZodiacSign::fromDate($date)`, and exposes a translated `label()`. Sign
readings are cached under `{prefix}:{number}:{sign}` and follow the configured
`cache.ttl`.

### Zodiac sign catalogue

Use `ZodiacSigns` to get display-ready metadata for the signs. `all()` returns
the twelve signs in calendar order and `get()` returns a single sign, both as
`ZodiacSignData` objects:

```php
use Horoscope\Horoscope\ZodiacSign;
use Horoscope\Horoscope\ZodiacSigns;

$signs = ZodiacSigns::all();          // list<ZodiacSignData>
$virgo = ZodiacSigns::get(ZodiacSign::Virgo);

$virgo->name;      // 'Virgo'   translated label
$virgo->codename;  // 'virgo'   downcased alphanumeric dash
$virgo->icon;      // '♍'       emoji
$virgo->startDate; // '08-23'   month-day, inclusive
$virgo->endDate;   // '09-22'   month-day, inclusive
$virgo->element;   // ZodiacElement::Earth

$virgo->element->value; // 'earth'
$virgo->element->label(); // 'Earth'
$virgo->element->icon();  // '🌍'

$virgo->toArray();
// ['sign' => 'virgo', 'name' => 'Virgo', 'codename' => 'virgo',
//  'icon' => '♍', 'start_date' => '08-23', 'end_date' => '09-22',
//  'element' => ['name' => 'Earth', 'icon' => '🌍']]
```

`ZodiacSignData` implements `Arrayable` and `JsonSerializable`, so it can be
converted with `toArray()`, `toJson()`, or `json_encode()`.

### Zodiac elements

The twelve signs belong to the four classical elements: `fire` (Aries, Leo,
Sagittarius), `earth` (Taurus, Virgo, Capricorn), `air` (Gemini, Libra,
Aquarius), and `water` (Cancer, Scorpio, Pisces). `ZodiacElement` is a backed
enum exposing a translated `label()` and an emoji `icon()`:

```php
use Horoscope\Horoscope\ZodiacElement;

ZodiacElement::Fire->value; // 'fire'
ZodiacElement::Fire->label(); // 'Fire'
ZodiacElement::Fire->icon();  // '🔥'

ZodiacElement::Water->toArray();
// ['name' => 'Water', 'icon' => '💧']
```

`ZodiacElement` implements `Arrayable` and `JsonSerializable`. Element labels
live in `lang/en/elements.php`, so publish `horoscope-lang` to translate them.

### Lucky colors

Use `LuckyColors` to list the colors a reading can pick from:

```php
use Horoscope\Horoscope\LuckyColors;

$colors = LuckyColors::all();          // list<LuckyColor>
$gold = LuckyColors::get(14);          // LuckyColor by index
$gold = LuckyColors::fromName('gold'); // LuckyColor by name, case-insensitive

$gold->name; // 'gold'
$gold->hex;  // '#FFD700'

$gold->toArray();
// ['name' => 'gold', 'hex' => '#FFD700']
```

`LuckyColor` implements `Arrayable` and `JsonSerializable` and the palette is
read from `resources/colors/colors.php`.

### Arrays and JSON

`HoroscopeResult` implements `Arrayable` and `JsonSerializable` and can be
converted back and forth:

```php
$array = $reading->toArray();
// ['love' => 43, 'career' => 80, ..., 'overall' => 52,
//  'match' => 'virgo', 'lucky_number' => 42,
//  'lucky_color' => ['name' => 'gold', 'hex' => '#FFD700']]

$reading = \Horoscope\Horoscope\HoroscopeResult::fromArray($array);

$reading->toJson();
json_encode($reading);
```

### Score levels and luck texts

Use `ScoreReader` to read a raw score into one of five qualitative levels:

```php
use Horoscope\Horoscope\ScoreReader;

$reader = new ScoreReader;

$reader->read(43);           // ScoreLevel::Normal
$reader->read(85);           // ScoreLevel::Excellent
$reader->read(85)->label();  // 'Excellent'
```

| Level     | Scores |
| --------- | ------ |
| terrible  | 1-20   |
| bad       | 21-40  |
| normal    | 41-60  |
| good      | 61-80  |
| excellent | 81-100 |

Every stat keeps ten luck texts for each level in `lang/en/luck.php`. Pick one
deterministically with a seed, or read all candidates:

```php
$reading = Horoscope::generate($user->id);
$seed = $user->id; // any stable value

$reader->text('love', $reading->love, $seed); // one luck text
$reader->texts('love', $reading->love);       // all candidate texts
```

`ScoreReader::STATS` lists the stats that have luck texts: `love`, `career`,
`money`, `health`, `social`, and `overall`. Publish the language files to
translate or replace the texts:

```bash
php artisan vendor:publish --tag=horoscope-lang
```

## Configuration

```php
// config/horoscope.php
return [
    'cache' => [
        'enabled' => true,        // store generated readings
        'store' => null,          // null uses the default cache store
        'prefix' => 'horoscope',  // cache key prefix
        'ttl' => 'end-of-day',    // 'end-of-day', seconds (int), or null (forever)
    ],
];
```

Cache keys follow the `{prefix}:{number}:{Y-m-d}` pattern, for example
`horoscope:42:2026-10-07`. Sign readings use `{prefix}:{number}:{sign}` instead,
for example `horoscope:42:virgo`.

## Testing

```bash
composer test
```

## License

Horoscope is open-sourced software licensed under the [MIT license](LICENSE.md).
