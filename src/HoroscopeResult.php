<?php

declare(strict_types=1);

namespace Horoscope\Horoscope;

use Illuminate\Contracts\Support\Arrayable;
use JsonSerializable;

/**
 * An immutable horoscope reading.
 *
 * @implements Arrayable<string, int>
 */
final readonly class HoroscopeResult implements Arrayable, JsonSerializable
{
    /**
     * The rounded average of every category score.
     */
    public int $overall;

    public function __construct(
        public int $love,
        public int $career,
        public int $money,
        public int $health,
        public int $social,
    ) {
        $this->overall = self::average([
            $this->love,
            $this->career,
            $this->money,
            $this->health,
            $this->social,
        ]);
    }

    /**
     * Build a reading from its array representation.
     *
     * @param  array{love: int, career: int, money: int, health: int, social: int, overall?: int}  $data
     */
    public static function fromArray(array $data): self
    {
        return new self(
            love: $data['love'],
            career: $data['career'],
            money: $data['money'],
            health: $data['health'],
            social: $data['social'],
        );
    }

    /**
     * Get the array representation of the reading.
     *
     * @return array{love: int, career: int, money: int, health: int, social: int, overall: int}
     */
    public function toArray(): array
    {
        return [
            'love' => $this->love,
            'career' => $this->career,
            'money' => $this->money,
            'health' => $this->health,
            'social' => $this->social,
            'overall' => $this->overall,
        ];
    }

    /**
     * Get the JSON representation of the reading.
     */
    public function toJson(int $options = 0): string
    {
        return json_encode($this, $options | JSON_THROW_ON_ERROR);
    }

    /**
     * Prepare the reading for JSON serialization.
     *
     * @return array{love: int, career: int, money: int, health: int, social: int, overall: int}
     */
    public function jsonSerialize(): array
    {
        return $this->toArray();
    }

    /**
     * Calculate the rounded average of the given scores.
     *
     * @param  list<int>  $scores
     */
    private static function average(array $scores): int
    {
        return (int) round(array_sum($scores) / count($scores));
    }
}
