<?php

namespace Koderpedia\Labayar\Services\Payments\Providers;

interface IProvider
{
  /**
   * Get payment gateway label name
   * 
   * @return string
   */
  public function getGateway(): string;

  /**
   * Set order id for every payment method
   * 
   * @param string $id Order Id
   * @return $this
   */
  public function setOrderId(string $id);

  /**
   * Set invoice expired time
   * 
   * @param mixed $time Expired time unit = minutes/hours/days, duration = int
   */
  public function setExpired(array $time);

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
}
