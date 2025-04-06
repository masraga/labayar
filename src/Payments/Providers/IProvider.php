<?php

namespace Koderpedia\Labayar\Payments\Providers;

use Koderpedia\Labayar\Payments\Providers\IMethod;

interface IProvider
{
  /**
   * Set order id for every payment method
   * 
   * @param string $id Order Id
   * @return $this
   */
  public function setOrderId(string $id);

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
