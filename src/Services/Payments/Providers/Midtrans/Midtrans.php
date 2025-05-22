<?php

namespace Koderpedia\Labayar\Services\Payments\Providers\Midtrans;

use Error;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Validator;
use \Illuminate\Support\Str;
use Koderpedia\Labayar\Libraries\PaymentSelector;
use Koderpedia\Labayar\Services\Payments\Providers\IMethod;
use Koderpedia\Labayar\Services\Payments\Providers\IPaymentGateway;
use Koderpedia\Labayar\Services\Payments\Providers\IProvider;
use Koderpedia\Labayar\Services\Payments\Providers\Midtrans\PaymentMethod\Ewallet;
use Koderpedia\Labayar\Services\Payments\Traits\PaymentCalculator;
use Koderpedia\Labayar\Utils\Constants;
use Koderpedia\Labayar\Utils\Time;

class Midtrans implements IProvider, IPaymentGateway
{
  use PaymentCalculator;

  /**
   * Gateway name of provider
   */
  private static string $gateway = "midtrans";

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
    if (config("midtrans.is_production")) {
      $this->baseUrl = "https://api.midtrans.com/v2";
    } else {
      $this->baseUrl = "https://api.sandbox.midtrans.com/v2";
    }
    $this->authorization = [
      "Content-Type" => "application/json",
      "Authorization" => "Basic " . Str::toBase64(config("midtrans.server_key") . ":")
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
    $this->payment = new Ewallet($ops);
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
    if ($method == Constants::$ewallet) {
      $this->payment = new Ewallet($ops);
    } else {
      throw new Error("$method not supported in labayar", 412);
    }
    $this->payload["paymentMethod"] = $method;
    $this->payload["paymentType"] = $type;
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
    $paymentPayload = $this->payment->use($this->payload["paymentType"])->createTransaction($this->payload);
    $paymentPayload["item_details"] = $this->payload["items"];
    $paymentPayload["customer_details"] = [
      "first_name" => $this->payload["customer"]["name"],
      "email" => $this->payload["customer"]["email"],
      "phone" => $this->payload["customer"]["phone"],
    ];
    $httpRequest = Http::withHeaders($this->authorization)->post($this->baseUrl . "/charge", $paymentPayload);
    $pgResult = array_merge($httpRequest->json(), $this->payload);
    $this->mapResult($pgResult);
    return $this->payload;
  }

  /**
   * Map payment gateway result metadata
   * 
   * @param mixed $result Payment gateway result
   * @return void
   */
  public function mapResult(array $result): void
  {
    if ($result["transaction_status"] == "pending") {
      $paymentName = $result["payment_type"];
      $paymentCode = $result["transaction_id"];
      $paymentUrl = "";
      $paymentUrlIsImage = false;
      if (in_array($paymentName, ["gopay", "qris"])) {
        $paymentUrlIsImage = true;
        foreach ($result["actions"] as $action) {
          if ($action["name"] == "generate-qr-code") {
            $paymentUrl = $action["url"];
            break;
          }
        }
      }
      $pgResult[Constants::$gatewayMerchantName] = $paymentName;
      $pgResult[Constants::$gatewayMerchantCode] = $paymentCode;
      $pgResult[Constants::$pgUrl] = $paymentUrl;
      $pgResult[Constants::$pgUrlIsImage] = $paymentUrlIsImage;
      $this->payload["paymentGatewayResult"] = $pgResult;
    }
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
    $types = [
      PaymentSelector::ewalletGopay()["code"] => "gopay",
      PaymentSelector::qris()["code"] => "qris",
    ];
    $payment = [
      "method" => $method,
      "type" => ""
    ];
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
