<?php

namespace Koderpedia\Labayar\Services\Payments\Providers;

/**
 * Interface for all payment gateway
 */
interface IMethod
{
  /**
   * Set payment type of payment method 
   * example:
   * ```php
   * $method = new BankTransfer()
   * $method->use("BRIVA")
   * 
   * @param string $type Type of payment method
   */
  public function use(string $type);

  /**
   * Get payment type of payment method
   * example:
   * briva, bniva, gopay
   */
  public function getType(): string;

  /**
   * Get payment method label
   * example:
   * cash, bankTransfer, creditCard
   */
  public function getLabel(): string;

  /**
   * Calculate total purchase order
   * 
   * @param mixed $items Purchase item
   * @return mixed
   */
  public function calculateOrder(array $items): array;

  /**
   * use this if payment have different logic to create transaction
   * 
   * @param mixed $payload Transaciton payload
   * @return mixed
   */
  public function createTransaction(array $payload): array;

  /**
   * Load supported payment type of payment gateway
   * 
   * @return mixed
   */
  public function loadSupportedPayment(): array;

  /**
   * Get tax of payment method
   * 
   * @param string $method Payment method
   * @param string $type Payment type
   * @return mixed
   */
  public function getTax(string $method, string $type): array;

  /**
   * Get payment status from payment gateway
   * 
   * @param string $paymentId Payment id from database
   * @return mixed
   */
  public function getPaymentStatus(string $paymentId): array;
}
