<?php

namespace Koderpedia\Labayar\Models;

use Illuminate\Database\Eloquent\Model;

class LabayarStore extends Model
{
  protected $table = "labayar_stores";

  /**
   * The attributes that are mass assignable.
   *
   * @var array<int, string>
   */
  protected $fillable = [
    'store_id',
    'name',
  ];
}
