<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
  /**
   * Run the migrations.
   */
  public function up(): void
  {
    if (!Schema::hasTable("labayar_stores")) {
      Schema::create('labayar_stores', function (Blueprint $table) {
        $table->id();
        $table->string("store_id")->unique();
        $table->string("name");
        $table->softDeletes();
        $table->timestamps();
      });
    }

    if (!Schema::hasTable("labayar_customers")) {
      Schema::create('labayar_customers', function (Blueprint $table) {
        $table->id();
        $table->string("customer_id")->unique();
        $table->string("store_id");
        $table->foreign("store_id")->references("store_id")->on("labayar_stores");
        $table->string("name");
        $table->string("email");
        $table->string("phone");
        $table->softDeletes();
        $table->timestamps();
      });
    }

    if (!Schema::hasTable("labayar_invoices")) {
      Schema::create('labayar_invoices', function (Blueprint $table) {
        $table->id();
        $table->string("invoice_id")->unique();
        $table->string("customer_id");
        $table->foreign("customer_id")->references("customer_id")->on("labayar_customers");
        $table->string("store_id");
        $table->foreign("store_id")->references("store_id")->on("labayar_stores");
        $table->date("expired_at");
        $table->integer("order_amount")->default(0);
        $table->tinyInteger("payment_status")->default(0);
        $table->softDeletes();
        $table->timestamps();
      });
    }

    if (!Schema::hasTable("labayar_invoice_metadata")) {
      Schema::create('labayar_invoice_metadata', function (Blueprint $table) {
        $table->id();
        $table->string("invoice_id");
        $table->foreign("invoice_id")->references("invoice_id")->on("labayar_invoices");
        $table->string("key");
        $table->string("value");
        $table->softDeletes();
        $table->timestamps();
      });
    }

    if (!Schema::hasTable("labayar_invoice_payments")) {
      Schema::create('labayar_invoice_payments', function (Blueprint $table) {
        $table->id();
        $table->string("invoice_id");
        $table->foreign("invoice_id")->references("invoice_id")->on("labayar_invoices");
        $table->integer("amount");
        $table->string("gateway");
        $table->string("payment_method");
        $table->string("payment_type");
        $table->tinyInteger("payment_status")->default(0);
        $table->softDeletes();
        $table->timestamps();
      });
    }

    if (!Schema::hasTable("labayar_invoice_items")) {
      Schema::create('labayar_invoice_items', function (Blueprint $table) {
        $table->id();
        $table->string("invoice_id");
        $table->foreign("invoice_id")->references("invoice_id")->on("labayar_invoices");
        $table->string("name");
        $table->string("price");
        $table->string("quantity");
        $table->string("product_id")->nullable();
        $table->softDeletes();
        $table->timestamps();
      });
    }

    if (!Schema::hasTable("labayar_products")) {
      Schema::create('labayar_products', function (Blueprint $table) {
        $table->id();
        $table->string("product_id")->unique();
        $table->string("store_id");
        $table->foreign("store_id")->references("store_id")->on("labayar_stores");
        $table->string("name");
        $table->string("price");
        $table->string("type")->default("common");
        $table->softDeletes();
        $table->timestamps();
      });
    }
  }

  /**
   * Reverse the migrations.
   */
  public function down(): void
  {
    Schema::dropIfExists('labayar_stores');
    Schema::dropIfExists('labayar_customers');
    Schema::dropIfExists('labayar_invoices');
    Schema::dropIfExists('labayar_invoice_metadata');
    Schema::dropIfExists('labayar_invoice_payments');
    Schema::dropIfExists('labayar_invoice_items');
    Schema::dropIfExists('labayar_products');
  }
};
