<?php

declare(strict_types=1);

namespace Horoscope\Horoscope;

use Illuminate\Support\ServiceProvider;

class HoroscopeServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        $this->mergeConfigFrom(__DIR__.'/../config/horoscope.php', 'horoscope');

        $this->app->singleton(Horoscope::class);
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        $this->loadRoutesFrom(__DIR__.'/../routes/horoscope.php');

        $this->loadViewsFrom(__DIR__.'/../resources/views', 'horoscope');

        $this->loadTranslationsFrom(__DIR__.'/../lang', 'horoscope');

        if (! $this->app->runningInConsole()) {
            return;
        }

        $this->publishes([
            __DIR__.'/../config/horoscope.php' => config_path('horoscope.php'),
        ], ['horoscope', 'horoscope-config']);

        $this->publishes([
            __DIR__.'/../lang' => $this->app->langPath('vendor/horoscope'),
        ], ['horoscope', 'horoscope-lang']);
    }
}
