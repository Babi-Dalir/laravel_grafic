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
        Schema::create('gift_carts', function (Blueprint $table) {
            $table->id();
            $table->string('gift_title');
            $table->string('code')->unique();
            $table->integer('gift_price');
            $table->integer('balance'); // مانده اعتبار (بسیار مهم)
            $table->string('status')->default(\App\Enums\GiftCartStatus::Active->value);
            $table->foreignId('user_id')->constrained('users')->cascadeOnDelete()->cascadeOnUpdate();
            $table->timestamp('expiration_date');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('gift_carts');
    }
};
