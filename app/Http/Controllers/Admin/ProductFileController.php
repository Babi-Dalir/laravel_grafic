<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Product;
use App\Models\ProductFile;
use App\Services\ProductFileUploadService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class ProductFileController extends Controller
{
    protected ProductFileUploadService $uploadService;

    public function __construct(ProductFileUploadService $uploadService)
    {
        $this->uploadService = $uploadService;
    }

    public function index(Product $product)
    {
        $files = $product->files()->latest()->get();
        return view('admin.products.files_list', compact('product', 'files'));
    }

    public function downloadFile(ProductFile $file)
    {
        $disk = Storage::disk('digital_files');
        $path = $file->path;

        if (!$disk->exists($path)) {
            abort(404, 'فایل یافت نشد');
        }

        // 🔒 بررسی کاملاً استاندارد و Runtime برای نوع درایور دیسک
        if ($disk->getConfig()['driver'] === 's3') {
            return redirect()->away($disk->temporaryUrl($path, now()->addMinutes(5), [
                'ResponseContentDisposition' => 'attachment; filename="' . urlencode($file->original_name) . '"'
            ]));
        }

        return $disk->download($path, $file->original_name);
    }

    public function uploadChunk(Request $request, Product $product)
    {
        $user = auth()->user();
        if (!$user) {
            return response()->json(['status' => 'error', 'message' => 'نشست کاربری شما منقضی شده است.'], 401);
        }

        $isOwner = (int)$product->user_id === (int)$user->id;
        if (!$user->hasRole('مدیر') && !$isOwner) {
            return response()->json(['status' => 'error', 'message' => 'شما دسترسی لازم برای ویرایش فایل‌های این محصول را ندارید.'], 403);
        }

        $request->validate([
            'file' => 'required|file',
            'resumableIdentifier' => 'required|string',
            'resumableChunkNumber' => 'required|integer',
            'resumableTotalChunks' => 'required|integer',
            'resumableFilename' => 'required|string',
            'resumableTotalSize' => 'required|integer',
            'title' => 'nullable|string|max:255',
        ]);

        if ((int)$request->input('resumableTotalSize') > 4294967296) {
            return response()->json(['status' => 'error', 'message' => 'حجم فایل بیش از حد مجاز است.'], 422);
        }

        try {
            $isLastChunkCombined = $this->uploadService->uploadBinaryChunk(
                $request->file('file'),
                $request->input('resumableIdentifier'),
                (int)$request->input('resumableChunkNumber') - 1,
                (int)$request->input('resumableTotalChunks'),
                $request->input('resumableFilename'),
                $product,
                $request->input('title')
            );

            return response()->json([
                'status' => 'success',
                'completed' => $isLastChunkCombined, // کلاینت متوجه می‌شود کل چانک‌ها دریافت و به صف ارسال شده است
            ]);

        } catch (\Throwable $e) {
            \Illuminate\Support\Facades\Log::error("خطا در API آپلود چانک: " . $e->getMessage());
            return response()->json(['status' => 'error', 'message' => $e->getMessage()], 500);
        }
    }
}
