<?php

namespace Koderpedia\Labayar\Repositories;

use Error;
use Koderpedia\Labayar\Models\LabayarInvoice;
use Koderpedia\Labayar\Models\LabayarInvoiceItem;
use Koderpedia\Labayar\Models\LabayarInvoicePayment;

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
      "expired_at" => $payload["expiredAt"]
    ]);
    $payment = LabayarInvoicePayment::create([
      "invoice_id" => $payload["orderId"],
      "amount" => intval($payload["amount"]),
      "gateway" => $payload["gateway"],
      "payment_method" => $payload["paymentMethod"],
      "payment_type" => $payload["paymentType"],
    ]);
    $itemPayload = [];
    foreach ($payload["items"] as $item) {
      $itemPayload[] = [
        "invoice_id" => $payload["orderId"],
        "name" => $item["name"],
        "quantity" => intval($item["quantity"]),
        "price" => intval($item["price"]),
      ];
    }
    LabayarInvoiceItem::insert($itemPayload);

    return $payload;
  }
}
