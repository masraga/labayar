<?php

namespace Koderpedia\Labayar\Services\Payments\Traits;

trait PaymentCalculator
{
  /**
   * Product item to calculate
   */
  private array $items;

  /**
   * Set purchase item to calculation logic
   * 
   * @param mixed $items Purchase item
   */
  public function setItems(array $items)
  {
    $this->items = $items;
    return $this;
  }

  /**
   * Calculate subtotal payment
   */
  public function calculate(): array
  {
    $total = 0;
    foreach ($this->items as $item) {
      $total += intval($item["quantity"]) * intval($item["price"]);
    }
    return [
      "amount" => $total,
      "items" => $this->items
    ];
  }
}
