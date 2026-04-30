<?php

use Illuminate\Support\Facades\Route;
use Modules\✅\Http\Controllers\✅Controller;

Route::middleware(['auth', 'verified'])->group(function () {
    Route::resource('✅s', ✅Controller::class)->names('✅');
});
