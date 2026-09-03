<?php

use Illuminate\Support\Facades\Route;

Route::get('/{any}', function () {
    return response()->json(['message' => 'NUVEX GLOBAL API. Use /api/* endpoints.'], 200);
})->where('any', '^(?!api).*');
