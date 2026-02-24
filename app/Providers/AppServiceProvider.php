<?php

namespace App\Providers;

use App\Auth\SupabaseGuard;
use App\Auth\SupabaseUserProvider;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
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
