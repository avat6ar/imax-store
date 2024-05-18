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
    Schema::create('product_questions', function (Blueprint $table) {
      $table->id();
      $table->string('question_en');
      $table->string('question_ar');
      $table->string('question_fr');
      $table->string('answer_ar');
      $table->string('answer_fr');
      $table->string('answer_en');
      $table->foreignId('product_id')->constrained("products", "id")->cascadeOnDelete();
      $table->timestamps();
    });
  }

  /**
   * Reverse the migrations.
   */
  public function down(): void
  {
    Schema::dropIfExists('product_questions');
  }
};
