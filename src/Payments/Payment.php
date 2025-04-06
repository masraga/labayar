<?php

namespace Koderpedia\Labayar\Payments;

use Error;
use Koderpedia\Labayar\Payments\Providers\IProvider;
use Koderpedia\Labayar\Payments\Providers\Labayar\Labayar;

class Payment
{

  /**
   * Payment provider
   */
  private IProvider $provider;

  public function __construct(string $provider)
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
    return $this->provider
      ->setPaymentMethod("cash", "cash")
      ->setOrderId($request["orderId"])
      ->setCustomer($request["customer"])
      ->setItems($request["items"])
      ->create();
  }
}
