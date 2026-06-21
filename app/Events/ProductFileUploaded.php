<?php

namespace App\Events;

use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class ProductFileUploaded
{
    use Dispatchable, SerializesModels;

    public int $productId; // 👈 تفکیک کامل از ORM برای Serialization بهینه
    public string $tempName;
    public string $originalName;
    public ?string $title;
    public string $fileUuid;

    public function __construct(int $productId, string $tempName, string $originalName, ?string $title, string $fileUuid)
    {
        $this->productId = $productId;
        $this->tempName = $tempName;
        $this->originalName = $originalName;
        $this->title = $title;
        $this->fileUuid = $fileUuid;
    }
}
