<?php

namespace Koderpedia\Labayar;

use Error;
use Koderpedia\Labayar\Services\Payments\Providers\IProvider;
use Koderpedia\Labayar\Services\Payments\Providers\Labayar\Labayar;
use Koderpedia\Labayar\Repositories\Payment as PaymentRepo;
use Koderpedia\Labayar\Repositories\Store;

class Payment
{

  /**
   * Payment provider
   */
  private IProvider $provider;

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
   */
  public function create(array $request)
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
    /**
     * set default storeId for lababar v1
     */
    $store = Store::createOrFind([]);
    $request["customer"]["storeId"] = $store["store_id"];
    $transaction = $this->provider
      ->setPaymentMethod("cash", "cash")
      ->setOrderId($request["orderId"])
      ->setCustomer($request["customer"])
      ->setItems($request["items"])
      ->create();
    $payment = PaymentRepo::createTransaction($transaction);
    return $payment;
  }
}
