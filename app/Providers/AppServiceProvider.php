<?php

namespace App\Providers;

use App\Models\Persona;
use App\Observers\MovimientoObserver;
use Illuminate\Pagination\Paginator;
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
       Paginator::useBootstrap();
       Persona::observe(MovimientoObserver::class);
    }
}
