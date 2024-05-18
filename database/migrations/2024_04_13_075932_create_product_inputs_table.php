<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
  /**
   * Run the migrations.
   */
  public function up(): void
  {
    Schema::create('product_inputs', function (Blueprint $table) {
      $table->id();
      $table->string('title_en', 255);
      $table->string('title_ar', 255);
      $table->string('title_fr', 255);
      $table->string('type', 45);
      $table->longText('data')->nullable();
      $table->foreignId('product_id')->constrained("products", "id")->cascadeOnDelete();
      $table->timestamps();
    });
  }

  /**
   * Reverse the migrations.
   */
  public function down(): void
  {
    Schema::dropIfExists('product_inputs');
  }
};
