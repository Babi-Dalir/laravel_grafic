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

            $table->foreignId('seller_id')->constrained('sellers')->cascadeOnDelete();
            $table->foreignId('order_id')->nullable()->constrained()->nullOnDelete();

            $table->decimal('amount', 15, 2);

            $table->string('type');
            // sale, withdrawal, refund, commission, adjustment

            $table->string('description')->nullable();

            /**
             * 🧠 Money lifecycle
             */
            $table->string('status')->default(\App\Enums\WalletTransactionStatus::Pending->value);

            $table->string('reference_id')->nullable()->unique();

            $table->timestamp('release_at')->nullable();
            $table->timestamp('settled_at')->nullable();

            $table->foreignId('settlement_id')
                ->nullable()
                ->constrained('seller_settlements')
                ->nullOnDelete();

            $table->timestamps();

            $table->index(['seller_id', 'status']);
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
