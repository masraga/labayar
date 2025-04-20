<?php

namespace Koderpedia\Labayar\Services\Payments\Providers;

/**
 * use it for provider with manual payment from system not payment gateway
 */
interface IManualPay
{
  /**
   * Set payment fee for invoice
   * 
   * @param int $amount Amount fee for the transaction
   */
  public function setPayAmount(int $amount);
}
