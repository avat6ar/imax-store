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
    Schema::create('orders', function (Blueprint $table)
    {
      $table->id();
      $table->string('order_id');
      $table->string('currency');
      $table->string('payment_method');
      $table->decimal('total_amount', 8, 2);
      $table->string('payment_status')->default('pending');
      $table->longText('data')->nullable();
      $table->string('session_id')->nullable();
      $table->string('send_admin')->nullable();
      $table->longText('message')->nullable();
      $table->string('type')->nullable();
      $table->string('code')->nullable();
      $table->boolean('sending')->default(false);
      $table->foreignId('product_id')->constrained("products", "id")->cascadeOnDelete();
      $table->foreignId('user_id')->constrained("users", "id")->cascadeOnDelete();
      $table->timestamp('send_date')->nullable();
      $table->timestamps();
    });
  }

  /**
   * Reverse the migrations.
   */
  public function down(): void
  {
    Schema::dropIfExists('orders');
  }
};
