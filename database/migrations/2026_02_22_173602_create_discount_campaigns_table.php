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
        Schema::create('discount_campaigns', function (Blueprint $table) {
            $table->id();
            $table->string('name'); // مثلا: حراج یلدایی لوگوها
            $table->string('type')->index();
            $table->unsignedTinyInteger('percent'); // درصد تخفیف
            $table->unsignedTinyInteger('priority')->default(1); // اولویت: 3 محصول، 2 دسته، 1 کل سایت
            $table->timestamp('starts_at')->useCurrent();
            $table->timestamp('expires_at')->nullable();
            $table->string('status')->default(\App\Enums\DiscountCampaignStatus::Active)->index();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('discount_campaigns');
    }
};
