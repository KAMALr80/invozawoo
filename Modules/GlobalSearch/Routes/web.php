<?php

use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Global Search Module Routes
|--------------------------------------------------------------------------
| Keep middleware generic so this module can work in different ERPs.
| If your ERP has extra middleware, add it in config/globalsearch.php.
*/

$middleware = config('globalsearch.web_middleware', ['web', 'auth']);

Route::group(['middleware' => $middleware], function () {
    Route::get('/global-search', 'SearchController@globalSearch')->name('global.search');
    Route::get('/global-search-config', 'SearchController@globalSearchConfig')->name('global.search.config');

    Route::get('/global-search-module/settings', 'SearchController@globalSearchSettingsIndex')
        ->name('global_search_settings.index');

    Route::post('/global-search-module/settings', 'SearchController@globalSearchSettingsStore')
        ->name('global_search_settings.store');
});
