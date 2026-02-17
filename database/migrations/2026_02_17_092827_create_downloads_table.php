<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('downloads', function (Blueprint $table) {
            $table->id();

            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->foreignId('product_id')->constrained()->cascadeOnDelete();
            $table->foreignId('order_detail_id')->constrained('order_details')->cascadeOnDelete();

            $table->string('token', 120)->unique();

            $table->integer('download_count')->default(0);
            $table->integer('max_download')->default(5);

            $table->string('status')->default(\App\Enums\DownloadStatus::Active->value);

            $table->timestamp('expire_at')->nullable();

            $table->string('ip_address')->nullable();
            $table->text('user_agent')->nullable();
            $table->timestamps();

        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('downloads');
    }
};
