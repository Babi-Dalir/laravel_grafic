@extends('admin.layouts.master')
@section('content')
    <main class="main-content">
        @include('admin.layouts.error')
        <div class="card">
            <div class="card-body">
                <div class="container">
                    <h6 class="card-title">ویرایش محصول</h6>
                    <form method="POST" action="{{route('products.update',$product->id)}}"
                          enctype="multipart/form-data">
                        @csrf
                        @method('PUT')
                        <div class="form-group row">
                            <label class="col-sm-2 col-form-label">نام محصول</label>
                            <div class="col-sm-10">
                                <input type="text" class="form-control text-left" dir="rtl" name="name"
                                       value="{{$product->name}}">
                            </div>
                        </div>
                        <div class="form-group row">
                            <label class="col-sm-2 col-form-label">نام انگلیسی محصول</label>
                            <div class="col-sm-10">
                                <input type="text" class="form-control text-left" dir="ltr" name="e_name"
                                       value="{{$product->e_name}}">
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

                                                {{-- 🎯 لایه سوم: برگ نهایی و عمیق‌ترین سطح (بررسی وضعیت selected با دسته‌بندی فعلی محصول) --}}
                                                @foreach($subVal as $leaf)
                                                    <option value="{{ $leaf->id }}" {{ $product->category_id == $leaf->id ? 'selected' : '' }} style="padding-right: 30px; color: #0284c7;">
                                                        &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;🔸 {{ $leaf->name }}
                                                    </option>
                                                @endforeach
                                            @else
                                                {{-- 🎯 اگر دسته کلاً ۲ لایه‌ای بود و لایه دومش خودش برگ نهایی بود (بررسی وضعیت selected با دسته‌بندی فعلی محصول) --}}
                                                <option value="{{ $subVal->id }}" {{ $product->category_id == $subVal->id ? 'selected' : '' }} style="padding-right: 15px; color: #0284c7;">
                                                    &nbsp;&nbsp;&nbsp;&nbsp;🔸 {{ $subVal->name }}
                                                </option>
                                            @endif
                                        @endforeach
                                    @endforeach

                                </select>
                                <small class="form-text text-muted mt-2">دسته‌بندی فعلی محصول به صورت خودکار انتخاب شده است. در صورت نیاز می‌توانید آن را به یک زیردسته نهایی (🔸) دیگر تغییر دهید.</small>
                            </div>
                        </div>
                        <div class="form-group row">
                            <label class="col-sm-2 col-form-label">قیمت اصلی</label>
                            <div class="col-sm-10">
                                <input type="text" class="form-control text-left" dir="rtl" name="main_price"
                                       value="{{$product->main_price}}">
                            </div>
                        </div>
                        @php
                            // پیدا کردن کمپین اختصاصی این محصول برای نمایش در فرم
                            $productCampaign = \App\Models\DiscountCampaignTarget::where('target_id', $product->id)
                                ->whereHas('campaign', fn($q) => $q->where('type', 'product'))
                                ->first()?->campaign;
                        @endphp

                        <div class="form-group row">
                            <label class="col-sm-2 col-form-label">درصد تخفیف فعلی</label>
                            <div class="col-sm-10">
                                <input type="text" class="form-control text-left" dir="rtl" name="discount"
                                       value="{{ $productCampaign ? $productCampaign->percent : '' }}">
                            </div>
                        </div>
                        {{-- فیلدهای تاریخ هم دقیقاً به همین شکل با مقدار $productCampaign->starts_at پر میشن --}}
                        <div class="form-group row">
                            <label class="col-sm-2 col-form-label">تگ ها</label>
                            <div class="col-sm-10">
                                <select name="tags[]" id="tags"
                                        class="form-control js-example-basic-single select2-hidden-accessible" multiple>
                                    @foreach($tags as $key => $value)
                                        @if(in_array($key,$product->tags->pluck('id')->toArray()))
                                            <option selected value="{{$key}}">{{$value}}</option>
                                        @else
                                            <option value="{{$key}}">{{$value}}</option>
                                        @endif
                                    @endforeach
                                </select>
                            </div>
                        </div>
                        <div class="form-group row">
                            <label class="col-sm-2 col-form-label">توضیحات</label>
                            <div class="col-sm-10">
                                <textarea type="text" class="form-control text-left" dir="rtl" name="description"
                                          id="editor1" cols="30" rows="10">{{$product->description}}</textarea>
                            </div>
                        </div>
                        <div class="form-group row">
                            <label class="col-sm-2 col-form-label" for="file"> آپلود عکس </label>
                            <div class="col-sm-10">
                                <input class="form-control-file" type="file" name="image" id="image">
                                @if($product->image)
                                    <small>فایل فعلی: {{ basename($product->image) }}</small>
                                @endif
                            </div>
                        </div>
                        <div class="form-group row">
                            <label class="col-sm-2 col-form-label"> تاریخ شروع شگفت انگیز</label>
                            <div class="col-sm-10">
                                <input type="text" id="spacial_start" class="text-left form-control" dir="rtl" name="spacial_start"
                                       value="{{ ($productCampaign && $productCampaign->starts_at) ? \Hekmatinasser\Verta\Verta::instance($productCampaign->starts_at)->format('Y/m/d') : null }}">
                            </div>
                        </div>

                        <div class="form-group row">
                            <label class="col-sm-2 col-form-label"> تاریخ انقضای شگفت انگیز</label>
                            <div class="col-sm-10">
                                <input type="text" id="spacial_expiration" class="text-left form-control" dir="rtl" name="spacial_expiration"
                                       value="{{ ($productCampaign && $productCampaign->expires_at) ? \Hekmatinasser\Verta\Verta::instance($productCampaign->expires_at)->format('Y/m/d') : null }}">
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
