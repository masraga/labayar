<?php

namespace Koderpedia\Labayar\Libraries;

class PaymentSelector
{
  /**
   * Load cash object
   * 
   * @param float $taxFix Tax fix for payment gateway
   * @param float $taxPercent Percentage tax for payment gateway
   * @return mixed
   */
  public static function cash(float $taxFix = 0, float $taxPercent = 0): array
  {
    return [
      "taxFix" => $taxFix,
      "taxPercent" => $taxPercent,
      "name" => "Cash",
      "code" => "cash",
      "image" => "/labayar-assets/images/cash-payment.png",
      "height" => "120px",
      "width" => "80px",
    ];
  }

  /**
   * Load virtual account bca object
   * 
   * @param float $taxFix Tax fix for payment gateway
   * @param float $taxPercent Percentage tax for payment gateway
   * @return mixed
   */
  public static function vaBca(float $taxFix = 0, float $taxPercent = 0): array
  {
    return [
      "taxFix" => $taxFix,
      "taxPercent" => $taxPercent,
      "name" => "Virtual Account BCA",
      "code" => "virtualAccountBca",
      "image" => "/labayar-assets/images/bca.png",
      "height" => "120px",
      "width" => "80px",
    ];
  }

  /**
   * Load virtual account bni object
   * 
   * @param float $taxFix Tax fix for payment gateway
   * @param float $taxPercent Percentage tax for payment gateway
   * @return mixed
   */
  public static function vaBni(float $taxFix = 0, float $taxPercent = 0): array
  {
    return [
      "taxFix" => $taxFix,
      "taxPercent" => $taxPercent,
      "name" => "Virtual Account BNI",
      "code" => "virtualAccountBni",
      "image" => "/labayar-assets/images/bni.png",
      "height" => "120px",
      "width" => "80px",
    ];
  }

  /**
   * Load virtual account bri object
   * 
   * @param float $taxFix Tax fix for payment gateway
   * @param float $taxPercent Percentage tax for payment gateway
   * @return mixed
   */
  public static function vaBri(float $taxFix = 0, float $taxPercent = 0): array
  {
    return [
      "taxFix" => $taxFix,
      "taxPercent" => $taxPercent,
      "name" => "Virtual Account BRI",
      "code" => "virtualAccountBri",
      "image" => "/labayar-assets/images/bri.png",
      "height" => "50px",
      "width" => "50px",
    ];
  }

  /**
   * Load virtual account bsi object
   * 
   * @param float $taxFix Tax fix for payment gateway
   * @param float $taxPercent Percentage tax for payment gateway
   * @return mixed
   */
  public static function vaBsi(float $taxFix = 0, float $taxPercent = 0): array
  {
    return [
      "taxFix" => $taxFix,
      "taxPercent" => $taxPercent,
      "name" => "Virtual Account BSI",
      "code" => "virtualAccountBsi",
      "image" => "/labayar-assets/images/bsi.png",
      "height" => "120px",
      "width" => "80px",
    ];
  }

  /**
   * Load virtual account cimb object
   * 
   * @param float $taxFix Tax fix for payment gateway
   * @param float $taxPercent Percentage tax for payment gateway
   * @return mixed
   */
  public static function vaCimb(float $taxFix = 0, float $taxPercent = 0): array
  {
    return [
      "taxFix" => $taxFix,
      "taxPercent" => $taxPercent,
      "name" => "Virtual Account CIMB",
      "code" => "virtualAccountCimb",
      "image" => "/labayar-assets/images/cimb.png",
      "height" => "120px",
      "width" => "80px",
    ];
  }

  /**
   * Load virtual account danamon object
   * 
   * @param float $taxFix Tax fix for payment gateway
   * @param float $taxPercent Percentage tax for payment gateway
   * @return mixed
   */
  public static function vaDanamon(float $taxFix = 0, float $taxPercent = 0): array
  {
    return [
      "taxFix" => $taxFix,
      "taxPercent" => $taxPercent,
      "name" => "Virtual Account Danamon",
      "code" => "virtualAccountDanamon",
      "image" => "/labayar-assets/images/danamon.png",
      "height" => "120px",
      "width" => "80px",
    ];
  }

  /**
   * Load virtual account mandiri object
   * 
   * @param float $taxFix Tax fix for payment gateway
   * @param float $taxPercent Percentage tax for payment gateway
   * @return mixed
   */
  public static function vaMandiri(float $taxFix = 0, float $taxPercent = 0): array
  {
    return [
      "taxFix" => $taxFix,
      "taxPercent" => $taxPercent,
      "name" => "Virtual Account Mandiri",
      "code" => "virtualAccountMandiri",
      "image" => "/labayar-assets/images/mandiri.png",
      "height" => "120px",
      "width" => "80px",
    ];
  }

  /**
   * Load virtual account muamalat object
   * 
   * @param float $taxFix Tax fix for payment gateway
   * @param float $taxPercent Percentage tax for payment gateway
   * @return mixed
   */
  public static function vaMuamalat(float $taxFix = 0, float $taxPercent = 0): array
  {
    return [
      "taxFix" => $taxFix,
      "taxPercent" => $taxPercent,
      "name" => "Virtual Account Muamalat",
      "code" => "virtualAccountMuamalat",
      "image" => "/labayar-assets/images/muamalat.png",
      "height" => "120px",
      "width" => "80px",
    ];
  }

  /**
   * Load virtual account ocbc object
   * 
   * @param float $taxFix Tax fix for payment gateway
   * @param float $taxPercent Percentage tax for payment gateway
   * @return mixed
   */
  public static function vaOcbc(float $taxFix = 0, float $taxPercent = 0): array
  {
    return [
      "taxFix" => $taxFix,
      "taxPercent" => $taxPercent,
      "name" => "Virtual Account OCBC",
      "code" => "virtualAccountOcbc",
      "image" => "/labayar-assets/images/ocbc.png",
      "height" => "120px",
      "width" => "80px",
    ];
  }

  /**
   * Load virtual account permata object
   * 
   * @param float $taxFix Tax fix for payment gateway
   * @param float $taxPercent Percentage tax for payment gateway
   * @return mixed
   */
  public static function vaPermata(float $taxFix = 0, float $taxPercent = 0): array
  {
    return [
      "taxFix" => $taxFix,
      "taxPercent" => $taxPercent,
      "name" => "Virtual Account Permata",
      "code" => "virtualAccountPermata",
      "image" => "/labayar-assets/images/permata.png",
      "height" => "120px",
      "width" => "80px",
    ];
  }

  /**
   * Load merchant alfamart object
   * 
   * @param float $taxFix Tax fix for payment gateway
   * @param float $taxPercent Percentage tax for payment gateway
   * @return mixed
   */
  public static function merchantAlfamart(float $taxFix = 0, float $taxPercent = 0): array
  {
    return [
      "taxFix" => $taxFix,
      "taxPercent" => $taxPercent,
      "name" => "Alfamart",
      "code" => "alfamart",
      "image" => "/labayar-assets/images/alfamart.png",
      "height" => "120px",
      "width" => "80px",
    ];
  }

  /**
   * Load merchant alfamidi object
   * 
   * @param float $taxFix Tax fix for payment gateway
   * @param float $taxPercent Percentage tax for payment gateway
   * @return mixed
   */
  public static function merchantAlfamidi(float $taxFix = 0, float $taxPercent = 0): array
  {
    return [
      "taxFix" => $taxFix,
      "taxPercent" => $taxPercent,
      "name" => "Alfamidi",
      "code" => "alfamidi",
      "image" => "/labayar-assets/images/alfamidi.png",
      "height" => "120px",
      "width" => "80px",
    ];
  }

  /**
   * Load merchant indomaret object
   * 
   * @param float $taxFix Tax fix for payment gateway
   * @param float $taxPercent Percentage tax for payment gateway
   * @return mixed
   */
  public static function merchantIndomaret(float $taxFix = 0, float $taxPercent = 0): array
  {
    return [
      "taxFix" => $taxFix,
      "taxPercent" => $taxPercent,
      "name" => "Indomaret",
      "code" => "indomaret",
      "image" => "/labayar-assets/images/indomaret.png",
      "height" => "120px",
      "width" => "80px",
    ];
  }

  /**
   * Load ewallet dana object
   * 
   * @param float $taxFix Tax fix for payment gateway
   * @param float $taxPercent Percentage tax for payment gateway
   * @return mixed
   */
  public static function ewalletDana(float $taxFix = 0, float $taxPercent = 0): array
  {
    return [
      "taxFix" => $taxFix,
      "taxPercent" => $taxPercent,
      "name" => "DANA",
      "code" => "dana",
      "image" => "/labayar-assets/images/dana.png",
      "height" => "120px",
      "width" => "80px",
    ];
  }

  /**
   * Load ewallet ovo object
   * 
   * @param float $taxFix Tax fix for payment gateway
   * @param float $taxPercent Percentage tax for payment gateway
   * @return mixed
   */
  public static function ewalletOvo(float $taxFix = 0, float $taxPercent = 0): array
  {
    return [
      "taxFix" => $taxFix,
      "taxPercent" => $taxPercent,
      "name" => "OVO",
      "code" => "ovo",
      "image" => "/labayar-assets/images/ovo.png",
      "height" => "40px",
      "width" => "35px",
    ];
  }

  /**
   * Load ewallet shoppepay object
   * 
   * @param float $taxFix Tax fix for payment gateway
   * @param float $taxPercent Percentage tax for payment gateway
   * @return mixed
   */
  public static function ewalletShoppePay(float $taxFix = 0, float $taxPercent = 0): array
  {
    return [
      "taxFix" => $taxFix,
      "taxPercent" => $taxPercent,
      "name" => "Shoppe Pay",
      "code" => "shoppepay",
      "image" => "/labayar-assets/images/shoppepay.png",
      "height" => "120px",
      "width" => "80px",
    ];
  }

  /**
   * Load qris object
   * 
   * @param float $taxFix Tax fix for payment gateway
   * @param float $taxPercent Percentage tax for payment gateway
   * @return mixed
   */
  public static function qris(float $taxFix = 0, float $taxPercent = 0): array
  {
    return [
      "taxFix" => $taxFix,
      "taxPercent" => $taxPercent,
      "name" => "QRIS Payment",
      "code" => "qris1",
      "image" => "/labayar-assets/images/qris.png",
      "height" => "120px",
      "width" => "80px",
    ];
  }
}
