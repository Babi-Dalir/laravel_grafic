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

        $path = 'products/' . $product->slug . '/' . $product->main_file;

        return Storage::disk('digital_files')->download(
            $path,
            $product->slug . '.' . $product->file_extension
        );
    }
}
