<?php

namespace Koderpedia\Labayar\Services\Payments\Providers\Midtrans\PaymentMethod;

use Error;
use Illuminate\Support\Facades\Http;
use Koderpedia\Labayar\Libraries\PaymentSelector;
use Koderpedia\Labayar\Services\Payments\Providers\IMethod;
use Koderpedia\Labayar\Services\Payments\Traits\PaymentCalculator;
use Koderpedia\Labayar\Utils\Constants;

class Ewallet implements IMethod
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

  /**
   * Valid type of payment method
   */
  private array $validType;

  /**
   * Gateway baseUrl
   */
  private string $baseUrl;

  /**
   * Http client authorization
   */
  private $authorization;

  public function __construct(array $ops = [])
  {
    $this->authorization = $ops["authorization"];
    $this->baseUrl = $ops["baseUrl"];
    $this->label = "close_transaction";
    $this->validType = [
      "gopay",
      "qris"
    ];
  }

  /**
   * Load supported payment type of payment gateway
   * 
   * @return mixed
   */
  public function loadSupportedPayment(): array
  {
    return [
      [
        "method" => Constants::$ewallet,
        "types" => [
          PaymentSelector::ewalletGopay(0, 2)
        ]
      ],
      [
        "method" => Constants::$qris,
        "types" => [
          PaymentSelector::qris(0, 0.7)
        ]
      ]
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
    if (!in_array($type, $this->validType)) {
      throw new Error("$type not supported for labayar $this->label method");
    }
    $this->type = $type;
    return $this;
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
   * @param mixed $payload Transaction payload
   * @return mixed
   */
  public function createTransaction(array $payload): array
  {
    $request = [
      "payment_type" => $this->type,
      "transaction_details" => [
        "gross_amount" => $payload["amount"],
        "order_id" => $payload["paymentId"],
      ],
    ];

    return $request;
  }

  /**
   * Get payment status from payment gateway
   * 
   * @param string $reference Payment reference from gateway
   * @return mixed
   */
  public function getPaymentStatus(string $reference): array
  {
    $endpoint = $this->baseUrl . "/transaction/detail";
    $payload = ["reference" => $reference];
    $paymentStatus = Http::withHeaders($this->authorization)->get($endpoint, $payload);
    return $paymentStatus->json();
  }
}
