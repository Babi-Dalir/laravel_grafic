<?php

namespace App\Console\Commands;

use App\Models\VerificationCode;
use Illuminate\Console\Command;

class CleanVerificationCodes extends Command
{
    /**
     * The name and signature of the console command.
     */
    protected $signature = 'verification-codes:clean';

    /**
     * The console command description.
     */
    protected $description = 'Delete expired verification codes';

    /**
     * Execute the console command.
     */
    public function handle(): int
    {
        $count = VerificationCode::query()
            ->where('created_at', '<', now()->subMinutes(20))
            ->delete();

        $this->info("{$count} verification codes deleted.");

        return self::SUCCESS;
    }
}
