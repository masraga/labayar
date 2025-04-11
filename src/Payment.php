<?php

namespace Koderpedia\Labayar;

use Error;
use Koderpedia\Labayar\Services\Payments\Providers\IProvider;
use Koderpedia\Labayar\Services\Payments\Providers\Labayar\Labayar;
use Koderpedia\Labayar\Repositories\Payment as PaymentRepo;
use Koderpedia\Labayar\Repositories\Store;
use Koderpedia\Labayar\Services\Payments\Providers\IManualPay;

class Payment
{

  /**
   * Default payment provider
   * 
   * @param string $provider Payment provider
   */
  private IProvider|IManualPay $provider;

  public function __construct(string $provider = "labayar")
  {
    if ($provider == "labayar") {
      $this->provider = new Labayar();
    } else {
      throw new Error("$provider not supported");
    }
  }

  /**
   * Creating payment for every transaction
   * 
   * @param mixed $request
   * @return mixed
   */
  public function createInvoice(array $request)
  {
    if (!array_key_exists("orderId", $request)) {
      throw new Error("Error: missing parameter orderId");
    }
    if (!array_key_exists("customer", $request)) {
      throw new Error("Error: missing parameter customer");
    }
    if (!array_key_exists("items", $request)) {
      throw new Error("Error: missing parameter items");
    }
    if (!array_key_exists("expiry", $request)) {
      throw new Error("Error: missing parameter expiry");
    }
    /**
     * set default storeId for lababar v1
     */
    $store = Store::createOrFind([]);
    $request["customer"]["storeId"] = $store["store_id"];
    $this->provider
      ->setPaymentMethod("cash", "cash")
      ->setOrderId($request["orderId"])
      ->setExpired($request["expiry"]["duration"], $request["expiry"]["unit"])
      ->setCustomer($request["customer"])
      ->setItems($request["items"]);
    if ($this->provider::getGateway() == "labayar") {
      $payAmount = (isset($request["payAmount"])) ? intval($request["payAmount"]) : 0;
      $this->provider->setPayAmount($payAmount);
    };
    $payment = PaymentRepo::createTransaction($this->provider->create());
    return $payment;
  }

  /**
   * Pay invoice order based on payment request
   * Set provider depending request from client. dont use default \Provider
   * 
   * @param mixed $request
   * @return mixed
   */
  public static function pay(array $request)
  {
    $provider = null;
    $orderId = "";
    if (array_key_exists("orderId", $request)) {
      $orderId = $request["orderId"];
    };
    $payment = PaymentRepo::getPayment(["orderId" => $orderId, "oneRow" => true]);
    $payment["expiredAt"] = $payment["expired_at"];
    $payment["payAmount"] = $request["amount"];
    $payment["storeId"] = $payment["store_id"];
    $payment["orderId"] = $payment["order_id"];
    $payment["invoiceId"] = $payment["invoice_id"];
    $payment["paymentStatus"] = $payment["payment_status"];
    $payment["orderAmount"] = intval($payment["invoice"]["order_amount"]);
    if ($payment["gateway"] == Labayar::getGateway()) {
      $provider = new Labayar();
    }

    $bill = PaymentRepo::pay($provider->pay($payment));

    return $bill;
  }
}
