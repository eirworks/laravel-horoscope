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

$reading->love;    // int 1-100
$reading->career;  // int 1-100
$reading->money;   // int 1-100
$reading->health;  // int 1-100
$reading->social;  // int 1-100
$reading->overall; // int 1-100, rounded average of the five scores
```

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

### Arrays and JSON

`HoroscopeResult` implements `Arrayable` and `JsonSerializable` and can be
converted back and forth:

```php
$array = $reading->toArray();
// ['love' => 43, 'career' => 80, ..., 'overall' => 52]

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
