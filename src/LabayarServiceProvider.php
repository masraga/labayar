<?php

namespace Koderpedia\Labayar;

use Illuminate\Support\ServiceProvider;

class LabayarServiceProvider extends ServiceProvider
{
  public function boot()
  {
    if ($this->app->runningInConsole()) {
      $this->loadMigrationsFrom(__DIR__ . "/../database/migrations");
      $this->publishes([__DIR__ . "/../database/migrations" => database_path("migrations")], "migrations");
    }
  }
  public function register() {}
}
