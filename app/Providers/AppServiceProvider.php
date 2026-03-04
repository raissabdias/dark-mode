<?php

namespace App\Providers;

use App\Support\CustomTiptapConverter;
use FilamentTiptapEditor\TiptapConverter;
use Illuminate\Support\Facades\URL;
use Illuminate\Support\ServiceProvider;
use App\Models\User;
use App\Observers\UserObserver;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        // Override default converter to keep highlight color attrs during source/save round-trips.
        $this->app->singleton('tiptap-converter', fn () => new CustomTiptapConverter());
        $this->app->singleton(TiptapConverter::class, fn () => new CustomTiptapConverter());
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        User::observe(UserObserver::class);

        if ($this->app->environment('production')) {
            URL::forceScheme('https');
            $this->app['request']->server->set('HTTPS', 'on');
        }
    }
}
