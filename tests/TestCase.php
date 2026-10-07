<?php

declare(strict_types=1);

namespace Horoscope\Horoscope\Tests;

use Horoscope\Horoscope\HoroscopeServiceProvider;
use Orchestra\Testbench\TestCase as Orchestra;

abstract class TestCase extends Orchestra
{
    protected function getPackageProviders($app): array
    {
        return [
            HoroscopeServiceProvider::class,
        ];
    }
}
