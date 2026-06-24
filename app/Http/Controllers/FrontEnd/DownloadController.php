<?php

namespace App\Http\Controllers\FrontEnd;

use App\Http\Controllers\Controller;
use App\Models\Downloads;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class DownloadController extends Controller
{
    public function download($token, Request $request)
    {
        // 🟢 حل ایراد سوم: ایگر لود کردن رابطه متد اصلی فایل (mainFile) برای به صفر رساندن تعداد کوئری‌ها
        $download = Downloads::query()
            ->with(['product.mainFile', 'product.files', 'orderDetail'])
            ->where('token', $token)
            ->firstOrFail();

        if ($download->user_id !== auth()->id()) {
            abort(403, 'این لینک دانلود متعلق به حساب کاربری شما نیست.');
        }

        if (!$download->canDownload()) {
            abort(403, 'لینک دانلود منقضی شده یا سقف تعداد دانلود به پایان رسیده است.');
        }

        try {
            // 🟢 حل ایراد دوم و چهارم: پاس دادن مقادیر صریح و گرفتن اکسپشن لایه مدل در کنترلر
            $download->registerDownload(
                $request->ip(),
                $request->userAgent()
            );
        } catch (\RuntimeException $e) {
            abort(403, $e->getMessage());
        }

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
