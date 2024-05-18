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
    Schema::create('product_reviews', function (Blueprint $table) {
      $table->id();
      $table->integer('rate');
      $table->longText('review');
      $table->boolean('publish')->default(false);
      $table->foreignId('user_id')->constrained("users", "id")->cascadeOnDelete();
      $table->foreignId('product_id')->constrained("products", "id")->cascadeOnDelete();
      $table->timestamps();
    });
  }

  /**
   * Reverse the migrations.
   */
  public function down(): void
  {
    Schema::dropIfExists('product_reviews');
  }
};
