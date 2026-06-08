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

            $table->foreignId('user_id')->unique()->constrained('users')
                ->cascadeOnDelete();

            // برند

            $table->string('brand_name')->nullable();

            // احراز هویت

            $table->string('national_code',20)->nullable(); //کدملی

            $table->string('first_name')->nullable();

            $table->string('last_name')->nullable();

            // بانکی

            $table->string('card_number',20)->nullable();

            $table->string('account_number',50)->nullable(); //شماره حساب

            $table->string('iban',50)->nullable(); //شماره شبا

            // مالی

            $table->decimal('balance',15, 2)->default(0); //کیف پول موجودی قایل برداشت

            $table->decimal('pending_balance',15, 2)->default(0); //کیف پول موجودی در انتظار تسویه

            $table->decimal('total_income', 15, 2)->default(0); // مجموع درامد

            $table->decimal('total_settlement', 15, 2)->default(0); //مجموع تسویه ها

            // آمار

            $table->integer('sales_count')->default(0); //تعداد فروش

            // وضعیت

            $table->string('status')->default(\App\Enums\SellerStatus::Active->value);

            // تایید اطلاعات بانکی

            $table->boolean('bank_verified')->default(false);

            // تاریخ تایید

            $table->timestamp('verified_at')->nullable();

            $table->timestamp('last_settlement_at') //تاریخ اخرین تسویه
                ->nullable();

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
