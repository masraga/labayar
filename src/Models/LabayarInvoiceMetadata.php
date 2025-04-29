<?php

namespace Koderpedia\Labayar\Models;

use Illuminate\Database\Eloquent\Model;

class LabayarInvoiceMetadata extends Model
{
  protected $table = "labayar_invoice_metadata";

  /**
   * The attributes that are mass assignable.
   *
   * @var array<int, string>
   */
  protected $fillable = [
    'invoice_id',
    'key',
    'value'
  ];
}
