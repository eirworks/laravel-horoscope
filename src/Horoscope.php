<?php

declare(strict_types=1);

namespace Horoscope\Horoscope;

use Carbon\CarbonImmutable;
use DateTimeInterface;
use Illuminate\Contracts\Cache\Factory as CacheFactory;
use Illuminate\Contracts\Cache\Repository;
use Random\Engine\Mt19937;
use Random\Randomizer;

/**
 * Generates deterministic horoscope readings for a given number and date or
 * zodiac sign.
 */
class Horoscope
{
    /**
     * The lowest possible score for a category.
     */
    private const int MIN_SCORE = 1;

    /**
     * The highest possible score for a category.
     */
    private const int MAX_SCORE = 100;

    /**
     * The lowest possible lucky number.
     */
    private const int MIN_LUCKY_NUMBER = 1;

    /**
     * The highest possible lucky number.
     */
    private const int MAX_LUCKY_NUMBER = 99;

    public function __construct(private readonly CacheFactory $cache) {}

    /**
     * Generate a horoscope reading for the given number and date.
     *
     * The reading is deterministic: the same number and date always produce the
     * same scores. When caching is enabled the reading is stored under a key
     * derived from the number and date, and reused on later calls unless the
     * reading is explicitly forced.
     *
     * @param  int|string  $number  A user id or any other stable identifier.
     * @param  DateTimeInterface|string|null  $date  The reading date, defaults to today.
     * @param  bool  $force  Ignore any cached reading and generate a new one.
     */
    public function generate(
        int|string $number,
        DateTimeInterface|string|null $date = null,
        bool $force = false,
    ): HoroscopeResult {
        $date = $this->normalizeDate($date);

        return $this->read($number, $date->toDateString(), $date, null, $force);
    }

    /**
     * Generate a horoscope reading for the given number and zodiac sign.
     *
     * Unlike generate(), the reading is not tied to a date. The sign is used
     * as the stable input instead, so the same number and sign always produce
     * the same scores. When caching is enabled the reading is stored under a
     * key derived from the number and sign, and reused on later calls unless
     * the reading is explicitly forced.
     *
     * @param  int|string  $number  A user id or any other stable identifier.
     * @param  ZodiacSign|string  $sign  The zodiac sign, for example "virgo".
     * @param  bool  $force  Ignore any cached reading and generate a new one.
     */
    public function generateForSign(
        int|string $number,
        ZodiacSign|string $sign,
        bool $force = false,
    ): HoroscopeResult {
        $sign = $sign instanceof ZodiacSign ? $sign : ZodiacSign::fromName($sign);

        return $this->read($number, $sign->value, CarbonImmutable::today(), $sign, $force);
    }

    /**
     * Read the cached reading for the given inputs or roll a new one.
     */
    private function read(
        int|string $number,
        string $discriminator,
        CarbonImmutable $expiresAt,
        ?ZodiacSign $sign,
        bool $force,
    ): HoroscopeResult {
        $store = $this->cacheEnabled() ? $this->cacheStore() : null;
        $key = $this->cacheKey($number, $discriminator);

        if ($store !== null && ! $force) {
            $cached = $store->get($key);

            if ($cached instanceof HoroscopeResult) {
                return $cached;
            }
        }

        $result = $this->roll($number, $discriminator, $sign, $force);

        $store?->put($key, $result, $this->expiration($expiresAt));

        return $result;
    }

    /**
     * Resolve the reading date, defaulting to today.
     */
    private function normalizeDate(DateTimeInterface|string|null $date): CarbonImmutable
    {
        if ($date === null) {
            return CarbonImmutable::today();
        }

        if ($date instanceof DateTimeInterface) {
            return CarbonImmutable::instance($date);
        }

        return CarbonImmutable::parse($date);
    }

    /**
     * Roll the scores, match, and lucky values for the given inputs.
     */
    private function roll(
        int|string $number,
        string $discriminator,
        ?ZodiacSign $sign,
        bool $force,
    ): HoroscopeResult {
        $seed = $force
            ? random_int(self::MIN_SCORE, PHP_INT_MAX)
            : crc32($number.'|'.$discriminator);

        $randomizer = new Randomizer(new Mt19937($seed));

        $colors = LuckyColors::all();

        return new HoroscopeResult(
            love: $randomizer->getInt(self::MIN_SCORE, self::MAX_SCORE),
            career: $randomizer->getInt(self::MIN_SCORE, self::MAX_SCORE),
            money: $randomizer->getInt(self::MIN_SCORE, self::MAX_SCORE),
            health: $randomizer->getInt(self::MIN_SCORE, self::MAX_SCORE),
            social: $randomizer->getInt(self::MIN_SCORE, self::MAX_SCORE),
            match: $this->pickMatch($randomizer, $sign),
            luckyNumber: $randomizer->getInt(self::MIN_LUCKY_NUMBER, self::MAX_LUCKY_NUMBER),
            luckyColor: $colors[$randomizer->getInt(0, count($colors) - 1)],
        );
    }

    /**
     * Pick the zodiac sign to match the reading with.
     *
     * When the reading is tied to a sign, that sign is excluded so a reading
     * never matches with itself.
     */
    private function pickMatch(Randomizer $randomizer, ?ZodiacSign $sign): ZodiacSign
    {
        $candidates = [];

        foreach (ZodiacSign::cases() as $candidate) {
            if ($candidate !== $sign) {
                $candidates[] = $candidate;
            }
        }

        return $candidates[$randomizer->getInt(0, count($candidates) - 1)];
    }

    /**
     * Build the cache key for the given number and discriminator.
     */
    private function cacheKey(int|string $number, string $discriminator): string
    {
        $prefix = config('horoscope.cache.prefix', 'horoscope');

        return sprintf(
            '%s:%s:%s',
            is_string($prefix) ? $prefix : 'horoscope',
            $number,
            $discriminator,
        );
    }

    /**
     * Determine when the cached reading for the given date should expire.
     */
    private function expiration(CarbonImmutable $date): DateTimeInterface|int|null
    {
        $ttl = config('horoscope.cache.ttl', 'end-of-day');

        if (is_int($ttl)) {
            return $ttl;
        }

        if ($ttl === 'end-of-day') {
            return $date->endOfDay();
        }

        return null;
    }

    /**
     * Determine whether caching is enabled.
     */
    private function cacheEnabled(): bool
    {
        return (bool) config('horoscope.cache.enabled', true);
    }

    /**
     * Resolve the cache repository used to store readings.
     */
    private function cacheStore(): Repository
    {
        $store = config('horoscope.cache.store');

        return $this->cache->store(is_string($store) ? $store : null);
    }
}
