<?php

namespace Koderpedia\Labayar;

use Illuminate\Support\ServiceProvider;

class LabayarServiceProvider extends ServiceProvider
{
  public function boot()
  {
    $this->loadMigrationsFrom(__DIR__ . "/../database/migrations");
    $this->loadRoutesFrom(__DIR__ . "/Routes/api.php");
    if ($this->app->runningInConsole()) {
      $this->publishes([__DIR__ . "/../database/migrations" => database_path("migrations")], "migrations");
      $this->publishes([__DIR__ . "/Resources/Public" => public_path("./")], "public");
      $this->publishes([__DIR__ . "/Config" => config_path("./")]);;
    }
    // $this->loadViewsFrom(__DIR__ . "/../resources/views", "labayar");
    $this->loadViewsFrom(__DIR__ . "/Resources/Views", "labayar");
  }
  public function register() {}
}
