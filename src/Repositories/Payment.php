<?php

namespace Koderpedia\Labayar\Repositories;

use Carbon\Carbon;
use Error;
use Exception;
use Illuminate\Support\Facades\DB;
use Koderpedia\Labayar\Models\LabayarInvoice;
use Koderpedia\Labayar\Models\LabayarInvoiceItem;
use Koderpedia\Labayar\Models\LabayarInvoicePayment;
use Koderpedia\Labayar\Utils\Constants;

class Payment
{
  /**
   * Save order transaction to database
   * 
   * @param mixed $payload
   * @return mixed
   */
  public static function createTransaction(array $payload)
  {
    try {
      DB::beginTransaction();
      $currentPayment = LabayarInvoice::where("invoice_id", $payload["orderId"])->first();
      if ($currentPayment) {
        throw new Error("Duplicate orderId " . $payload["orderId"]);
      }
      $customer = Customer::createOrFind([
        "name" => $payload["customer"]["name"],
        "email" => $payload["customer"]["email"],
        "phone" => $payload["customer"]["phone"],
        "storeId" => $payload["customer"]["storeId"]
      ]);
      $invoice = LabayarInvoice::create([
        "invoice_id" => $payload["orderId"],
        "customer_id" => $customer["customer_id"],
        "store_id" => $customer["store_id"],
        "order_amount" => intval($payload["amount"]),
      ]);
      $payAmount = (isset($payload["payAmount"])) ? intval($payload["payAmount"]) :  intval($payload["amount"]);
      $changeAmount = ($payAmount - $payload["amount"] > 0) ? $payAmount - $payload["amount"] : 0;
      $payment = LabayarInvoicePayment::create([
        "store_id" => $customer["store_id"],
        "invoice_id" => $invoice->invoice_id,
        "order_id" => $payload["paymentId"],
        "amount" => $payAmount,
        "gateway" => $payload["gateway"],
        "payment_method" => $payload["paymentMethod"],
        "payment_type" => $payload["paymentType"],
        "expired_at" => $payload["expiredAt"],
        "nett_amount" => intval($payload["amount"]),
        "change" => $changeAmount
      ]);
      $itemPayload = [];
      foreach ($payload["items"] as $item) {
        $itemPayload[] = [
          "invoice_id" => $invoice->invoice_id,
          "name" => $item["name"],
          "quantity" => intval($item["quantity"]),
          "price" => intval($item["price"]),
        ];
      }
      LabayarInvoiceItem::insert($itemPayload);
      DB::commit();
      return $payload;
    } catch (Exception $e) {
      DB::rollBack();
      throw $e;
    }
  }

  /**
   * Get peyment detail of invoice
   * 
   * @param mixed $payload Payment filter
   * @return mixed
   */
  public static function getPayment(array $payload): array
  {
    $payment = LabayarInvoicePayment::with(["invoice"]);
    if (isset($payload["orderId"])) {
      $payment->where("order_id", $payload["orderId"]);
    }
    if (isset($payload["invoiceId"])) {
      $payment->where("invoice_id", $payload["invoiceId"]);
    }
    if (isset($payload["paymentStatus"])) {
      $payment->where("payment_status", $payload["paymentStatus"]);
    }
    $payment = $payment->get();
    if (isset($payload["pagination"])) {
      if ($payload["pagination"]["page"] && $payload["pagination"]["perPage"]) {
        $payment->forPage($payload["pagination"]["page"], $payload["pagination"]["perPage"]);
      }
    }
    if (isset($payload["oneRow"])) {
      return (array) $payment->first()->toArray();
    }
    return (array) $payment->toArray();
  }

  /**
   * Pay invoice based on orderId
   * 
   * @param mixed $paload Payment payload
   * @return mixed
   */
  public static function pay(array $payload): array
  {
    $payDate = Carbon::parse(date("Y-m-d H:i:s"));
    $expiredDate = Carbon::parse($payload["expiredAt"]);
    $isExpired = $payDate->greaterThan($expiredDate);
    if ($isExpired) {
      throw new Error("Payment is expired");
    }
    if (intval($payload["payAmount"]) < intval($payload["amount"])) {
      throw new Error("Pay amount valid");
    }
    if ($payload["paymentStatus"] == Constants::$paymentPaid) {
      return $payload;
    }
    $pay = LabayarInvoicePayment::where([
      "store_id" => $payload["storeId"],
      "order_id" => $payload["orderId"]
    ])->update([
      "payment_status" => Constants::$paymentPaid,
      "paid_date" => date("Y-m-d H:i:s")
    ]);

    $allAmount = 0;
    $allPayment = self::getPayment(["invoiceId" => $payload["invoiceId"], "paymentStatus" => Constants::$paymentPaid]);
    foreach ($allPayment as $payment) {
      $allAmount += intval($payment["amount"]) - intval($payment["change"]);
    }
    if ($allAmount >= $payload["orderAmount"]) {
      LabayarInvoice::where(["invoice_id" => $payload["invoiceId"]])->update([
        "payment_status" => Constants::$paymentPaid
      ]);
    }
    return $payload;
  }

  /**
   * Get purchase invoice
   * 
   * @param mixed $payload Filter query
   * @return mixed
   */
  public static function getOrder(array $payload): array
  {
    $table = LabayarInvoice::with(["customer", "store", "item"]);
    if (isset($payload["invoiceId"])) {
      $table->where("invoice_id", $payload["invoiceId"]);
    }
    if (isset($payload["keyword"])) {
      $table->where("invoice_id", "like", $payload["keyword"]);
    }
    if (isset($payload["oneRow"])) {
      return (array) $table->get()->first()->toArray();
    }
    return (array) $table->get()->toArray();
  }
}
