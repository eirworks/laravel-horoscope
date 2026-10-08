---
name: horoscope-development
description: >
  Configure and apply the Horoscope package in Laravel applications.
license: MIT
metadata:
  author: Rully (eirworks)
---

# Horoscope

Use this skill when a Laravel application needs to generate daily horoscope
readings with the `eirworks/horoscope` package.

## Primary Goal

- generate cached, deterministic daily horoscope readings through the package's
  public API in the smallest correct way

## Workflow

### 1. Install and configure

- require `eirworks/horoscope` in the Laravel application
- optionally publish the config: `php artisan vendor:publish --tag=horoscope-config`
- adjust `config/horoscope.php` only when the defaults do not fit:
  `cache.enabled`, `cache.store`, `cache.prefix`, and `cache.ttl`
  (`'end-of-day'`, seconds as `int`, or `null` for forever)

### 2. Generate a reading

- resolve `Horoscope\Horoscope\Horoscope` from the container, or use the
  `Horoscope\Horoscope\Facades\Horoscope` facade
- call `generate($number, $date = null, $force = false)`
- `$number` is any stable identifier such as a user id; `$date` is a
  `DateTimeInterface`, string, or `null` (today); `$force` ignores and replaces
  the cached reading
- call `generateForSign($number, $sign, $force = false)` for a reading based on
  a zodiac sign instead of a date; `$sign` is a `ZodiacSign` or a
  case-insensitive name such as `'virgo'`

```php
use Horoscope\Horoscope\Facades\Horoscope;

$reading = Horoscope::generate($user->id);

$reading->love;         // 1-100
$reading->career;       // 1-100
$reading->money;        // 1-100
$reading->health;       // 1-100
$reading->social;       // 1-100
$reading->overall;      // 1-100 rounded average
$reading->match;        // ZodiacSign enum, e.g. ZodiacSign::Virgo
$reading->luckyNumber;  // 1-99
$reading->luckyColor;   // LuckyColor value object
$reading->luckyColor->name; // 'gold'
$reading->luckyColor->hex;  // '#FFD700'
```

### 3. Work with the result

- the same number and date always yield the same scores, and the reading is
  cached per `{prefix}:{number}:{Y-m-d}`
- the same number and sign always yield the same scores, and the sign reading is
  cached per `{prefix}:{number}:{sign}` (for example `horoscope:42:virgo`)
- `Horoscope\Horoscope\ZodiacSign` holds the twelve signs (`aries` through
  `pisces`), resolves a name with `ZodiacSign::fromName('virgo')`, resolves the
  sign for a date with `ZodiacSign::fromDate($date)`, and exposes a translated
  `label()`
- `Horoscope\Horoscope\ZodiacSigns::all()` returns the twelve `ZodiacSignData`
  objects in calendar order and `ZodiacSigns::get($sign)` returns one; each
  exposes `$name` (translated label), `$codename` (downcased alphanumeric
  dash), `$icon` (emoji), `$startDate`, and `$endDate` (inclusive `m-d`)
- `ZodiacSignData` implements `Arrayable` and `JsonSerializable`
- every reading carries a matched `ZodiacSign` (`$reading->match`), a
  `luckyNumber` from 1 to 99, and a `LuckyColor` (`$reading->luckyColor`); a
  sign reading never matches with its own sign
- `Horoscope\Horoscope\LuckyColors::all()` returns the `LuckyColor` palette from
  `resources/colors/colors.php`; `LuckyColors::get($index)` and
  `LuckyColors::fromName($name)` resolve a single `LuckyColor`, and each exposes
  `$name` and `$hex`
- `LuckyColor`, `ZodiacSignData`, and `HoroscopeResult` implement `Arrayable`
  and `JsonSerializable`
- convert with `toArray()`, `toJson()`, `json_encode()`, or
  `HoroscopeResult::fromArray($array)`

### 4. Read score levels and luck texts

- resolve `Horoscope\Horoscope\ScoreReader` and call `read($score)` to get a
  `ScoreLevel` (`terrible` 1-20, `bad` 21-40, `normal` 41-60, `good` 61-80,
  `excellent` 81-100); `ScoreLevel::fromScore($score)` is the same mapping
- call `text($stat, $score, $seed = null)` for one deterministically picked
  luck text, or `texts($stat, $score)` for both candidates
- `ScoreReader::STATS` are `love`, `career`, `money`, `health`, `social`, and
  `overall`; the texts live in `lang/en/luck.php`

```php
use Horoscope\Horoscope\ScoreReader;

$reader = new ScoreReader;
$reading = Horoscope::generate($user->id);

$reader->read($reading->love);                  // ScoreLevel
$reader->text('love', $reading->love, $user->id); // one text, stable per seed
$reader->texts('love', $reading->love);         // both candidates
```

## Rules, References, and Templates

Read before executing:

- `config/horoscope.php` for cache options
- `lang/en/luck.php` for score levels and luck text resources
- `lang/en/zodiac.php` for zodiac sign names
- `Horoscope\Horoscope\HoroscopeResult` for the reading object
- `Horoscope\Horoscope\ZodiacSign` for zodiac signs
- `Horoscope\Horoscope\ZodiacSigns` and `Horoscope\Horoscope\ZodiacSignData` for
  sign names, codenames, icons, and date ranges
- `Horoscope\Horoscope\LuckyColors` and `Horoscope\Horoscope\LuckyColor` for
  lucky color names and hex codes
- `Horoscope\Horoscope\ScoreReader` and `Horoscope\Horoscope\ScoreLevel` for
  score levels and luck texts

## Examples

- Daily dashboard widget for an authenticated user:

```php
$reading = Horoscope::generate($user->id);

return view('dashboard', ['reading' => $reading]);
```

- Force a fresh reading for a specific day:

```php
$reading = Horoscope::generate($user->id, '2026-10-07', force: true);
```

- Read a zodiac sign instead of a date:

```php
use Horoscope\Horoscope\ZodiacSign;

$reading = Horoscope::generateForSign($user->id, ZodiacSign::Virgo);
$reading = Horoscope::generateForSign($user->id, 'virgo', force: true);
```

- List the signs or read one sign's display metadata:

```php
use Horoscope\Horoscope\ZodiacSign;
use Horoscope\Horoscope\ZodiacSigns;

$signs = ZodiacSigns::all();                   // list<ZodiacSignData>
$virgo = ZodiacSigns::get(ZodiacSign::Virgo);

$virgo->name;      // 'Virgo'   translated label
$virgo->codename;  // 'virgo'   downcased alphanumeric dash
$virgo->icon;      // '♍'       emoji
$virgo->startDate; // '08-23'
$virgo->endDate;   // '09-22'
$virgo->toArray();
```

- Store or transfer a reading as an array and restore it later:

```php
$data = Horoscope::generate($user->id)->toArray();
$reading = \Horoscope\Horoscope\HoroscopeResult::fromArray($data);
```

- Show the match and lucky values of a reading:

```php
$reading = Horoscope::generateForSign($user->id, 'leo');

$reading->match->value;      // e.g. 'virgo'
$reading->match->label();    // e.g. 'Virgo'
$reading->luckyNumber;       // 1-99
$reading->luckyColor->name;  // e.g. 'gold'
$reading->luckyColor->hex;   // e.g. '#FFD700'
```

- Show a level and luck text for each stat:

```php
$reader = new \Horoscope\Horoscope\ScoreReader;
$reading = Horoscope::generate($user->id);

foreach (\Horoscope\Horoscope\ScoreReader::STATS as $stat) {
    $level = $reader->read($reading->{$stat});
    $text = $reader->text($stat, $reading->{$stat}, $user->id);
}
```

## Anti-patterns

- do not seed or roll random values yourself; the generator already derives
  stable scores from the number and date or sign
- do not reuse one number across users when you want per-user readings
- do not call `generate()` with `force: true` on every request; it defeats the
  daily cache
