<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\URL;

class AppServiceProvider extends ServiceProvider
{
    // query class bind
    protected $queries = [
        \App\Queries\Contracts\UserQuery::class => \App\Queries\Eloquent\UserQuery::class,
        \App\Queries\Contracts\ReminderQuery::class => \App\Queries\Eloquent\ReminderQuery::class,
    ];

    // repository class bind
    protected $repositories = [
        \App\Repositories\Contracts\UserInterface::class => \App\Repositories\Eloquent\UserRepository::class,
        \App\Repositories\Contracts\ReminderInterface::class => \App\Repositories\Eloquent\ReminderRepository::class,
    ];

    /**
     * Register any application services.
     *
     * @return void
     */
    public function register(): void
    {
        // Overwrites the UserCrudController provided by the PermissionManager
        $this->app->bind(
            \Backpack\PermissionManager\app\Http\Controllers\UserCrudController::class,
            \App\Http\Controllers\Admin\UserCrudController::class
        );
    }

    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot(): void
    {
        $this->checkSecure();
        $this->bindQuery();
        $this->bindRepository();
    }

    /**
     * ssl check
     *
     * @return void
     */
    protected function checkSecure(): void
    {
        if (request()->isSecure() || strpos(config('app.url', 'http://localhost'), 'https://') === 0) {
            URL::forceScheme('https');
        }
    }

    /**
     *
     * @return void
     */
    protected function bindQuery(): void
    {
        foreach ($this->queries as $abstract => $class) {
            $this->app->bind($abstract, $class);
        }
    }

    /**
     *
     * @return void
     */
    protected function bindRepository(): void
    {
        foreach ($this->repositories as $abstract => $class) {
            $this->app->bind($abstract, $class);
        }
    }
}