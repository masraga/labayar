<?php

namespace Koderpedia\Labayar\Repositories;

use Carbon\Carbon;
use Error;
use Exception;
use Illuminate\Support\Facades\DB;
use Koderpedia\Labayar\Models\LabayarInvoice;
use Koderpedia\Labayar\Models\LabayarInvoiceItem;
use Koderpedia\Labayar\Models\LabayarInvoiceMetadata;
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
      /**
       * condition to create invoice metadata
       */
      $addMetadata = false;

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
      $adminFee = 0;
      $subTotalFee = 0;
      foreach ($payload["items"] as $item) {
        $type = isset($item["type"]) ? $item["type"] : Constants::$sellItem;
        $itemPayload[] = [
          "invoice_id" => $invoice->invoice_id,
          "name" => $item["name"],
          "quantity" => intval($item["quantity"]),
          "price" => intval($item["price"]),
          "gross_total" => intval($item["quantity"]) * intval($item["price"]),
          "type" => $type
        ];

        /** 
         * Get admin fee of payment
         */
        if ($type == Constants::$adminFee) {
          $adminFee += (int)((int)$item["quantity"] * (int)$item["price"]);
          $addMetadata = true;
        }
        /**
         * Get subtotalfee
         */
        if ($type == Constants::$sellItem) {
          $subTotalFee += (int)((int)$item["quantity"] * (int)$item["price"]);
          $addMetadata = true;
        }
      }
      LabayarInvoiceItem::insert($itemPayload);

      /**
       * is used for save payment gateway method
       */
      $paymentSelector = [];
      if (isset($payload["paymentSelector"])) {
        $paymentSelector["method"] = $payload["paymentSelector"]["method"];
        $paymentSelector["type"] = $payload["paymentSelector"]["type"];
        $paymentSelector["image"] = $payload["paymentSelector"]["image"];
        $paymentSelector["name"] = $payload["paymentSelector"]["name"];
      }
      /**
       * Save metadata to db
       */
      if ($addMetadata) {
        $metadata = [];
        if ($adminFee > 0) {
          $metadata[] = [
            "invoice_id" => $invoice->invoice_id,
            "key" => Constants::$adminFee,
            "value" => intval($adminFee)
          ];
        }
        if ($subTotalFee > 0) {
          $metadata[] = [
            "invoice_id" => $invoice->invoice_id,
            "key" => Constants::$subTotal,
            "value" => intval($subTotalFee)
          ];
        }
        if (count($paymentSelector) > 0) {
          if (isset($payload["paymentGatewayResult"])) {
            $metadata[] = [
              "invoice_id" => $invoice->invoice_id,
              "key" => Constants::$isPaymentGateway,
              "value" => true
            ];
            $metadata[] = [
              "invoice_id" => $invoice->invoice_id,
              "key" => Constants::$gatewayMerchantName,
              "value" => $payload["paymentGatewayResult"][Constants::$gatewayMerchantName]
            ];
            $metadata[] = [
              "invoice_id" => $invoice->invoice_id,
              "key" => Constants::$gatewayMerchantCode,
              "value" => $payload["paymentGatewayResult"][Constants::$gatewayMerchantCode]
            ];
          }
          $metadata[] = [
            "invoice_id" => $invoice->invoice_id,
            "key" => Constants::$paymentMethod,
            "value" => $paymentSelector["method"]
          ];
          $metadata[] = [
            "invoice_id" => $invoice->invoice_id,
            "key" => Constants::$paymentType,
            "value" => $paymentSelector["type"]
          ];
          $metadata[] = [
            "invoice_id" => $invoice->invoice_id,
            "key" => Constants::$paymentGatewayImage,
            "value" => $paymentSelector["image"]
          ];
          $metadata[] = [
            "invoice_id" => $invoice->invoice_id,
            "key" => Constants::$paymentTypeName,
            "value" => $paymentSelector["name"]
          ];
        }
        if (count($metadata) > 0) {
          LabayarInvoiceMetadata::insert($metadata);
        }
      }
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
    $payment = LabayarInvoicePayment::with(["invoice", "metadata"]);
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
      "is_manual_pay" => $payload["isManualPay"],
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
    $table = LabayarInvoice::with(["customer", "store", "item", "metadata"]);
    if (isset($payload["invoiceId"])) {
      $table->where("invoice_id", $payload["invoiceId"]);
    }
    if (isset($payload["keyword"])) {
      $table->where("invoice_id", "like", $payload["keyword"]);
    }
    if (isset($payload["createdAtRange"])) {
      if (is_array($payload["createdAtRange"])) {
        $table->whereBetween('created_at', [$payload["createdAtRange"]["dateStart"], $payload["createdAtRange"]["dateEnd"]]);
      }
    }
    if (isset($payload["oneRow"])) {
      return (array) $table->get()->first()->toArray();
    }
    return (array) $table->get()->toArray();
  }

  /**
   * Get sales report weekly, monthly, yearly. default report is weekly
   * 
   * @param mixed $filter Query filter
   * @return mixed
   */
  public static function getSalesReport(array $filter = []): array
  {
    $reportType = "weekly";
    $dateStart = Carbon::now()->subWeek();
    $validType = ["weekly", "monthly", "yearly"];
    $labels = [];
    if (isset($filter["reportType"])) {
      if (in_array($filter["reportType"], $validType)) {
        $reportType = $filter["reportType"];
      }
    }
    if ($reportType == "weekly") {
      $dateStart = Carbon::now()->subWeek();
    } elseif ($reportType == "monthly") {
      $dateStart = Carbon::now()->subMonth();
    } elseif ($reportType == "yearly") {
      $dateStart = Carbon::now()->subYear();
    }

    $orders = self::getOrder([
      "createdAtRange" => [
        "dateStart" => $dateStart->toDateTimeString(),
        "dateEnd" => Carbon::now()->toDateTimeString()
      ]
    ]);

    if (in_array($reportType, ["weekly", "monthly"])) {
      while ($dateStart->isBefore(Carbon::now())) {
        $labels[$dateStart->toDateString()] = ["paid" => 0, "unpaid" => 0];
        $dateStart->addDays(1);
      }
    }

    foreach ($orders as $order) {
      $createdAt = date("Y-m-d", strtotime($order["created_at"]));
      if ($order["payment_status"] == Constants::$paymentPaid) {
        $labels[$createdAt]["paid"] += $order['order_amount'];
      } elseif ($order["payment_status"] == Constants::$paymentUnpaid) {
        $labels[$createdAt]["unpaid"] += $order['order_amount'];
      }
    }
    $result = [
      "labels" => [],
      "paid" => [],
      "unpaid" => []
    ];
    foreach ($labels as $date => $value) {
      $result["labels"][] = $date;
      $result["paid"][] = $labels[$date]["paid"];
      $result["unpaid"][] = $labels[$date]["unpaid"];
    }
    return $result;
  }
}
