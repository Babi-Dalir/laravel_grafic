<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Product;
use App\Models\ProductFile;
use App\Services\ProductFileUploadService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;

class ProductFileController extends Controller
{
    public function index(Product $product)
    {
        $files = $product
            ->files()
            ->latest()
            ->get();

        return view('admin.products.files_list',compact('product', 'files'));
    }

    public function downloadFile(ProductFile $file)
    {
        $path = $file->path;

        if (! Storage::disk('digital_files')->exists($path)) {
            abort(404, 'فایل یافت نشد');
        }

        return Storage::disk('digital_files')->download(
            $path,
            $file->original_name
        );
    }

    protected $uploadService;

    public function __construct(ProductFileUploadService $uploadService)
    {
        $this->uploadService = $uploadService;
    }

    public function uploadChunk(Request $request, $productId)
    {
        $product = Product::findOrFail($productId);
        $user = auth()->user();

        if (!$user) {
            return response()->json(['status' => 'error', 'message' => 'نشست کاربری شما منقضی شده است.'], 401);
        }

        // 🔒 لایه امنیت و احراز هویت کنترلر
        if (!$user->hasRole('مدیر') && (int)$product->user_id !== (int)$user->id) {
            return response()->json(['status' => 'error', 'message' => 'شما دسترسی لازم برای این محصول را ندارید.'], 403);
        }

        $request->validate([
            'file' => 'required|file',
            'resumableIdentifier' => 'required|string',
            'resumableChunkNumber' => 'required|integer',
            'resumableTotalChunks' => 'required|integer',
            'resumableFilename' => 'required|string',
            'title' => 'nullable|string|max:255',
        ]);

        try {
            // دریافت مستقیم فایل به صورت شیء باینری (مستقیماً از Temp سرور بدون تبدیل به متن)
            $uploadedFile = $request->file('file');
            $fileUuid = $request->input('resumableIdentifier');
            $chunkIndex = (int)$request->input('resumableChunkNumber') - 1; // Resumable index 1-based است
            $totalChunks = (int)$request->input('resumableTotalChunks');
            $originalName = $request->input('resumableFilename');
            $title = $request->input('title');

            $productFile = $this->uploadService->uploadBinaryChunk(
                $uploadedFile,
                $fileUuid,
                $chunkIndex,
                $totalChunks,
                $originalName,
                $product,
                $title
            );

            return response()->json([
                'status' => 'success',
                'completed' => $productFile !== null,
            ]);

        } catch (\Throwable $e) {
            Log::error("خطا در API آپلود چانک: " . $e->getMessage());
            return response()->json([
                'status' => 'error',
                'message' => $e->getMessage()
            ], 500);
        }
    }
}
