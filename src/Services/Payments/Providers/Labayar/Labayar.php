<?php

namespace Koderpedia\Labayar\Services\Payments\Providers\Labayar;

use Error;
use Illuminate\Support\Facades\Validator;
use Koderpedia\Labayar\Services\Payments\Providers\IManualPay;
use Koderpedia\Labayar\Services\Payments\Providers\IProvider;
use Koderpedia\Labayar\Services\Payments\Providers\IMethod;
use Koderpedia\Labayar\Services\Payments\Providers\Labayar\PaymentMethod\Cash;
use Koderpedia\Labayar\Utils\Time;

class Labayar implements IProvider, IManualPay
{
  /**
   * Payment method
   */
  private IMethod $payment;

  /**
   * Require payload to creating transaction
   */
  private array $payload;

  /**
   * Payment gateway label
   */
  private static string $gateway = "labayar";

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
    if ($method == "cash") {
      $this->payment = new Cash();
    } else {
      throw new Error("$method not supported for labayar");
    }
    $this->payment->use($type);
    $this->payload["paymentMethod"] = $this->payment->getLabel();
    $this->payload["paymentType"] = $this->payment->getType();
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
    return $payload;
  }

  /**
   * Set payment fee for invoice
   * 
   * @param int $amount Amount fee for the transaction
   */
  public function setPayAmount(int $amount)
  {
    if ($amount < 10000) {
      throw new Error("Min purchase order is Rp10000");
    }
    $this->payload["payAmount"] = $amount;
    return $this;
  }
}
