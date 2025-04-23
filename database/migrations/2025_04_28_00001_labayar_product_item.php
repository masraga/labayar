<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Koderpedia\Labayar\Utils\Constants;

return new class extends Migration
{
  /**
   * Run the migrations.
   */
  public function up(): void
  {
    if (Schema::hasTable("labayar_invoice_items")) {
      Schema::table('labayar_invoice_items', function (Blueprint $table) {
        $table->string("type")->default(Constants::$sellItem);
      });
    }
    if (Schema::hasTable("labayar_invoice_payments")) {
      Schema::table('labayar_invoice_payments', function (Blueprint $table) {
        $table->boolean("is_manual_pay")->default(true);
      });
    }
  }

  /**
   * Reverse the migrations.
   */
  public function down(): void
  {
    if (Schema::hasTable("labayar_invoice_items")) {
      Schema::table('labayar_invoice_items', function (Blueprint $table) {
        $table->dropColumn('type');
      });
    }
    if (Schema::hasTable("labayar_invoice_payments")) {
      Schema::table('labayar_invoice_payments', function (Blueprint $table) {
        $table->dropColumn('is_manual_pay');
      });
    }
  }
};
