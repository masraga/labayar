<?php

namespace Koderpedia\Labayar;

use Error;
use Koderpedia\Labayar\Services\Payments\Providers\IProvider;
use Koderpedia\Labayar\Services\Payments\Providers\Labayar\Labayar;
use Koderpedia\Labayar\Repositories\Payment as PaymentRepo;
use Koderpedia\Labayar\Repositories\Store;
use Koderpedia\Labayar\Services\Payments\Providers\IManualPay;
use Koderpedia\Labayar\Utils\Str;

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
    if (array_key_exists("paymentId", $request)) {
      $orderId = $request["paymentId"];
    };
    $payment = PaymentRepo::getPayment(["orderId" => $orderId, "oneRow" => true]);
    $payment["expiredAt"] = $payment["expired_at"];
    $payment["payAmount"] = Str::toInt($request["amount"]);
    $payment["storeId"] = $payment["store_id"];
    $payment["orderId"] = $payment["order_id"];
    $payment["invoiceId"] = $payment["invoice_id"];
    $payment["paymentStatus"] = $payment["payment_status"];
    $payment["orderAmount"] = intval($payment["invoice"]["order_amount"]);
    if ($payment["gateway"] == Labayar::getGateway()) {
      $provider = new Labayar();
    }

    $bill = PaymentRepo::pay($provider->pay($payment));
    if(isset($request["useBuiltIn"])){
      return redirect("/api/labayar/payment-status/".$payment["order_id"]);
    }
    return $bill;
  }

  /**
   * Show default order page
   * 
   * @param mixed $request
   * @return view
   */
  public static function UIListOrder(array $request)
  {
    $orders = PaymentRepo::getOrder($request);
    return view("labayar::invoice", compact("orders"));
  }

  /**
   * API list available invoice
   * 
   * @param mixed $filter
   * @return mixed
   */
  public static function APIListOrder(array $filter): array
  {
    return PaymentRepo::getOrder($filter);
  }

  /**
   * Default payment page, show payment history and add new payment
   * 
   * @param mixed $request
   * @return view
   */
  public static function UIListPayments(array $request)
  {
    $request["oneRow"] = true;
    $order = PaymentRepo::getOrder($request);
    $payments = PaymentRepo::getPayment(["invoiceId" => $request["invoiceId"]]);
    return view("labayar::payment", compact("order", "payments"));
  }

  /**
   * Show form pay invoice page, currently is not supported for this version
   * 
   * @param mixed $request
   * @return view
   */
  public static function UIPay(array $request)
  {
    $payment = PaymentRepo::getPayment([
      "oneRow" => true,
      "orderId" => $request["orderId"]
    ]);
    return view("labayar::pay", compact("payment"));
  }

  /**
   * Show payment status page
   * 
   * @param mixed $requset
   * @return view
   */
  public static function UIPaymentStatus(array $request)
  {
    $payment = PaymentRepo::getPayment([
      "oneRow" => true,
      "orderId" => $request["orderId"]
    ]);
    return view("labayar::payment-status", compact("payment"));
  }

  /**
   * Show sales diagram
   * 
   * @param mixed $request
   */
  public static function UISalesGraph(array $request){
    $graph = json_encode(PaymentRepo::getSalesReport(["reportType" => "monthly"]));
    return view("labayar::payment-graph", ["graph" => $graph]);
  }

  /**
   * API sales report diagram
   * 
   * @param mixed $request
   * @return mixed
   */
  public static function APISalesGraph(array $request){
    return PaymentRepo::getSalesReport($request);
  }
}
