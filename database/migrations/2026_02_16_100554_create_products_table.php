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

            // قیمت (فقط قیمت پایه)
            $table->integer('main_price')->default(0);
            $table->integer('sold')->default(0);

            // توضیحات و تصویر
            $table->string('image')->nullable();
            $table->text('description')->nullable();

            // آمار
            $table->integer('download_count')->default(0);

            // وضعیت و دسته‌بندی
            $table->string('status')->default(\App\Enums\ProductStatus::Draft->value);
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
