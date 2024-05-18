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
    Schema::create('product_seos', function (Blueprint $table) {
      $table->id();
      $table->string('title_en');
      $table->string('title_ar');
      $table->string('title_fr');
      $table->text('description_en');
      $table->text('description_ar');
      $table->text('description_fr');
      $table->string('keywords_en');
      $table->string('keywords_ar');
      $table->string('keywords_fr');
      $table->foreignId('product_id')->constrained("products", "id")->cascadeOnDelete();
      $table->timestamps();
    });
  }

  /**
   * Reverse the migrations.
   */
  public function down(): void
  {
    Schema::dropIfExists('product_seos');
  }
};
