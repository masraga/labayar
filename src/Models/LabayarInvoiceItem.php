<?php

namespace Koderpedia\Labayar\Models;

use Illuminate\Database\Eloquent\Model;

class LabayarInvoiceItem extends Model
{
  protected $table = "labayar_invoice_items";

  /**
   * The attributes that are mass assignable.
   *
   * @var array<int, string>
   */
  protected $fillable = [
    'invoice_id',
    'name',
    'price',
    'quantity',
    'product_id',
  ];
}
