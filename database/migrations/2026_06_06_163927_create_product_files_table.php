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
        Schema::create('product_files', function (Blueprint $table) {

            $table->id();

            $table->foreignId('product_id')
                ->constrained()
                ->cascadeOnUpdate()
                ->cascadeOnDelete();
            // مثلا فایل لایسنس یا بونوس
            $table->string('title')->nullable();

            // نامی که فروشنده آپلود کرده
            $table->string('original_name');

            // نام ذخیره شده روی سرور
            $table->string('stored_name',100)->index();

            // zip, rar, psd ...
            $table->string('extension', 20);

            // application/zip
            $table->string('mime_type')->nullable();

            // byte
            $table->unsignedBigInteger('size');

            // جلوگیری از فایل تکراری
            $table->string('sha256', 64);

            // فایل اصلی محصول
            $table->boolean('is_default')->default(false);

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('product_files');
    }
};
