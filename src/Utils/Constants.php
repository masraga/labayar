<?php

namespace Koderpedia\Labayar\Utils;

class Constants
{
  /**
   * This value is the name of your application
   */
  public static $appName = "Labayar";
  /**
   * Use to payment status unpaid
   */
  public static $paymentUnpaid = 0;
  /**
   * Use to payment status paid
   */
  public static $paymentPaid = 1;
  /**
   * Use to payment status expired
   */
  public static $paymentExpired = 2;
  /**
   * Payment gateway type ewallet
   */
  public static $ewallet = "E-Wallet";
  /**
   * Payment gateway type credit card
   */
  public static $cash = "Cash";
  /**
   * Payment gateway type credit card
   */
  public static $cc = "Credit Card";
  /**
   * Payment gateway type bank transfer
   */
  public static $bank = "Bank Transfer";
  /**
   * Payment gateway type merchant
   */
  public static $merchant = "Merchant";
  /**
   * Payment gateway type qris
   */
  public static $qris = "QRIS";
  /**
   * Type for merchant sell product
   */
  public static $sellItem = "sellItem";
  /**
   * Type for admin fee of invoice
   */
  public static $adminFee = "adminFee";
  /**
   * Key for get payment method
   */
  public static $paymentMethod = "paymentMethod";
  /**
   * Key for get payment type
   */
  public static $paymentType = "paymentType";
  /**
   * Boolean to check if payment is payment gateway
   */
  public static $isPaymentGateway = "isPaymentGateway";
  /**
   * Sub total of purchase item amount fee
   */
  public static $subTotal = "subTotalFee";
  /**
   * Payment gateway image
   */
  public static $paymentGatewayImage = "paymentGatewayImage";
  /**
   * Payment gateway method type name
   */
  public static $paymentTypeName = "paymentTypeName";
  /**
   * Merchant name of payment gateway.
   * Is use to be name if you want to pay bills in counter
   */
  public static $gatewayMerchantName = "merchantName";
  /**
   * Merchant code of payment gateway. 
   * is use to be pay code if you do payment in counter
   */
  public static $gatewayMerchantCode = "merchantCode";
}
