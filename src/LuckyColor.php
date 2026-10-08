<?php

declare(strict_types=1);

namespace Horoscope\Horoscope;

use Illuminate\Contracts\Support\Arrayable;
use JsonSerializable;

/**
 * The lucky color of a horoscope reading.
 *
 * @implements Arrayable<string, string>
 */
final readonly class LuckyColor implements Arrayable, JsonSerializable
{
    public function __construct(
        public string $name,
        public string $hex,
    ) {}

    /**
     * Get the array representation of the color.
     *
     * @return array{name: string, hex: string}
     */
    public function toArray(): array
    {
        return [
            'name' => $this->name,
            'hex' => $this->hex,
        ];
    }

    /**
     * Get the JSON representation of the color.
     */
    public function toJson(int $options = 0): string
    {
        return json_encode($this, $options | JSON_THROW_ON_ERROR);
    }

    /**
     * Prepare the color for JSON serialization.
     *
     * @return array{name: string, hex: string}
     */
    public function jsonSerialize(): array
    {
        return $this->toArray();
    }
}
