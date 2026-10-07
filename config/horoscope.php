<?php

declare(strict_types=1);

return [

    'cache' => [

        /*
         * Determine whether generated readings are stored in the cache.
         */
        'enabled' => true,

        /*
         * The cache store used to keep readings. When null, the application's
         * default cache store is used.
         */
        'store' => null,

        /*
         * The prefix applied to every horoscope cache key.
         */
        'prefix' => 'horoscope',

        /*
         * How long a cached reading should live.
         *
         * - 'end-of-day': expire at the end of the reading date
         * - int: number of seconds
         * - null: cache the reading forever
         */
        'ttl' => 'end-of-day',

    ],

];
