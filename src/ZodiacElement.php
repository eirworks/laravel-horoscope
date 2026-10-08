<?php

declare(strict_types=1);

namespace Horoscope\Horoscope;

use Illuminate\Contracts\Support\Arrayable;
use JsonSerializable;

/**
 * The four classical elements the zodiac signs are grouped into.
 *
 * @implements Arrayable<string, string>
 */
enum ZodiacElement: string implements Arrayable, JsonSerializable
{
    case Fire = 'fire';
    case Earth = 'earth';
    case Air = 'air';
    case Water = 'water';

    /**
     * The emoji icon of the element.
     */
    public function icon(): string
    {
        return match ($this) {
            self::Fire => '🔥',
            self::Earth => '🌍',
            self::Air => '💨',
            self::Water => '💧',
        };
    }

    /**
     * The translated, human readable name of the element.
     */
    public function label(): string
    {
        return trans('horoscope::elements.'.$this->value);
    }

    /**
     * Get the array representation of the element.
     *
     * @return array{name: string, icon: string}
     */
    public function toArray(): array
    {
        return [
            'name' => $this->label(),
            'icon' => $this->icon(),
        ];
    }

    /**
     * Get the JSON representation of the element.
     */
    public function toJson(int $options = 0): string
    {
        return json_encode($this, $options | JSON_THROW_ON_ERROR);
    }

    /**
     * Prepare the element for JSON serialization.
     *
     * @return array{name: string, icon: string}
     */
    public function jsonSerialize(): array
    {
        return $this->toArray();
    }
}
