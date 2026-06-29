<?php

namespace App\Http\Controllers\FrontEnd;

use App\Http\Controllers\Controller;
use App\Models\Downloads;
use App\Exceptions\DownloadLimitException;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class DownloadController extends Controller
{
    public function download($token, Request $request)
    {
        // ۱. ایگر لودینگ روابط برای جلوگیری از خطای N+1
        $download = Downloads::query()
            ->with(['product.mainFile', 'product.files', 'orderDetail'])
            ->where('token', $token)
            ->firstOrFail();

        // ۲. لایه امنیت و احراز هویت (Strict Security) -> ۴۰۳ سخت‌گیرانه
        if ($download->user_id !== auth()->id()) {
            abort(403, 'این لینک دانلود متعلق به حساب کاربری شما نیست.');
        }

        // ۳. لایه بیزینس (Business Logic) -> کنترلر هیچ شروط بیزینسی ندارد و فقط امضا می‌گیرد
        try {
            $download->registerDownload(
                $request->ip(),
                $request->userAgent()
            );

        } catch (DownloadLimitException $e) {
            // 🟢 بازگشت نرم برای خطای منطقی بیزینس (UX تمیز و بدون کرش)
            return back()->with('error', $e->getMessage());
        }

        // ۴. لایه تحویل فایل (File Stream Delivery)
        $product = $download->product;
        $file = $product->mainFile ?? $product->files?->first();

        if (!$file || !Storage::disk('digital_files')->exists($file->path)) {
            abort(404, 'فایل فیزیکی محصول یافت نشد.');
        }

        return Storage::disk('digital_files')
            ->download(
                $file->path,
                $file->original_name
            );
    }
}
