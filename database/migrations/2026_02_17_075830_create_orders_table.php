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
        Schema::create('orders', function (Blueprint $table) {
            $table->id();

            $table->foreignId('user_id')->constrained('users')->cascadeOnUpdate()->cascadeOnDelete();
            $table->string('order_code')->unique();
            $table->string('transaction_id')->nullable();
            $table->decimal('total_price', 15, 2);

            $table->double('discount_price')->nullable();
            $table->string('discount_code')->nullable();

            $table->double('gift_cart_price')->nullable();
            $table->string('gift_cart_code')->nullable();

            $table->string('status')->default(\App\Enums\OrderStatus::WaitPayment->value);
            $table->timestamp('paid_at')->nullable();
            $table->softDeletes();
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
