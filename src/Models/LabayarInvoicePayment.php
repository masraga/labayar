<?php

namespace Koderpedia\Labayar\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

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
    'order_id',
    'expired_at',
    'amount',
    'gateway',
    'payment_method',
    'payment_type',
    'store_id',
    'payment_status',
    'change',
    'paid_date',
    'nett_amount'
  ];

  public function invoice(): BelongsTo
  {
    return $this->belongsTo(LabayarInvoice::class, "invoice_id", "invoice_id");
  }
}
