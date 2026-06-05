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
        Schema::create('seller_requests', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')
                ->unique()
                ->constrained('users')
                ->cascadeOnDelete();

            $table->string('brand_name')->nullable(); // نام برند یا استودیو

            $table->text('portfolio')->nullable(); // نمونه کارها
            $table->string('resume')->nullable();

            $table->text('reason')->nullable(); // توضیحات متقاضی

            $table->string('status')->default(\App\Enums\SellerRequestStatus::Pending->value);

            $table->timestamp('reviewed_at')->nullable(); //تاریخ بررسی درخواست

            $table->text('admin_note')->nullable(); //دلیل رد یا توضیح مدیر
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('seller_requests');
    }
};
