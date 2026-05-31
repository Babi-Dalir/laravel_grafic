<?php

namespace App\Http\Controllers\FrontEnd;

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

        $download->increment('download_count');

        $download->update([
            'ip_address' => request()->ip(),
            'user_agent' => request()->userAgent(),
        ]);

        $product = $download->product;

        return Storage::disk('private')
            ->download(
                $product->main_file,
                $product->slug . '.' . $product->file_extension
            );
    }
}
