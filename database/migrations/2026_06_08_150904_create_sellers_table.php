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
        Schema::create('sellers', function (Blueprint $table) {

            $table->id();

            $table->foreignId('user_id')->unique()->constrained()->cascadeOnDelete();

            $table->string('brand_name')->nullable();

            $table->string('national_code', 20)->nullable();
            $table->string('first_name')->nullable();
            $table->string('last_name')->nullable();

            $table->string('card_number', 20)->nullable();
            $table->string('account_number', 50)->nullable();
            $table->string('iban', 50)->nullable();

            $table->string('status')->default(\App\Enums\SellerStatus::Active->value);

            $table->boolean('bank_verified')->default(false);
            $table->timestamp('verified_at')->nullable();

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('sellers');
    }
};
