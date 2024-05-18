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
    Schema::create('chargily_payments', function (Blueprint $table)
    {
      $table->id();
      $table->enum("status", ["pending", "paid", "failed"])->default("pending");
      $table->string("currency");
      $table->string("amount");
      $table->foreignId('user_id')->constrained("users", "id")->cascadeOnDelete();
      $table->timestamps();
    });
  }

  /**
   * Reverse the migrations.
   */
  public function down(): void
  {
    Schema::dropIfExists('chargily_payments');
  }
};
