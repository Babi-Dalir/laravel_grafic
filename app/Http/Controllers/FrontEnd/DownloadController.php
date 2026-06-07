<?php

namespace App\Http\Controllers\FrontEnd;

use App\Enums\DownloadStatus;
use App\Enums\OrderDetailStatus;
use App\Http\Controllers\Controller;
use App\Models\Downloads;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class DownloadController extends Controller
{
    public function download($token)
    {
        $download = Downloads::query()
            ->where('token', $token)
            ->firstOrFail();

        if (!$download->canDownload()) {
            abort(403);
        }

        if ($download->user_id != auth()->id()) {
            abort(403);
        }

        $download->registerDownload(request());

        $product = $download->product;
        $file = $product->mainFile()->firstOrFail();

        return Storage::disk('digital_files')
            ->download(
                $file->path,
                $file->original_name
            );
    }
}
