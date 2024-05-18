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
    Schema::create('products', function (Blueprint $table) {
      $table->id();
      $table->string('title_en', 1000);
      $table->string('title_ar', 1000);
      $table->string('title_fr', 1000);
      $table->string('slug', 1000);
      $table->boolean('status')->default(false);
      $table->longText('description_en');
      $table->longText('description_fr');
      $table->longText('description_ar');
      $table->string('type', 70);
      $table->string('type_en', 70);
      $table->string('type_fr', 70);
      $table->string('type_ar', 70);
      $table->integer('seen')->default(0);
      $table->integer('sales_count')->default(0);
      $table->foreignId('user_id')->constrained("users", "id")->cascadeOnDelete();
      $table->foreignId('category_id')->constrained("product_categories", "id")->cascadeOnDelete();
      $table->timestamps();
    });
  }

  /**
   * Reverse the migrations.
   */
  public function down(): void
  {
    Schema::dropIfExists('products');
  }
};
