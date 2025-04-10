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
   * @return mixed
   */
  public function createTransaction(): array;
}
