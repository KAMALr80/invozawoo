<?php
namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use App\Services\GoogleMapsService;

class MapServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        $this->app->singleton(GoogleMapsService::class, function ($app) {
            return new GoogleMapsService();
        });
    }

    public function boot(): void
    {
        //
    }
}
