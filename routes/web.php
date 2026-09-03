<?php

use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return response()->json([
        'name' => 'NUVEX GLOBAL',
        'type' => 'Laravel Backend API',
        'status' => 'Running',
        'version' => '1.0.0',
        'docs' => '/api/health',
    ]);
});

Route::get('/{any}', function () {
    return response()->json(['message' => 'NUVEX GLOBAL API. Use /api/* endpoints.'], 200);
})->where('any', '^(?!api).*');
