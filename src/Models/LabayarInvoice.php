<?php

namespace Koderpedia\Labayar\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class LabayarInvoice extends Model
{
  protected $table = "labayar_invoices";

  /**
   * The attributes that are mass assignable.
   *
   * @var array<int, string>
   */
  protected $fillable = [
    'invoice_id',
    'customer_id',
    'store_id',
    'order_amount',
    'payment_status',
  ];

  public function payment(): HasMany
  {
    return $this->hasMany(LabayarInvoicePayment::class, "invoice_id", "invoice_id");
  }
}
