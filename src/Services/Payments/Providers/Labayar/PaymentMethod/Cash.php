<?php

namespace Koderpedia\Labayar\Services\Payments\Providers\Labayar\PaymentMethod;

use Error;
use Koderpedia\Labayar\Services\Payments\Providers\IMethod;
use Koderpedia\Labayar\Services\Payments\Traits\PaymentCalculator;

class Cash implements IMethod
{
  use PaymentCalculator;

  /**
   * Paymnet method label
   */
  private string $label;

  /**
   * Payment type of method
   */
  private string $type;

  public function __construct()
  {
    $this->label = "cash";
  }

  /**
   * Set payment type of payment method 
   * example:
   * ```php
   * $method = new BankTransfer()
   * $method->use("BRIVA")
   * 
   * @param string $type Type of payment method
   */
  public function use(string $type)
  {
    $validType = ["cash"];
    if (!in_array($type, $validType)) {
      throw new Error("$type not supported for labayar $this->label method");
    }
    $this->type = $type;
  }

  /**
   * Get payment type of payment method
   * example:
   * briva, bniva, gopay
   */
  public function getType(): string
  {
    return $this->type;
  }

  /**
   * Get payment method label
   * example:
   * cash, bankTransfer, creditCard
   */
  public function getLabel(): string
  {
    return $this->label;
  }

  /**
   * Calculate total purchase order
   * 
   * @param mixed $items Purchase item
   * @return mixed
   */
  public function calculateOrder(array $items): array
  {
    return $this->setItems($items)->calculate();
  }

  /**
   * use this if payment have different logic to create transaction
   * 
   * @return mixed
   */
  public function createTransaction(): array
  {
    return [];
  }
}
