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
        Schema::create('financial_ledgers', function (Blueprint $table) {
            $table->id();
            $table->foreignId('order_id')
                ->constrained('orders')
                ->cascadeOnUpdate()
                ->cascadeOnDelete();

            $table->string('type'); // 'customer_payment', 'seller_share', 'site_share', 'coupon_expense', 'gift_cart_expense', 'platform_subsidy'
            $table->string('entry_type'); // 'debit' (بدهکار) یا 'credit' (بستانکار)
            $table->decimal('amount', 18, 2);
            $table->string('description')->nullable();
            $table->timestamps();

            $table->index(['order_id', 'type']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('financial_ledgers');
    }
};
