<?php

namespace Koderpedia\Labayar\Services\Payments\Providers;

/**
 * Use this interface if create new payment gateway
 */
interface IPaymentGateway
{
  /**
   * Load payment method selector for payment gateway
   */
  public function loadPaymentSelector(): array;

  /**
   * Map string payment method and type from frontend with
   * current gateway method and type
   * 
   * @param string $method Payment method
   * @param string $type Payment type
   * @return mixed Payment method and type
   */
  public function mapPaymentMethod(string $method, string $type): array;

  /**
   * use default payment method of payment provider, that will automatically initiate payment method
   * instance for every payment gateway
   */
  public function useDefaultPaymentMethod();

  /**
   * Getting tax of payment method
   * 
   * @param string $method Payment method
   * @param string $type Payment type
   * @return mixed
   */
  public function getPaymentTax(string $method, string $type): array;
}
