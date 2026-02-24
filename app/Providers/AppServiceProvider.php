<?php

namespace App\Providers;

use App\Auth\SupabaseGuard;
use App\Auth\SupabaseUserProvider;
use App\Database\SupabasePostgresConnector;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        // Override the pgsql connector to support Supabase pooler reference
        $this->app->bind('db.connector.pgsql', function () {
            return new SupabasePostgresConnector();
        });
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        // Registar provider customizado para Supabase
        Auth::provider('supabase', function ($app, array $config) {
            return new SupabaseUserProvider();
        });

        // Registar guard customizado para Supabase
        Auth::extend('supabase', function ($app, $name, array $config) {
            $provider = Auth::createUserProvider($config['provider']);
            return new SupabaseGuard($provider, $app['request']);
        });
    }
}
