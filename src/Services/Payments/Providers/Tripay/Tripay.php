<?php

namespace Koderpedia\Labayar\Services\Payments\Providers\Tripay;

use Error;
use Illuminate\Support\Facades\Validator;
use Koderpedia\Labayar\Libraries\PaymentSelector;
use Koderpedia\Labayar\Services\Payments\Providers\IMethod;
use Koderpedia\Labayar\Services\Payments\Providers\IPaymentGateway;
use Koderpedia\Labayar\Services\Payments\Providers\IProvider;
use Koderpedia\Labayar\Services\Payments\Providers\Tripay\PaymentMethod\CloseTransaction;
use Koderpedia\Labayar\Services\Payments\Traits\PaymentCalculator;
use Koderpedia\Labayar\Utils\Constants;
use Koderpedia\Labayar\Utils\Time;

class Tripay implements IProvider, IPaymentGateway
{
  use PaymentCalculator;
  /**
   * Payment method open transaction
   */
  public const OPEN_TRANSACTION = "open";

  /**
   * Payment method close transaction
   */
  public const CLOSE_TRANSACTION = "close";

  /**
   * Gateway name of provider
   */
  private static string $gateway = "tripay";

  /**
   * Transaction payload
   */
  private array $payload;

  /**
   * Payment method
   */
  private IMethod $payment;

  /**
   * request authorization
   */
  private array $authorization;

  /**
   * Payment base url
   */
  private string $baseUrl;

  public function __construct()
  {
    if (config("tripay.is_production")) {
      $this->baseUrl = "https://tripay.co.id/api";
    } else {
      $this->baseUrl = "https://tripay.co.id/api-sandbox";
    }
    $this->authorization = [
      "Content-Type" => "application/json",
      "Authorization" => "Bearer " . config("tripay.api_key")
    ];
  }

  /**
   * Get payment gateway label name
   * 
   * @return string
   */
  public static function getGateway(): string
  {
    return self::$gateway;
  }

  /**
   * Set order id for every payment method
   * 
   * @param string $id Order id
   * @return $this
   */
  public function setOrderId(string $id = "")
  {
    Validator::make(["orderId" => $id], [
      "orderId" => "required"
    ], [
      "orderId.require" => "Order ID is required"
    ]);
    $this->payload["orderId"] = $id;
    $this->payload["paymentId"] = $id . "-" . time();
    return $this;
  }

  /**
   * Transaction expiry time
   * 
   * @param int $duration Expiry time duration
   * @param string $unit Expiry unit seconds/minutes/hours/days
   */
  public function setExpired(int $duration, string $unit)
  {
    $this->payload["expiredAt"] = Time::add($duration, $unit, false);
    $this->payload["expiredAtUnix"] = Time::add($duration, $unit);
    return $this;
  }

  /**
   * use default payment method of payment provider, that will automatically initiate payment method
   * instance for every payment gateway
   */
  public function useDefaultPaymentMethod()
  {
    $ops = [
      "baseUrl" => $this->baseUrl,
      "authorization" => $this->authorization
    ];
    $this->payment = new CloseTransaction($ops);
    return $this;
  }

  /**
   * Set payment method for every transaction
   * 
   * @param string $method Payment method
   * @param string $type Payment type of payment method
   * @return $this
   */
  public function setPaymentMethod(string $method, string $type)
  {
    $ops = [
      "baseUrl" => $this->baseUrl,
      "authorization" => $this->authorization
    ];
    if ($method != self::OPEN_TRANSACTION && $method != self::CLOSE_TRANSACTION) {
      throw new Error("$method not supported in labayar");
    }
    $this->payload["paymentMethod"] = $method;
    $this->payload["paymentType"] = $type;
    $this->payment = new CloseTransaction($ops);
    return $this;
  }

  /**
   * Get payment method for every transaction
   * 
   * @return IMethod
   */
  public function getPaymentMethod(): IMethod
  {
    return $this->payment;
  }

  /**
   * Set customer for every transaction
   * 
   * @param mixed $customer Customer info
   */
  public function setCustomer(array $customer)
  {
    $validator = Validator::make($customer, [
      "name" => "required|string",
      "email" => "required|email",
      "phone" => "required",
      "storeId" => "required",
    ], [
      "name.required" => "Customer name is required",
      "email.required" => "Customer email is required",
      "phone.required" => "Customer phone is required",
      "storeId.required" => "Customer store id is required",
    ]);
    if ($validator->fails()) {
      throw new Error($validator->errors());
    }
    $this->payload["customer"] = [
      "name" => $customer["name"],
      "email" => $customer["email"],
      "phone" => $customer["phone"],
      "storeId" => $customer["storeId"],
      "address" => $customer["address"] ?? "",
    ];
    return $this;
  }

  /**
   * Set transaction item for every transaction
   * 
   * @param mixed $items order item
   */
  public function setItems(array $items)
  {
    $validator = Validator::make($items, [
      "*.productId" => "required|string",
      "*.name" => "required|string",
      "*.quantity" => "required|numeric",
      "*.price" => "required|numeric",
    ]);
    if ($validator->fails()) {
      throw new Error($validator->errors());
    }
    $this->payload["items"] = $items;
    return $this;
  }

  /**
   * Create transaction for every payment
   * 
   * @return mixed
   */
  public function create(): array
  {
    $order = $this->payment->calculateOrder($this->payload["items"]);
    $this->payload["items"] = $order["items"];
    $this->payload["amount"] = $order["amount"];
    $this->payload["gateway"] = $this->getGateway();
    $paymentGateway = $this->payment->use($this->payload["paymentType"])->createTransaction($this->payload);
    $pgResult = [];
    if (isset($paymentGateway["data"])) {
      $paymentData = $paymentGateway["data"];
      $paymentName = $paymentData["payment_name"];
      /**
       * Hardcode with linkita cause tripay use Linkita to pay bills over the counter
       */
      if (in_array($paymentData["payment_name"], ["Alfamart", "Alfamidi", "Indomaret"])) {
        $paymentName .= " - Linkita";
      }
      $pgResult[Constants::$gatewayMerchantName] = $paymentName;
      $pgResult[Constants::$gatewayMerchantCode] = $paymentGateway["data"]["pay_code"];
    }
    $this->payload["paymentGatewayResult"] = $pgResult;
    return $this->payload;
  }

  /**
   * Pay order based on orderId
   * 
   * @param mixed $payload Payment payload 
   * @return mixed
   */
  public function pay(array $payload): array
  {
    if ($payload["isManualPay"]) {
      return $payload;
    }
    if (!isset($payload["reference"])) {
      return $payload;
    }
    $paymentStatus = $this->payment->getPaymentStatus($payload["reference"]);
    if (!isset($paymentStatus["success"])) {
      return ["success" => false];
    }
    if (!(bool)$paymentStatus["success"]) {
      return ["success" => false];
    }
    if (isset($paymentStatus["data"]["reference"])) {
      $data = $paymentStatus["data"];
      return [
        "success" => true,
        "status" => $data["status"] == "PAID" ? Constants::$paymentPaid : Constants::$paymentUnpaid
      ];
    }
    return ["success" => false];
  }

  /**
   * Load payment method selector for payment gateway
   */
  public function loadPaymentSelector(): array
  {
    $this->items = $this->payload["items"];
    $order = $this->calculate();
    $this->payload["amount"] = $order["amount"];
    $this->payload["paymentChannel"] = $this->payment->loadSupportedPayment();

    return $this->payload;
  }

  /**
   * Map string payment method and type from frontend with
   * current gateway method and type
   * 
   * @param string $method Payment method
   * @param string $type Payment type
   * @return mixed Payment method and type
   */
  public function mapPaymentMethod(string $method, string $type): array
  {
    $closeTransactions = [
      Constants::$bank,
      Constants::$merchant,
      Constants::$ewallet,
      Constants::$qris,
    ];
    $types = [
      PaymentSelector::vaPermata()["code"] => "PERMATAVA",
      PaymentSelector::vaBni()["code"] => "BNIVA",
      PaymentSelector::vaBri()["code"] => "BRIVA",
      PaymentSelector::vaMandiri()["code"] => "MANDIRIVA",
      PaymentSelector::vaBca()["code"] => "BCAVA",
      PaymentSelector::vaMuamalat()["code"] => "MUAMALATVA",
      PaymentSelector::vaCimb()["code"] => "CIMBVA",
      PaymentSelector::vaBsi()["code"] => "BSIVA",
      PaymentSelector::vaOcbc()["code"] => "OCBCVA",
      PaymentSelector::vaDanamon()["code"] => "DANAMONVA",
      PaymentSelector::merchantAlfamart()["code"] => "ALFAMART",
      PaymentSelector::merchantIndomaret()["code"] => "INDOMARET",
      PaymentSelector::merchantAlfamidi()["code"] => "ALFAMIDI",
      PaymentSelector::ewalletOvo()["code"] => "OVO",
      PaymentSelector::ewalletDana()["code"] => "DANA",
      PaymentSelector::ewalletShoppePay()["code"] => "SHOPPEPAY",
      PaymentSelector::qris()["code"] => "QRIS",
    ];
    $payment = [
      "method" => "",
      "type" => ""
    ];
    $payment["method"] = (in_array($method, $closeTransactions)) ? self::CLOSE_TRANSACTION : self::OPEN_TRANSACTION;
    $payment["type"] = $types[$type];
    $payment["selector"] = [
      "method" => $method,
      "type" => $type,
    ];
    return $payment;
  }

  /**
   * Getting tax of payment method
   * 
   * @param string $method Payment method
   * @param string $type Payment type
   * @return mixed
   */
  public function getPaymentTax(string $method, string $type): array
  {
    return $this->payment->getTax($method, $type);
  }
}
