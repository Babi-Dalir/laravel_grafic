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
        Schema::create('products', function (Blueprint $table) {
            $table->id();
            // اطلاعات اصلی
            $table->string('name')->index();
            $table->string('e_name');
            $table->string('slug')->unique();

            // قیمت
            $table->integer('main_price')->default(0); // قیمت اصلی محصول
            $table->integer('price')->default(0);      // قیمت نهایی که نمایش داده میشه
            $table->integer('discount')->default(0);   // درصد یا مبلغ تخفیف

            // توضیحات و تصویر کاور
            $table->string('image')->nullable(); // تصویر اصلی محصول
            $table->text('description')->nullable();

            // فایل اصلی و اطلاعات پیش‌نمایش
            $table->string('main_file');
            $table->integer('file_size');
            $table->integer('download_count')->default(0);

            // وضعیت محصول
            $table->timestamp('spacial_start')->nullable();
            $table->timestamp('spacial_expiration')->nullable();
            $table->string('status')->default(\App\Enums\ProductStatus::Waiting->value);

            // ارتباط با فروشنده و دسته‌بندی
            $table->foreignId('user_id')->constrained('users')->cascadeOnDelete();
            $table->foreignId('category_id')->constrained('categories')->cascadeOnDelete();

            $table->softDeletes();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('products');
    }
};
