<?php

namespace Koderpedia\Labayar\Payments\Providers\Labayar;

use Error;
use Illuminate\Support\Facades\Validator;
use Koderpedia\Labayar\Payments\Providers\IProvider;
use Koderpedia\Labayar\Payments\Providers\IMethod;
use Koderpedia\Labayar\Payments\Providers\Labayar\PaymentMethod\Cash;

class Labayar implements IProvider
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
   * Set order id for every payment method
   * 
   * @param string $id Order id
   * @return $this
   */
  public function setOrderId(string $id)
  {
    $this->payload["orderId"] = $id;
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
    ], [
      "name.required" => "Customer name is required",
      "email.required" => "Customer email is required",
      "phone.required" => "Customer phone is required",
    ]);
    if ($validator->fails()) {
      throw new Error($validator->errors());
    }
    $this->payload["customer"] = [
      "name" => $customer["name"],
      "email" => $customer["email"],
      "phone" => $customer["phone"],
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

    return $this->payload;
  }
}
