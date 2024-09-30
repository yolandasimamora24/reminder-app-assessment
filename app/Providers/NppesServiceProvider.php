<?php

namespace App\Providers;

use App\Actions\Nppes\Client;
use Illuminate\Support\ServiceProvider;

class NppesServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        $this->app->singleton(
            Client::class,
            fn() => new Client(
                base_url: config('nppes.base_url'),
                api_version: config('nppes.api_version'),
            )
        );
    }

    public function boot()
    {
        //
    }
}
