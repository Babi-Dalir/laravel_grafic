@extends('admin.layouts.master')
@section('content')
    <main class="main-content">
        @include('admin.layouts.error')
        <div class="card banner-form-card">
            <div class="card-body p-4 p-md-5">
                <div class="container-fluid">

                    <div class="d-flex align-items-center mb-4 pb-2 border-bottom">
                        <div class="banner-form-header text-primary d-flex align-items-center justify-content-center m-l-15">
                            <i class="ti-layers-alt" style="font-size: 20px;"></i>
                        </div>
                        <div>
                            <h5 class="card-title mb-1" style="font-weight: 700; color: #1e293b;">ایجاد اسلایدر جدید</h5>
                            <p class="text-muted small mb-0">مشخصات، لینک ارجاع و تصویر اسلایدر اصلی فرانت‌سایت را بارگذاری کنید.</p>
                        </div>
                    </div>

                    <form method="POST" action="{{route('sliders.store')}}" enctype="multipart/form-data">
                        @csrf

                        <div class="form-group row align-items-center mb-4">
                            <label class="col-xl-2 col-lg-3 col-form-label font-weight-bold text-secondary">لینک ارجاع (URL)</label>
                            <div class="col-xl-10 col-lg-9">
                                <div class="input-group">
                                    <div class="input-group-prepend">
                                        <span class="input-group-text banner-input-icon"><i class="ti-link"></i></span>
                                    </div>
                                    <input type="url" class="form-control banner-input-link" name="link" placeholder="https://example.com/target-page">
                                </div>
                                <small class="form-text text-muted mt-1">آدرس وب کامل صفحه‌ای که کاربر پس از کلیک روی اسلایدر به آن هدایت می‌شود.</small>
                            </div>
                        </div>

                        <div class="form-group row align-items-center mb-4">
                            <label class="col-xl-2 col-lg-3 col-form-label font-weight-bold text-secondary">تصویر اسلایدر</label>
                            <div class="col-xl-10 col-lg-9">
                                <div class="grafic-upload-zone">
                                    <input type="file" name="image" id="image" accept="image/*" onchange="previewSliderImage(this)">

                                    <div id="upload-slider-placeholder">
                                        <i class="ti-image text-muted mb-2" style="font-size: 32px; display: block;"></i>
                                        <span class="upload-zone-title">کلیک کنید یا عکس اسلایدر را به این کادر بکشید</span>
                                        <span class="text-muted small">فرمت‌های استاندارد: JPG, PNG, WEBP</span>
                                    </div>

                                    <div id="slider-preview-container" class="d-none" style="z-index: 10;">
                                        <p class="small text-success font-weight-bold mb-2"><i class="ti-check m-l-5"></i> تصویر اسلایدر آماده بارگذاری است:</p>
                                        <img id="slider-preview" src="#" alt="Preview" class="img-thumbnail shadow-sm" style="max-height: 140px; border-radius: 8px;">
                                        <span class="d-block text-muted small mt-2">برای تعویض عکس کلیک کنید</span>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="form-group row mb-0">
                            <div class="col-12 text-left">
                                <hr class="my-4">
                                <button type="submit" class="btn btn-success btn-uppercase px-4 font-weight-bold" style="border-radius: 8px; height: 42px;">
                                    <i class="ti-check-box m-r-5"></i> ذخیره اسلایدر جدید
                                </button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </main>

    <script>
        function previewSliderImage(input) {
            const container = document.getElementById('slider-preview-container');
            const placeholder = document.getElementById('upload-slider-placeholder');
            const preview = document.getElementById('slider-preview');
            if (input.files && input.files[0]) {
                const reader = new FileReader();
                reader.onload = function(e) {
                    preview.src = e.target.result;
                    placeholder.classList.add('d-none');
                    container.classList.remove('d-none');
                }
                reader.readAsDataURL(input.files[0]);
            }
        }
    </script>
@endsection
