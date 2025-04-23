<?php

namespace Koderpedia\Labayar\Services\Payments\Providers\Tripay\PaymentMethod;

use Error;
use Illuminate\Support\Facades\Http;
use Koderpedia\Labayar\Libraries\PaymentSelector;
use Koderpedia\Labayar\Services\Payments\Providers\IMethod;
use Koderpedia\Labayar\Services\Payments\Traits\PaymentCalculator;
use Koderpedia\Labayar\Utils\Constants;

class CloseTransaction implements IMethod
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
      "PERMATAVA",
      "BNIVA",
      "BRIVA",
      "MANDIRIVA",
      "BCAVA",
      "MUAMALATVA",
      "CIMBVA",
      "BSIVA",
      "OCBCVA",
      "DANAMONVA",
      "OTHERBANKVA",
      "ALFAMART",
      "INDOMARET",
      "ALFAMIDI",
      "OVO",
      "QRIS",
      "QRISC",
      "QRIS2",
      "DANA",
      "SHOPEEPAY"
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
        "method" => Constants::$bank,
        "types" => [
          PaymentSelector::vaPermata(4250),
          PaymentSelector::vaBni(4250),
          PaymentSelector::vaBri(4250),
          PaymentSelector::vaMandiri(4250),
          PaymentSelector::vaBca(5500),
          PaymentSelector::vaMuamalat(4250),
          PaymentSelector::vaCimb(4250),
          PaymentSelector::vaBsi(4250),
          PaymentSelector::vaOcbc(4250),
          PaymentSelector::vaDanamon(4250)
        ]
      ],
      [
        "method" => Constants::$merchant,
        "types" => [
          PaymentSelector::merchantAlfamart(3500),
          PaymentSelector::merchantIndomaret(3500),
          PaymentSelector::merchantAlfamidi(3500),
        ]
      ],
      [
        "method" => Constants::$ewallet,
        "types" => [
          PaymentSelector::ewalletOvo(0, 3),
          PaymentSelector::ewalletDana(0, 3),
          PaymentSelector::ewalletShoppePay(0, 3),
        ]
      ],
      [
        "method" => Constants::$qris,
        "types" => [
          PaymentSelector::qris(750, 0.7)
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
    $signature = hash_hmac(
      'sha256',
      config("tripay.merchant_code") .
        $payload["paymentId"] .
        $payload["amount"],
      config("tripay.private_key")
    );
    $request = [
      "method" => $this->type,
      "merchant_ref" => $payload["paymentId"],
      "amount" => $payload["amount"],
      "customer_name" => $payload["customer"]["name"],
      "customer_email" => $payload["customer"]["email"],
      "customer_phone" => $payload["customer"]["phone"],
      "order_items" => $payload["items"],
      "return_url" => config("tripay.return_url"),
      "expired_time" => $payload["expiredAtUnix"],
      "signature" => $signature,
      "callback_url" => config("app.url") . "/api/labayar/gateway/notification"
    ];
    $endpoint = $this->baseUrl . "/transaction/create";
    $post = Http::withHeaders($this->authorization)->post($endpoint, $request);
    return $post->json();
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
