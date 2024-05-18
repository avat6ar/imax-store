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
    Schema::create('product_prices', function (Blueprint $table)
    {
      $table->id();
      $table->decimal('sar', 10, 2);
      $table->decimal('aed', 10, 2);
      $table->decimal('kwd', 10, 2);
      $table->decimal('usd', 10, 2);
      $table->decimal('eur', 10, 2);
      $table->decimal('dzd', 10, 2);
      $table->decimal('egp', 10, 2);
      $table->decimal('imx', 10, 2);
      $table->foreignId('product_id')->constrained("products", "id")->cascadeOnDelete();
      $table->timestamps();
    });
  }

  /**
   * Reverse the migrations.
   */
  public function down(): void
  {
    Schema::dropIfExists('product_prices');
  }
};
