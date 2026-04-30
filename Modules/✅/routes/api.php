<?php

use Illuminate\Support\Facades\Route;
use Modules\✅\Http\Controllers\✅Controller;

Route::middleware(['auth:sanctum'])->prefix('v1')->group(function () {
    Route::apiResource('✅s', ✅Controller::class)->names('✅');
});
