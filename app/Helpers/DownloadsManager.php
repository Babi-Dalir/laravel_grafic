<?php

namespace App\Helpers;

use App\Enums\DownloadStatus;
use Illuminate\Support\Facades\Storage;
use Intervention\Image\Drivers\Gd\Driver;
use Intervention\Image\ImageManager as IM;

class DownloadsManager
{
    public function isExpired(): bool
    {
        if (!$this->expire_at) {
            return false;
        }

        return now()->greaterThan($this->expire_at);
    }

    public function hasRemainingDownloads(): bool
    {
        return $this->download_count < $this->max_download;
    }

    public function isActive(): bool
    {
        return $this->status === DownloadStatus::Active->value;
    }

    public function canDownload(): bool
    {
        return
            $this->isActive() &&
            !$this->isExpired() &&
            $this->hasRemainingDownloads();
    }

    public function registerDownload($request): void
    {
        $this->increment('download_count');

        $this->update([
            'ip_address' => $request->ip(),
            'user_agent' => $request->userAgent(),
        ]);

        if ($this->download_count >= $this->max_download) {
            $this->update([
                'status' => DownloadStatus::Expired->value,
            ]);
        }
    }

}
