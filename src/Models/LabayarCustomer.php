<?php

namespace Koderpedia\Labayar\Models;

use Illuminate\Database\Eloquent\Model;

class LabayarCustomer extends Model
{
  protected $table = "labayar_customers";

  /**
   * The attributes that are mass assignable.
   *
   * @var array<int, string>
   */
  protected $fillable = [
    'customer_id',
    'store_id',
    'name',
    'email',
    'phone'
  ];
}
