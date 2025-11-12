<?php

use App\Http\Controllers\Api\StudentApiController;
use Illuminate\Support\Facades\Route;

// Route::get('/user', function (Request $request) {
//     return $request->user();
// })->middleware('auth:sanctum');

Route::apiResource('/students', StudentApiController::class);
