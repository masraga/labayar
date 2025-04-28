<?php

namespace Database\Seeders;

// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class LabayarSeeder extends Seeder
{
  /**
   * Seed the application's database.
   */
  public function run(): void
  {
    $store = DB::table("labayar_stores")->get()->first();
    if ($store) {
      DB::table("labayar_stores")->where("store_id", $store->store_id)->update([
        "name" => config("labayar.store_name"),
        "owner_name" => config("labayar.store_owner"),
        "address" => config("labayar.store_address"),
        "logo" => config("labayar.store_logo"),
      ]);
    } else {
      DB::table("labayar_stores")->insert([
        "store_id" => "store-" . time(),
        "name" => config("labayar.store_name"),
        "owner_name" => config("labayar.store_owner"),
        "address" => config("labayar.store_address"),
        "logo" => config("labayar.store_logo"),
      ]);
    }
  }
}
