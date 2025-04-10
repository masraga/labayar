<?php

namespace Koderpedia\Labayar\Models;

use Illuminate\Database\Eloquent\Model;

class LabayarInvoicePayment extends Model
{
  protected $table = "labayar_invoice_payments";

  /**
   * The attributes that are mass assignable.
   *
   * @var array<int, string>
   */
  protected $fillable = [
    'invoice_id',
    'amount',
    'gateway',
    'payment_method',
    'payment_type',
    'payment_status',
  ];
}
