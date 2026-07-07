@extends('admin.layouts.master')
@section('content')
    <main class="main-content">
        @include('admin.layouts.error')
        <div class="card">
            <div class="card-body">
                <div class="container">
                    <h6 class="card-title">ایجاد محصول</h6>
                    <form method="POST" action="{{route('store.seller.product')}}" enctype="multipart/form-data">
                        @csrf
                        <div class="form-group row">
                            <label class="col-sm-2 col-form-label">نام محصول</label>
                            <div class="col-sm-10">
                                <input type="text" class="form-control text-left" dir="rtl" name="name">
                            </div>
                        </div>
                        <div class="form-group row">
                            <label class="col-sm-2 col-form-label">نام انگلیسی محصول</label>
                            <div class="col-sm-10">
                                <input type="text" class="form-control text-left" dir="rtl" name="e_name">
                            </div>
                        </div>
                        <div class="form-group row mb-4">
                            <label class="col-sm-2 col-form-label font-weight-bold">دسته بندی محصول</label>
                            <div class="col-sm-10">
                                <select name="category_id" class="form-select text-right" dir="rtl" required style="width: 100%;">
                                    <option value="">-- انتخاب لایه نهایی دسته‌بندی --</option>

                                    @foreach($categories as $mainCategory => $subContent)
                                        {{-- 📂 لایه اول: دسته اصلی (غیرقابل انتخاب) --}}
                                        <option value="" disabled style="font-weight: bold; color: #1e293b; background-color: #f1f5f9;">
                                            📂 {{ $mainCategory }}
                                        </option>

                                        @foreach($subContent as $subKey => $subVal)
                                            @if(is_string($subKey))
                                                {{-- 🔹 لایه دوم: زیردسته واسط که خودش فرزند دارد (غیرقابل انتخاب) --}}
                                                <option value="" disabled style="font-weight: 600; color: #475569; padding-right: 15px;">
                                                    &nbsp;&nbsp;&nbsp;&nbsp;🔹 {{ $subKey }}
                                                </option>

                                                {{-- 🎯 لایه سوم: برگ نهایی و عمیق‌ترین سطح (قابل انتخاب) --}}
                                                @foreach($subVal as $leaf)
                                                    <option value="{{ $leaf->id }}" {{ old('category_id') == $leaf->id ? 'selected' : '' }} style="padding-right: 30px; color: #0284c7;">
                                                        &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;🔸 {{ $leaf->name }}
                                                    </option>
                                                @endforeach
                                            @else
                                                {{-- 🎯 اگر دسته کلاً ۲ لایه‌ای بود و لایه دومش خودش برگ نهایی بود (قابل انتخاب) --}}
                                                <option value="{{ $subVal->id }}" {{ old('category_id') == $subVal->id ? 'selected' : '' }} style="padding-right: 15px; color: #0284c7;">
                                                    &nbsp;&nbsp;&nbsp;&nbsp;🔸 {{ $subVal->name }}
                                                </option>
                                            @endif
                                        @endforeach
                                    @endforeach

                                </select>
                                <small class="form-text text-muted mt-2">توجه: گزینه‌های 📂 و 🔹 دسته‌های مادر هستند. شما فقط مجاز به انتخاب گزینه‌های نارنجی (🔸) هستید.</small>
                            </div>
                        </div>
                        <div class="form-group row">
                            <label class="col-sm-2 col-form-label">قیمت اصلی</label>
                            <div class="col-sm-10">
                                <input type="text" class="form-control text-left" dir="rtl" name="main_price">
                            </div>
                        </div>
                        <div class="form-group row">
                            <label class="col-sm-2 col-form-label">درصد تخفیف محصول(اختیاری)</label>
                            <div class="col-sm-10">
                                <input type="text" class="form-control text-left" dir="rtl" name="discount">
                            </div>
                        </div>
                        <div class="form-group row">
                            <label class="col-sm-2 col-form-label">تگ ها</label>
                            <div class="col-sm-10">
                                <select name="tags[]" id="tags"
                                        class="form-control js-example-basic-single select2-hidden-accessible" multiple>
                                    @foreach($tags as $key => $value)
                                        <option value="{{$key}}">{{$value}}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                        <div class="form-group row">
                            <label class="col-sm-2 col-form-label">توضیحات</label>
                            <div class="col-sm-10">
                                <textarea type="text" class="form-control text-left" dir="rtl" name="description"
                                          id="editor1" cols="30" rows="10"></textarea>
                            </div>
                        </div>
                        <div class="form-group row">
                            <label class="col-sm-2 col-form-label" for="file"> آپلود عکس </label>
                            <input class="col-sm-10 form-control-file" type="file" name="image" id="image">
                        </div>
                        <h6 class="mb-4 text-primary">تنظیمات تخفیف شگفت‌انگیز (اختیاری)</h6>
                        <div class="form-group row">
                            <label class="col-sm-2 col-form-label"> تاریخ شروع شگفت انگیز</label>
                            <div class="col-sm-10">
                                <input type="text" id="spacial_start" class="text-left form-control" dir="rtl"
                                       name="spacial_start">
                            </div>
                        </div>
                        <div class="form-group row">
                            <label class="col-sm-2 col-form-label"> تاریخ انقضای شگفت انگیز</label>
                            <div class="col-sm-10">
                                <input type="text" id="spacial_expiration" class="text-left form-control" dir="rtl"
                                       name="spacial_expiration">
                            </div>
                        </div>
                        <div class="form-group row">
                            <button type="submit" class="btn btn-success btn-uppercase">
                                <i class="ti-check-box m-r-5"></i> ذخیره
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </main>
@endsection
@section('scripts')
    @include('admin.layouts.ckeditorConf')
    <script>
        $('select').select2({
            dir: "rtl",
            dropdownAutoWidth: true,
            $dropdownParent: $('#parent')
        })
        $('.form-select').select2()

        var customOptions = {
            placeholder: "روز / ماه / سال"
            , twodigit: false
            , closeAfterSelect: true
            , nextButtonIcon: "fa fa-arrow-circle-right"
            , previousButtonIcon: "fa fa-arrow-circle-left"
            , buttonsColor: "#5867dd"
            , markToday: true
            , markHolidays: true
            , highlightSelectedDay: true
            , sync: true
            , gotoToday: true
        }
        kamaDatepicker('spacial_start', customOptions);
        kamaDatepicker('spacial_expiration', customOptions);
        $('.form-select').select2()
    </script>
@endsection
