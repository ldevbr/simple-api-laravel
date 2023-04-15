<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ClientController;

Route::group(['middleware' => ['auth:sanctum']], function(){
    Route::apiResource('clients', ClientController::class);
});