<?php

declare(strict_types=1);

namespace Horoscope\Horoscope;

use InvalidArgumentException;

/**
 * The catalogue of available lucky colors.
 */
final class LuckyColors
{
    /**
     * The absolute path to the color definitions.
     */
    private const string PATH = __DIR__.'/../resources/colors/colors.php';

    /**
     * Get every available lucky color.
     *
     * @return list<LuckyColor>
     */
    public static function all(): array
    {
        return array_map(
            static fn (array $color): LuckyColor => new LuckyColor(
                name: $color['name'],
                hex: $color['hex'],
            ),
            self::definitions(),
        );
    }

    /**
     * Get the lucky color at the given index.
     *
     * @throws InvalidArgumentException when the index does not exist.
     */
    public static function get(int $index): LuckyColor
    {
        $colors = self::all();

        return $colors[$index]
            ?? throw new InvalidArgumentException(sprintf('Unknown lucky color index [%d].', $index));
    }

    /**
     * Resolve a lucky color from its name, ignoring case and surrounding whitespace.
     *
     * @throws InvalidArgumentException when the name is not a known color.
     */
    public static function fromName(string $name): LuckyColor
    {
        $needle = mb_strtolower(trim($name));

        foreach (self::all() as $color) {
            if (mb_strtolower($color->name) === $needle) {
                return $color;
            }
        }

        throw new InvalidArgumentException(sprintf('Unknown lucky color [%s].', $name));
    }

    /**
     * Load the color definitions from the color resource.
     *
     * @return list<array{name: string, hex: string}>
     */
    private static function definitions(): array
    {
        /** @var list<array{name: string, hex: string}> $colors */
        $colors = require self::PATH;

        return $colors;
    }
}
