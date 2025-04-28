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
        $table->integer("gross_total")->default(0);
      });
    }
    if (Schema::hasTable("labayar_invoice_payments")) {
      Schema::table('labayar_invoice_payments', function (Blueprint $table) {
        $table->boolean("is_manual_pay")->default(true);
      });
    }
    if (Schema::hasTable("labayar_stores")) {
      Schema::table('labayar_stores', function (Blueprint $table) {
        $table->string("owner_name")->default(Constants::$appName);
        $table->string("address")->nullable();
        $table->string("logo")->nullable();
        $table->string("phone")->nullable();
        $table->string("email")->nullable();
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
    if (Schema::hasTable("labayar_stores")) {
      Schema::table('labayar_stores', function (Blueprint $table) {
        $table->dropColumn("owner_name");
        $table->dropColumn("address");
        $table->dropColumn("logo");
        $table->dropColumn("phone");
        $table->dropColumn("email");
      });
    }
  }
};
