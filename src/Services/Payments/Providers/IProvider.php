<?php

namespace Koderpedia\Labayar\Services\Payments\Providers;

interface IProvider
{
  /**
   * Get payment gateway label name
   * 
   * @return string
   */
  public static function getGateway(): string;

  /**
   * Set order id for every payment method
   * 
   * @param string $id Order Id
   * @return $this
   */
  public function setOrderId(string $id);

  /**
   * Transaction expiry time
   * 
   * @param int $duration Expiry time duration
   * @param string $unit Expiry unit seconds/minutes/hours/days
   */
  public function setExpired(int $duration, string $unit);

  /**
   * Set payment method for every transaction
   * 
   * @param string $method Payment method
   * @param string $type Payment type of payment method
   * @return $this
   */
  public function setPaymentMethod(string $method, string $type);

  /**
   * Set customer for every transaction
   * 
   * @param mixed $customer Customer info
   * @return $this
   */
  public function setCustomer(array $customer);

  /**
   * Set transaction item for every transaction
   * 
   * @param mixed $items order item
   * @return $this
   */
  public function setItems(array $items);

  /**
   * Create transaction for every payment
   * 
   * @return mixed
   */
  public function create(): array;

  /**
   * Pay order based on orderId
   * 
   * @param mixed $payload Payment payload 
   * @return mixed
   */
  public function pay(array $payload): array;
}
