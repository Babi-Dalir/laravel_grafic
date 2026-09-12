<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('verification_codes', function (Blueprint $table) {
            $table->timestamp('expires_at')->nullable()->after('code');
            $table->unsignedTinyInteger('attempts')->default(0)->after('expires_at');
            $table->timestamp('consumed_at')->nullable()->after('attempts');

            $table->index(['mobile', 'expires_at']);
        });
    }

    public function down(): void
    {
        Schema::table('verification_codes', function (Blueprint $table) {
            $table->dropIndex(['mobile', 'expires_at']);

            $table->dropColumn([
                'expires_at',
                'attempts',
                'consumed_at',
            ]);
        });
    }
};
