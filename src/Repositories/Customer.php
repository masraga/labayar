<?php

namespace Koderpedia\Labayar\Repositories;

use Error;
use Illuminate\Support\Facades\DB;
use Koderpedia\Labayar\Models\LabayarCustomer;
use Koderpedia\Labayar\Models\LabayarStore;

class Customer
{
  /**
   * Create patient if not exists
   * 
   * @param mixed $payload Customer info
   * @return mixed
   */
  public static function createOrFind(array $payload): array
  {
    $current = DB::table("labayar_customers");
    if (isset($payload["customerId"])) {
      $current->where("customer_id", $payload["customerId"]);
    }
    if (isset($payload["storeId"])) {
      $current->orWhere("store_id", $payload["storeId"]);
    }
    if (isset($payload["email"])) {
      $current->orWhere("email", $payload["email"]);
    }
    $current = $current->get()->first();
    if ($current) {
      return (array) $current;
    }

    $store = LabayarStore::where("store_id", $payload["storeId"])->first();
    if (!$store) {
      throw new Error("Store $store->store_id not registered in labayar");
    }

    $customerPayload = [
      "customer_id" => "cust-" . time(),
      "store_id" => $store->store_id,
      "name" => $payload["name"],
      "email" => $payload["email"],
      "phone" => $payload["phone"],
    ];
    $customer = LabayarCustomer::create($customerPayload);
    return (array) $customer->toArray();
  }
}
