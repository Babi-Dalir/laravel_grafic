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
        Schema::create('discount_campaign_targets', function (Blueprint $table) {
            $table->id();
            $table->foreignId('discount_campaign_id')->constrained()->cascadeOnDelete();
            $table->unsignedBigInteger('target_id')->index();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('discount_campaign_targets');
    }
};
