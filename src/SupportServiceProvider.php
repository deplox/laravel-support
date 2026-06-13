<?php

declare(strict_types=1);

namespace Deplox\Support;

use Deplox\Support\Commands\RouteShowCommand;
use Illuminate\Support\ServiceProvider;
use Override;

final class SupportServiceProvider extends ServiceProvider
{
    #[Override]
    public function register(): void {}

    public function boot(): void
    {
        $this->loadTranslationsFrom(__DIR__.'/../resources/lang', 'support');

        if ($this->app->runningInConsole()) {
            $this->commands([RouteShowCommand::class]);

            $this->publishes([
                __DIR__.'/../resources/lang' => $this->app->langPath('vendor/support'),
            ], 'laravel-support-translations');
        }
    }
}
