<?php

declare(strict_types=1);

namespace Horoscope\Horoscope\Facades;

use Illuminate\Support\Facades\Facade;

/**
 * @see \Horoscope\Horoscope\Horoscope
 */
class Horoscope extends Facade
{
    protected static function getFacadeAccessor(): string
    {
        return \Horoscope\Horoscope\Horoscope::class;
    }
}
