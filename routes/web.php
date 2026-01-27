<?php

use Illuminate\Support\Facades\Route;

Route::get('/api', function () {
    return ['Laravel' => app()->version()];
});

require __DIR__.'/auth.php';
