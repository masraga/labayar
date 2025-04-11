<?php

namespace Koderpedia\Labayar\Routes;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use Koderpedia\Labayar\Payment;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "api" middleware group. Make something great!
|
*/

Route::post("/api/labayar/pay", function (Request $request) {
  return Payment::pay($request->all());
});
