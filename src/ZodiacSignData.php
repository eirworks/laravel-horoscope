<?php

declare(strict_types=1);

namespace Horoscope\Horoscope;

use Illuminate\Contracts\Support\Arrayable;
use JsonSerializable;

/**
 * The descriptive data of a zodiac sign.
 *
 * @implements Arrayable<string, string>
 */
final readonly class ZodiacSignData implements Arrayable, JsonSerializable
{
    public function __construct(
        public ZodiacSign $sign,
        public string $name,
        public string $codename,
        public string $icon,
        public string $startDate,
        public string $endDate,
    ) {}

    /**
     * Get the array representation of the sign data.
     *
     * @return array{sign: string, name: string, codename: string, icon: string, start_date: string, end_date: string}
     */
    public function toArray(): array
    {
        return [
            'sign' => $this->sign->value,
            'name' => $this->name,
            'codename' => $this->codename,
            'icon' => $this->icon,
            'start_date' => $this->startDate,
            'end_date' => $this->endDate,
        ];
    }

    /**
     * Get the JSON representation of the sign data.
     */
    public function toJson(int $options = 0): string
    {
        return json_encode($this, $options | JSON_THROW_ON_ERROR);
    }

    /**
     * Prepare the sign data for JSON serialization.
     *
     * @return array{sign: string, name: string, codename: string, icon: string, start_date: string, end_date: string}
     */
    public function jsonSerialize(): array
    {
        return $this->toArray();
    }
}
