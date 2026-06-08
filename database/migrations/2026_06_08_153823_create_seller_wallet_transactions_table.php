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
        Schema::create('seller_wallet_transactions', function (Blueprint $table) {

            $table->id();

            $table->foreignId('seller_id')
                ->constrained()
                ->cascadeOnUpdate()
                ->cascadeOnDelete();

            /*
            |--------------------------------------------------------------------------
            | مبلغ
            |--------------------------------------------------------------------------
            */

            $table->decimal('amount',15,2);

            /*
            |--------------------------------------------------------------------------
            | نوع تراکنش
            |--------------------------------------------------------------------------
            */

            $table->string('type');

            /*
            |--------------------------------------------------------------------------
            | شرح
            |--------------------------------------------------------------------------
            */

            $table->string('description')
                ->nullable();

            /*
            |--------------------------------------------------------------------------
            | ارتباط با سفارش
            |--------------------------------------------------------------------------
            */

            $table->foreignId('order_id')
                ->nullable()
                ->constrained()
                ->nullOnDelete();

            /*
            |--------------------------------------------------------------------------
            | موجودی بعد از تراکنش
            |--------------------------------------------------------------------------
            */

            $table->decimal('balance_after',15,2)
                ->default(0);

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('seller_wallet_transactions');
    }
};
