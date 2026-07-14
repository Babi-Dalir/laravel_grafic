<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Product;
use App\Models\ProductFile;
use App\Services\Upload\FileAssemblerService;
use App\Events\ProductFileUploaded;
use App\Enums\UploadFileStatus;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Log;

class ProductFileController extends Controller
{
    protected FileAssemblerService $assembler;

    public function __construct(FileAssemblerService $assembler)
    {
        $this->assembler = $assembler;
    }

    public function index(Product $product)
    {
        $files = $product->files()->latest()->get();
        return view('admin.products.files_list', compact('product', 'files'));
    }

    public function downloadFile(ProductFile $file)
    {
        $disk = Storage::disk('digital_files');
        $path = "products/{$file->product_id}/{$file->stored_name}";

        if (!$disk->exists($path)) {
            abort(404, 'فایل یافت نشد');
        }

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

        $originalName = $request->input('resumableFilename');
        $extension = strtolower(pathinfo($originalName, PATHINFO_EXTENSION));

        // 🟢 بررسی زودهنگام پسوند فایل با کانفیگ شما (در صورت عدم وجود کانفیگ، از لیست پیش‌فرض استفاده می‌شود)
        $allowedExtensions = config('files.allowed_extensions', [
            'dxf', 'png', 'jpg', 'jpeg', 'cdr', 'cdt', 'cmx', 'cpt', 'art', 'svg', 'webp', 'tiff',
            'stl', 'obj', '3ds', 'stp', 'step', 'zip', 'psd', 'ai', 'eps', 'pdf', 'ttf', 'otf'
        ]);

        if (!in_array($extension, $allowedExtensions, true)) {
            return response()->json([
                'status' => 'error',
                'message' => "پسوند فایل ارسالی ({$extension}) مجاز نیست. لطفا فایل خود را به صورت ZIP آپلود کنید."
            ], 422);
        }

        if ((int)$request->input('resumableTotalSize') > 4294967296) {
            return response()->json(['status' => 'error', 'message' => 'حجم فایل بیش از حد مجاز است.'], 422);
        }

        try {
            $fileUuid = $request->input('resumableIdentifier');
            $chunkNumber = (int)$request->input('resumableChunkNumber') - 1;
            $totalChunks = (int)$request->input('resumableTotalChunks');
            $title = $request->input('title') ?: pathinfo($originalName, PATHINFO_FILENAME);

            // ۱. ذخیره‌سازی امن چانک موقت با استفاده از ساختار اسمبلر
            $this->assembler->storeChunk($request->file('file'), $fileUuid, $chunkNumber);

            // ۲. بررسی اینکه آیا آخرین چانک دریافت شده است یا خیر
            $isLastChunk = ($chunkNumber + 1) === $totalChunks;

            if ($isLastChunk) {
                // ترکیب چانک‌ها به یک فایل موقت واحد روی دیسک دیجیتال تمپ
                $tempName = $this->assembler->combine($fileUuid, $totalChunks, $extension);

                // ایجاد رکورد اولیه در دیتابیس با وضعیت در حال آپلود/پردازش
                ProductFile::create([
                    'product_id' => $product->id,
                    'title' => $title,
                    'original_name' => $originalName,
                    'stored_name' => $tempName,
                    'extension' => $extension,
                    'mime_type' => $request->file('file')->getClientMimeType(),
                    'size' => (int)$request->input('resumableTotalSize'),
                    'sha256' => 'temp_' . $fileUuid,
                    'status' => UploadFileStatus::Uploading->value,
                ]);

                // ۳. شلیک رویداد پردازش امنیتی ناهمگام
                event(new ProductFileUploaded($product->id, $tempName, $originalName, $fileUuid, $title));
            }

            return response()->json([
                'status' => 'success',
                'completed' => $isLastChunk,
            ]);

        } catch (\Throwable $e) {
            Log::error("خطا در API آپلود چانک: " . $e->getMessage());
            return response()->json(['status' => 'error', 'message' => $e->getMessage()], 500);
        }
    }
}
