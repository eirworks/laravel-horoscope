<?php

declare(strict_types=1);

namespace Horoscope\Horoscope;

use Random\Engine\Mt19937;
use Random\Randomizer;

/**
 * Reads numeric horoscope scores into score levels and luck texts.
 */
final class ScoreReader
{
    /**
     * The score categories that have luck texts.
     *
     * @var list<string>
     */
    public const array STATS = ['love', 'career', 'money', 'health', 'social', 'overall'];

    /**
     * Read a numeric score into its score level.
     */
    public function read(int $score): ScoreLevel
    {
        return ScoreLevel::fromScore($score);
    }

    /**
     * Get the luck text candidates for the given stat and score.
     *
     * @return list<string>
     */
    public function texts(string $stat, int $score): array
    {
        $candidates = trans('horoscope::luck.texts.'.$stat.'.'.$this->read($score)->value);

        if (! is_array($candidates)) {
            return [];
        }

        $texts = [];

        foreach ($candidates as $candidate) {
            if (is_string($candidate)) {
                $texts[] = $candidate;
            }
        }

        return $texts;
    }

    /**
     * Get one luck text for the given stat and score.
     *
     * The text is picked deterministically from the candidates using the
     * given seed, so the same seed, stat, and score always return the same
     * text. When no seed is given, the stat and score are used as the seed.
     */
    public function text(string $stat, int $score, int|string|null $seed = null): string
    {
        $texts = $this->texts($stat, $score);

        if ($texts === []) {
            return '';
        }

        $randomizer = new Randomizer(new Mt19937($this->seed($stat, $score, $seed)));

        return $texts[$randomizer->getInt(0, count($texts) - 1)];
    }

    /**
     * Resolve the deterministic seed used to pick a luck text.
     */
    private function seed(string $stat, int $score, int|string|null $seed): int
    {
        if (is_int($seed)) {
            return $seed;
        }

        return crc32($seed ?? $stat.'|'.$score);
    }
}
