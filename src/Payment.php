<?php

namespace Koderpedia\Labayar;

use Error;
use Koderpedia\Labayar\Libraries\PDF;
use Koderpedia\Labayar\Models\LabayarStore;
use Koderpedia\Labayar\Services\Payments\Providers\IProvider;
use Koderpedia\Labayar\Services\Payments\Providers\Labayar\Labayar;
use Koderpedia\Labayar\Repositories\Payment as PaymentRepo;
use Koderpedia\Labayar\Repositories\Store;
use Koderpedia\Labayar\Services\Payments\Providers\IManualPay;
use Koderpedia\Labayar\Services\Payments\Providers\IPaymentGateway;
use Koderpedia\Labayar\Services\Payments\Providers\Tripay\Tripay;
use Koderpedia\Labayar\Utils\Constants;
use Koderpedia\Labayar\Utils\Str;

class Payment
{

  /**
   * Default payment provider
   * 
   * @param string $provider Payment provider
   */
  private IProvider|IManualPay|IPaymentGateway $provider;

  public function __construct(string $provider = "labayar", array $ops = [])
  {
    $this->buildGateway($provider, $ops);
  }

  /**
   * Initiate payment gateway instance to $provider attribute
   * 
   * @param string $provider Gateway provider
   * @param mixed $ops Options for provider
   * @return void
   */
  private function buildGateway(string $provider, array $ops = [])
  {
    if ($provider == "labayar") {
      $this->provider = new Labayar();
    } elseif ($provider == "tripay") {
      $this->provider = new Tripay();
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
    $payment = [
      "method" => "cash",
      "type" => "cash"
    ];
    if (isset($request["payment"])) {
      if (isset($request["payment"]["method"]) && isset($request["payment"]["type"])) {
        $payment["method"] = $request["payment"]["method"];
        $payment["type"] = $request["payment"]["type"];
      }
    }
    /**
     * set default storeId for lababar v1
     */
    $store = Store::createOrFind([]);
    $request["customer"]["storeId"] = $store["store_id"];
    $this->provider
      ->setPaymentMethod($payment["method"], $payment["type"])
      ->setOrderId($request["orderId"])
      ->setExpired($request["expiry"]["duration"], $request["expiry"]["unit"])
      ->setCustomer($request["customer"])
      ->setItems($request["items"]);
    if ($this->provider::getGateway() == "labayar") {
      $payAmount = (isset($request["payAmount"])) ? intval($request["payAmount"]) : 0;
      $this->provider->setPayAmount($payAmount);
    };
    $newPayment = $this->provider->create();

    /**
     * is necessary if we use payment gateway, cause payment selector from FE has been mapped
     * with supported payment method of payment gateway. A selector must be save to db
     */
    if (isset($request["payment"]["selector"])) {
      $newPayment["paymentSelector"] = $request["payment"]["selector"];
    }
    $payment = PaymentRepo::createTransaction($newPayment);
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
    $orderId = null;
    if (array_key_exists("paymentId", $request)) {
      $orderId = $request["paymentId"];
    };
    if (!$orderId) {
      if (isset($request["useBuiltIn"])) {
        return redirect("/api/labayar/orders");
      }
    }
    $payment = PaymentRepo::getPayment(["orderId" => $orderId, "oneRow" => true]);
    $payment["expiredAt"] = $payment["expired_at"];
    $payment["payAmount"] = Str::toInt($request["amount"]);
    $payment["storeId"] = $payment["store_id"];
    $payment["orderId"] = $payment["order_id"];
    $payment["invoiceId"] = $payment["invoice_id"];
    $payment["paymentStatus"] = $payment["payment_status"];
    $payment["orderAmount"] = intval($payment["invoice"]["order_amount"]);
    $payment["isManualPay"] = isset($request["isManualPay"]) ? (bool)$request["isManualPay"] : true;
    $payment["method"] = $payment["payment_method"];
    $payment["type"] = $payment["payment_type"];
    if ($payment["gateway"] == Labayar::getGateway()) {
      $provider = new Labayar();
    }
    if ($payment["gateway"] == Tripay::getGateway()) {
      $provider = new Tripay();
    }
    $bill = PaymentRepo::pay($provider->setPaymentMethod($payment["method"], $payment["type"])->pay($payment));
    if (isset($request["useBuiltIn"])) {
      return redirect("/api/labayar/payment-status/" . $payment["order_id"]);
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
  public static function UISalesGraph(array $request)
  {
    $graph = json_encode(PaymentRepo::getSalesReport(["reportType" => "monthly"]));
    return view("labayar::payment-graph", ["graph" => $graph]);
  }

  /**
   * API sales report diagram
   * 
   * @param mixed $request
   * @return mixed
   */
  public static function APISalesGraph(array $request)
  {
    return PaymentRepo::getSalesReport($request);
  }

  /**
   * Payment selector page to select method of payment gateway
   * 
   * @param mixed $request
   * @return view
   */
  public function UISnapLabayar(array $request)
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
    $payload = $this->provider
      ->setOrderId($request["orderId"])
      ->setExpired($request["expiry"]["duration"], $request["expiry"]["unit"])
      ->setCustomer($request["customer"])
      ->setItems($request["items"])
      ->useDefaultPaymentMethod()
      ->loadPaymentSelector();
    $data = [
      "channels" => $payload["paymentChannel"],
      "expiry" => $request["expiry"],
      "gateway" => $this->provider->getGateway(),
      "amount" => intval($payload["amount"]),
      "items" => $payload["items"],
      "orderId" => $payload["orderId"],
      "customer" => $payload["customer"],
      "createdAt" => date("d/m/Y H:i:s")
    ];
    return view("labayar::payment-selector", $data);
  }

  /**
   * Create snap payment transaction, this method only can use
   * after UISnapLabayar is fulfilled
   * 
   * @param mixed $request
   * @return mixed
   */
  public function createSnapTransaction(array $request)
  {
    $payload = [];
    $orderId = $request["orderId"];
    $payload["orderId"] = $orderId;
    $payload["customer"]["name"] = $request["customerName"];
    $payload["customer"]["email"] = $request["customerEmail"];
    $payload["customer"]["phone"] = $request["customerPhone"];
    $payload["expiry"] = [
      "duration" => $request["expiryDuration"],
      "unit" => $request["expiryUnit"],
    ];
    $payload["payment"] = $this->provider->mapPaymentMethod($request["paymentMethod"], $request["paymentType"]);
    $payload["payment"]["selector"]["image"] = $request["paymentImage"];
    $payload["payment"]["selector"]["name"] = $request["paymentName"];
    for ($i = 0; $i < count($request["itemName"]); $i++) {
      $payload["items"][] = [
        "name" => $request["itemName"][$i],
        "quantity" => $request["itemQuantity"][$i],
        "price" => $request["itemPrice"][$i],
        "productId" => $request["itemId"][$i],
      ];
    }
    $payload["items"][] = [
      "productId" => "adminFee001",
      "name" => "Admin Fee",
      "quantity" => 1,
      "price" => intval($request["adminFee"]),
      "type" => Constants::$adminFee
    ];
    $invoice = $this->createInvoice($payload);
    return redirect("/api/labayar/payments/{$orderId}");
  }

  /**
   * Handle payment gateway notification
   * 
   * @param mixed $request
   * @return mixed
   */
  public static function gatewayNotif(array $request)
  {
    $paymentId = null;
    if (isset($request["merchant_ref"])) {
      $paymentId = $request["merchant_ref"];
    };
    if (!$paymentId) {
      return false;
    }
    $payment = PaymentRepo::getPayment(["paymentId" => $paymentId, "oneRow" => true]);
    $provider = null;
    if (!$payment) {
      return false;
    }
    if ($payment["gateway"] == "tripay") {
      $provider = new Tripay();
    }
    $paymentStatus = $provider->useDefaultPaymentMethod()->pay([
      "paymentId" => $paymentId,
      "isManualPay" => false,
      "reference" => $request["reference"]
    ]);
    if ($paymentStatus["success"]) {
      if ($paymentStatus["status"] == Constants::$paymentUnpaid) {
        return false;
      }
      return self::pay([
        "paymentId" => $paymentId,
        "isManualPay" => false,
        "amount" => $request["total_amount"],
      ]);
    }
    return false;
  }

  /**
   * Download selected invoice to pdf
   * 
   * @param mixed $request
   * @return
   */
  public static function downloadInvoice(array $request)
  {
    $order = PaymentRepo::getOrder(["invoiceId" => $request["invoiceId"], "oneRow" => true]);
    $store = LabayarStore::first();
    $filename = $order["invoice_id"] . "-" . date("d-m-Y H:i:s") . ".pdf";
    $html = view("labayar::download-invoice", compact("order", "store", 'filename'))->render();
    $pdf = new PDF();
    $pdf->loadHtml($html);
    $pdf->render();
    return $pdf->stream($filename);
  }
}
