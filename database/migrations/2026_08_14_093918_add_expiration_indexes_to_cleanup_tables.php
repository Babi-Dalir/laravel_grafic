<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // verification_codes
        Schema::table('verification_codes', function (Blueprint $table) {
            $table->index('created_at', 'verification_codes_created_at_index');
        });

        // discount_campaigns
        Schema::table('discount_campaigns', function (Blueprint $table) {
            $table->index(
                ['status', 'expires_at'],
                'discount_campaigns_status_expires_at_index'
            );

            $table->index(
                ['status', 'starts_at'],
                'discount_campaigns_status_starts_at_index'
            );
        });

        // discounts
        Schema::table('discounts', function (Blueprint $table) {
            $table->index(
                ['status', 'expiration_date'],
                'discounts_status_expiration_date_index'
            );
        });

        // gift_carts
        Schema::table('gift_carts', function (Blueprint $table) {
            $table->index(
                ['status', 'expiration_date'],
                'gift_carts_status_expiration_date_index'
            );
        });
    }

    public function down(): void
    {
        Schema::table('verification_codes', function (Blueprint $table) {
            $table->dropIndex('verification_codes_created_at_index');
        });

        Schema::table('discount_campaigns', function (Blueprint $table) {
            $table->dropIndex('discount_campaigns_status_expires_at_index');
            $table->dropIndex('discount_campaigns_status_starts_at_index');
        });

        Schema::table('discounts', function (Blueprint $table) {
            $table->dropIndex('discounts_status_expiration_date_index');
        });

        Schema::table('gift_carts', function (Blueprint $table) {
            $table->dropIndex('gift_carts_status_expiration_date_index');
        });
    }
};
