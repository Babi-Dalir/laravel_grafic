@extends('admin.layouts.master')
@section('content')
    <main class="main-content">
        @include('admin.layouts.error')
        <div class="card banner-form-card">
            <div class="card-body p-4 p-md-5">
                <div class="container-fluid">

                    <div class="d-flex align-items-center mb-4 pb-2 border-bottom">
                        <div class="banner-form-header text-primary d-flex align-items-center justify-content-center m-l-15">
                            <i class="ti-layout-media-left-alt" style="font-size: 20px;"></i>
                        </div>
                        <div>
                            <h5 class="card-title mb-1" style="font-weight: 700; color: #1e293b;">ایجاد بنر جدید</h5>
                            <p class="text-muted small mb-0">مشخصات، پیوند ارجاع و تصویر بنر تبلیغاتی خود را وارد کنید.</p>
                        </div>
                    </div>

                    <form method="POST" action="{{route('banners.store')}}" enctype="multipart/form-data">
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
                                <small class="form-text text-muted mt-1">آدرس وب کامل صفحه‌ای که کاربر پس از کلیک باید به آن هدایت شود.</small>
                            </div>
                        </div>

                        <div class="form-group row align-items-center mb-4">
                            <label class="col-xl-2 col-lg-3 col-form-label font-weight-bold text-secondary">موقعیت نمایش</label>
                            <div class="col-xl-4 col-lg-6">
                                <select name="type" class="form-control custom-select banner-select-type">
                                    <option value="{{\App\Enums\BannerType::TopBanner->value}}">بنر بالا (سراسر سایت)</option>
                                    <option value="{{\App\Enums\BannerType::SideBanner->value}}">بنر کناری (سایدبار فرانت)</option>
                                    <option value="{{\App\Enums\BannerType::MediumBanner->value}}">بنر متوسط (دوتایی وسط)</option>
                                    <option value="{{\App\Enums\BannerType::SmallBanner->value}}">بنر کوچک (چهارم دوتایی)</option>
                                    <option value="{{\App\Enums\BannerType::LargeBanner->value}}">بنر بزرگ (افقی انتهای صفحه)</option>
                                </select>
                            </div>
                        </div>

                        <div class="form-group row align-items-center mb-4">
                            <label class="col-xl-2 col-lg-3 col-form-label font-weight-bold text-secondary">تصویر بنر</label>
                            <div class="col-xl-10 col-lg-9">
                                <div class="grafic-upload-zone">
                                    <input type="file" name="image" id="image" accept="image/*" onchange="previewImage(this)">

                                    <div id="upload-zone-placeholder">
                                        <i class="ti-image text-muted mb-2" style="font-size: 32px; display: block;"></i>
                                        <span class="upload-zone-title">کلیک کنید یا عکس را به این کادر بکشید</span>
                                        <span class="text-muted small">فرمت‌های استاندارد: JPG, PNG, WEBP</span>
                                    </div>

                                    <div id="live-preview-container" class="d-none" style="z-index: 10;">
                                        <p class="small text-success font-weight-bold mb-2"><i class="ti-check m-l-5"></i> تصویر آماده بارگذاری است:</p>
                                        <img id="live-preview" src="#" alt="Preview" class="img-thumbnail shadow-sm" style="max-height: 130px; border-radius: 8px;">
                                        <span class="d-block text-muted small mt-2">برای جابجایی کلیک کنید</span>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="form-group row mb-0">
                            <div class="col-12 text-left">
                                <hr class="my-4">
                                <button type="submit" class="btn btn-success btn-uppercase px-4 font-weight-bold" style="border-radius: 8px; height: 42px;">
                                    <i class="ti-check-box m-r-5"></i> ذخیره بنر جدید
                                </button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </main>

    <script>
        function previewImage(input) {
            const container = document.getElementById('live-preview-container');
            const placeholder = document.getElementById('upload-zone-placeholder');
            const preview = document.getElementById('live-preview');
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
