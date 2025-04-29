<?php

namespace Koderpedia\Labayar\Services\Payments\Providers\Labayar\PaymentMethod;

use Error;
use Koderpedia\Labayar\Libraries\PaymentSelector;
use Koderpedia\Labayar\Services\Payments\Providers\IMethod;
use Koderpedia\Labayar\Services\Payments\Traits\PaymentCalculator;
use Koderpedia\Labayar\Utils\Constants;

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
  public function createTransaction(array $payload): array
  {
    return [];
  }

  /**
   * Load supported payment type of payment gateway
   * 
   * @return mixed
   */
  public function loadSupportedPayment(): array
  {
    return [
      "method" => Constants::$cash,
      "types" => [
        PaymentSelector::cash()
      ],
    ];
  }

  /**
   * Get tax of payment method
   * 
   * @param string $method Payment method
   * @param string $type Payment type
   * @return mixed
   */
  public function getTax(string $method, string $type): array
  {
    $supportPayment = $this->loadSupportedPayment();
    $selectedMethod = [];
    $taxes = [];
    foreach ($supportPayment as $payment) {
      if ($payment["method"] == $method) {
        $selectedMethod = $payment;
        break;
      }
    }
    foreach ($selectedMethod["types"] as $paymentType) {
      if ($paymentType == $type) {
        $taxes = $paymentType;
      }
    }
    return [
      "taxFix" => $taxes["taxFix"],
      "taxPercent" => $taxes["taxPercent"],
    ];
  }

  /**
   * Get payment status from payment gateway
   * 
   * @param string $paymentId Payment id from database
   * @return mixed
   */
  public function getPaymentStatus(string $paymentId): array
  {
    return [];
  }
}
