<?php

namespace Modules\GlobalSearch\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Database\Eloquent\Factory;
use Modules\GlobalSearch\Http\Middleware\InjectGlobalSearch;

class GlobalSearchServiceProvider extends ServiceProvider
{
    public function boot()
    {
        $this->registerTranslations();
        $this->registerConfig();
        $this->registerViews();
        $this->registerFactories();
        $this->loadMigrationsFrom(__DIR__ . '/../Database/Migrations');

        // Auto inject global search UI + Alt+Shift+S shortcut into every HTML page.
        // This makes the module work without editing app.blade.php in each ERP.
        $this->app['router']->pushMiddlewareToGroup('web', InjectGlobalSearch::class);
    }

    public function register()
    {
        $this->app->register(RouteServiceProvider::class);
    }

    protected function registerConfig()
    {
        $this->publishes([
            __DIR__.'/../Config/config.php' => config_path('globalsearch.php'),
        ], 'config');
        $this->mergeConfigFrom(__DIR__.'/../Config/config.php', 'globalsearch');
    }

    public function registerViews()
    {
        $viewPath = resource_path('views/modules/globalsearch');
        $sourcePath = __DIR__.'/../Resources/views';

        $this->publishes([$sourcePath => $viewPath], 'views');

        $this->loadViewsFrom(array_merge(array_map(function ($path) {
            return $path . '/modules/globalsearch';
        }, \Config::get('view.paths')), [$sourcePath]), 'globalsearch');
    }

    public function registerTranslations()
    {
        $langPath = resource_path('lang/modules/globalsearch');
        if (is_dir($langPath)) {
            $this->loadTranslationsFrom($langPath, 'globalsearch');
        } else {
            $this->loadTranslationsFrom(__DIR__ .'/../Resources/lang', 'globalsearch');
        }
    }

    public function registerFactories()
    {
        if (! app()->environment('production') && $this->app->runningInConsole()) {
            app(Factory::class)->load(__DIR__ . '/../Database/factories');
        }
    }

    public function provides()
    {
        return [];
    }
}
