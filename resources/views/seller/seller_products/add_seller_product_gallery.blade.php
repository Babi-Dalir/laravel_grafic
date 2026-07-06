@extends('admin.layouts.master')
@section('content')
    <main class="main-content">
        @include('admin.layouts.error')
        <div class="card">
            <div class="card-body">
                <div class="container">
                    <h6 class="card-title">ایجاد لیست تصاویر {{$product->name}}</h6>
                    <form class="dropzone border border-primary" id="sellerProductGalleryDropzone" method="post" action="{{route('store.seller.product.gallery',$product->id)}}" enctype="multipart/form-data">
                        @csrf
                        <div class="form-group row">
                            <div class="fallback">
                                <input type="file" name="file" multiple>
                            </div>
                        </div>
                    </form>

                    <livewire:seller.seller-products.gallery-list :product="$product"/>
                </div>
            </div>
        </div>
    </main>
@endsection
@section('scripts')
    <script>
        Dropzone.autoDiscover = false;

        $(document).ready(function () {
            var myDropzone = new Dropzone("#sellerProductGalleryDropzone", {
                paramName: "file",
                maxFilesize: 2,
                acceptedFiles: ".jpeg,.jpg,.png,.webp",

                error: function (file, response) {
                    var errorMessage = 'خطایی در آپلود تصویر رخ داد.';
                    if (typeof response === 'object' && response.error) {
                        errorMessage = response.error;
                    } else if (typeof response === 'string') {
                        if(response.includes("File is too big")) {
                            errorMessage = "حجم فایل بیشتر از حد مجاز (۲ مگابایت) است.";
                        } else if(response.includes("You can't upload files of this type")) {
                            errorMessage = "فرمت فایل انتخاب شده مجاز نیست.";
                        } else {
                            errorMessage = response;
                        }
                    }

                    Swal.fire({
                        title: "خطای آپلود!",
                        text: errorMessage,
                        icon: "error",
                        confirmButtonText: "باشه"
                    });

                    this.removeFile(file);
                },

                // 🟢 استفاده از متد بومی و مطمئن برای برقراری ارتباط با کامپوننت لایووایر
                success: function (file, response) {
                    var self = this;

                    // شلیک رویداد با متد ایمن جاوااسکریپتی نسخه ۳
                    if (window.Livewire) {
                        window.Livewire.dispatch('refreshSellerGalleryList');
                    }

                    // حذف افکت فایل بعد از اتمام انیمیشن
                    setTimeout(function() {
                        self.removeFile(file);
                    }, 1000);
                }
            });
        });
    </script>
@endsection
