<?php

namespace Koderpedia\Labayar\Repositories;

use Illuminate\Support\Facades\DB;
use Koderpedia\Labayar\Models\LabayarStore;
use Koderpedia\Labayar\Utils\Constants;

class Store
{
  public static function createOrFind(array $payload): array
  {
    $current = DB::table("labayar_stores");
    if (array_key_exists("orderId", $payload)) {
      $current->where("store_id", $payload["storeId"]);
    }
    if (array_key_exists("name", $payload)) {
      $current->where("name", $payload["name"]);
    }
    $current = $current->get()->first();
    if ($current) {
      return (array) $current;
    }

    /**
     * Use this for static store name. Cause labayar is not supported for multistore
     */
    $payload["name"] = Constants::$appName;

    $storePayload = [
      "store_id" => "store-" . time(),
      "name" => $payload["name"]
    ];
    $store = LabayarStore::create($storePayload);
    return $store->toArray();
  }
}
