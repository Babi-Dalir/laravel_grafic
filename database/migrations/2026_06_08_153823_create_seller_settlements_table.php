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
        Schema::create('seller_settlements', function (Blueprint $table) {

            $table->id();

            $table->foreignId('seller_id')->constrained('sellers')->cascadeOnDelete();

            $table->decimal('amount', 15, 2);

            $table->string('status')->default(\App\Enums\SettlementStatus::Pending->value);

            $table->string('reference_id')->unique()->nullable();

            $table->text('admin_note')->nullable();

            $table->timestamp('paid_at')->nullable();

            $table->foreignId('paid_by')
                ->nullable()
                ->constrained('users')
                ->nullOnDelete();

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('seller_settlements');
    }
};
