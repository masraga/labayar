<?php

namespace Koderpedia\Labayar\Routes;

use Dompdf\Dompdf;
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

Route::get("/api/labayar/pay/{orderId}", function (Request $request, $orderId) {
  $request = array_merge($request->all(), ["orderId" => $orderId]);
  return Payment::UIPay($request);
});

Route::get("/api/labayar/orders", function (Request $request) {
  return Payment::UIListOrder($request->all());
});

Route::get("/api/labayar/payments/graph", function (Request $request) {
  return Payment::UISalesGraph($request->all());
});

Route::get("/api/labayar/payments/{invoiceId}", function (Request $request, $invoiceId) {
  $request = array_merge($request->all(), ["invoiceId" => $invoiceId]);
  return Payment::UIListPayments($request);
});

Route::get("/api/labayar/payment-status/{orderId}", function (Request $request, $orderId) {
  $request = array_merge($request->all(), ["orderId" => $orderId]);
  return Payment::UIPaymentStatus($request);
});

Route::post("/api/labayar/snap", function (Request $request) {
  $payment = new Payment($request["gateway"]);
  return $payment->createSnapTransaction($request->all());
});

Route::post("/api/labayar/gateway/notification", function (Request $request) {
  return Payment::gatewayNotif($request->all());
});

Route::get("/api/labayar/payment/download/{invoiceId}", function (Request $request, $invoiceId) {
  $payload = array_merge($request->all(), ["invoiceId" => $invoiceId]);
  return Payment::downloadInvoice($payload);
});
