@extends('admin.layouts.master')
@section('content')
    <main class="main-content">
        @include('admin.layouts.error')
        <div class="card banner-form-card">
            <div class="card-body p-4 p-md-5">
                <div class="container-fluid">

                    <div class="d-flex align-items-center mb-4 pb-2 border-bottom">
                        <div class="banner-form-header text-warning d-flex align-items-center justify-content-center m-l-15" style="background: rgba(255, 193, 7, 0.1);">
                            <i class="ti-pencil-alt" style="font-size: 20px;"></i>
                        </div>
                        <div>
                            <h5 class="card-title mb-1" style="font-weight: 700; color: #1e293b;">ویرایش و اصلاح اسلایدر</h5>
                            <p class="text-muted small mb-0">شناسه اسلایدر: <span class="font-numeric font-weight-bold">#{{$slider->id}}</span> | می‌توانید لینک یا تصویر آن را تعویض کنید.</p>
                        </div>
                    </div>

                    <form method="POST" action="{{route('sliders.update',$slider->id)}}" enctype="multipart/form-data">
                        @csrf
                        @method('put')

                        <div class="form-group row align-items-center mb-4">
                            <label class="col-xl-2 col-lg-3 col-form-label font-weight-bold text-secondary">لینک ارجاع (URL)</label>
                            <div class="col-xl-10 col-lg-9">
                                <div class="input-group">
                                    <div class="input-group-prepend">
                                        <span class="input-group-text banner-input-icon"><i class="ti-link"></i></span>
                                    </div>
                                    <input type="url" class="form-control banner-input-link" name="link" value="{{$slider->link}}" placeholder="https://example.com/target-page">
                                </div>
                            </div>
                        </div>

                        <div class="form-group row mb-4">
                            <label class="col-xl-2 col-lg-3 col-form-label font-weight-bold text-secondary pt-2">تصویر اسلایدر</label>
                            <div class="col-xl-10 col-lg-9">
                                <div class="row align-items-stretch">

                                    <div class="col-md-4 col-sm-5 mb-3 mb-md-0">
                                        <div class="border text-center p-2 rounded bg-light h-100 d-flex align-items-center justify-content-center" style="position: relative; min-height: 160px;">
                                            <span class="badge badge-dark" style="position: absolute; top: 8px; right: 8px; font-weight: normal; font-size: 11px;">اسلایدر فعلی</span>
                                            <img id="current-slider-img" class="img-fluid rounded" style="max-height: 140px; object-fit: contain;" src="{{url('images/sliders/big/'.$slider->image)}}" alt="Current Slider">
                                        </div>
                                    </div>

                                    <div class="col-md-8 col-sm-7">
                                        <div class="grafic-upload-zone h-100">
                                            <input type="file" name="image" id="image" accept="image/*" onchange="updateSliderEditPreview(this)">

                                            <div id="upload-slider-edit-feedback">
                                                <i class="ti-cloud-up text-muted mb-2" style="font-size: 32px; display: block;"></i>
                                                <span class="upload-zone-title">بارگذاری تصویر جایگزین</span>
                                                <span class="text-muted small">جهت تعویض اسلایدر، فایل جدید را اینجا رها کنید</span>
                                            </div>
                                        </div>
                                    </div>

                                </div>
                            </div>
                        </div>

                        <div class="form-group row mb-0">
                            <div class="col-12 text-left">
                                <hr class="my-4">
                                <button type="submit" class="btn btn-warning btn-uppercase px-4 font-weight-bold text-dark" style="border-radius: 8px; height: 42px;">
                                    <i class="ti-check-box m-r-5"></i> اعمال تغییرات و ویرایش اسلایدر
                                </button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </main>

    <script>
        function updateSliderEditPreview(input) {
            const currentImg = document.getElementById('current-slider-img');
            const feedbackZone = document.getElementById('upload-slider-edit-feedback');
            if (input.files && input.files[0]) {
                const reader = new FileReader();
                reader.onload = function(e) {
                    currentImg.src = e.target.result;
                    feedbackZone.innerHTML = '<i class="ti-check text-success mb-2" style="font-size: 32px; display: block;"></i><span class="upload-zone-title text-success">تصویر جدید با موفقیت قرار گرفت!</span><span class="text-muted small">برای ثبت نهایی، دکمه زرد رنگ زیر را کلیک کنید</span>';
                }
                reader.readAsDataURL(input.files[0]);
            }
        }
    </script>
@endsection
