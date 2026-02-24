@extends('admin.layouts.master')
@section('content')
    <main class="main-content">
        @include('admin.layouts.error')
        <div class="card">
            <div class="card-body">
                <div class="container">
                    <h6 class="card-title">ایجاد کمپین تخفیف جدید</h6>
                    <form method="POST" action="{{ route('discount_campaigns.store') }}">
                        @csrf

                        {{-- نام کمپین --}}
                        <div class="form-group row">
                            <label class="col-sm-2 col-form-label">نام کمپین</label>
                            <div class="col-sm-10">
                                <input type="text" class="form-control text-left" dir="rtl" name="name">
                            </div>
                        </div>

                        {{-- نوع تخفیف --}}
                        <div class="form-group row">
                            <label class="col-sm-2 col-form-label">نوع تخفیف</label>
                            <div class="col-sm-10">
                                <select name="type" id="campaign_type" class="form-select select2-single">
                                    <option value="{{ \App\Enums\DiscountCampaignType::Product->value }}">مخصوص محصولات خاص</option>
                                    <option value="{{ \App\Enums\DiscountCampaignType::Category->value }}">مخصوص دسته‌بندی‌ها</option>
                                    <option value="{{ \App\Enums\DiscountCampaignType::Global->value }}">کل سایت (بدون انتخاب محصول/دسته)</option>
                                </select>
                            </div>
                        </div>

                        {{-- بخش انتخاب محصول (قابل سرچ) --}}
                        <div id="product_selection" class="form-group row">
                            <label class="col-sm-2 col-form-label">انتخاب محصولات</label>
                            <div class="col-sm-10">
                                <select name="target_ids[]" id="product_select" class="form-control select2-multiple" multiple>
                                    @foreach($products as $key => $value)
                                        <option value="{{$key}}">{{$value}}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>

                        {{-- بخش انتخاب دسته‌بندی (قابل سرچ) --}}
                        <div id="category_selection" class="form-group row" style="display: none;">
                            <label class="col-sm-2 col-form-label">انتخاب دسته‌بندی‌ها</label>
                            <div class="col-sm-10">
                                <select name="target_ids[]" id="category_select" class="form-control select2-multiple" multiple>
                                    @foreach($categories as $key => $value)
                                        <option value="{{$key}}">{{$value}}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>

                        {{-- درصد تخفیف --}}
                        <div class="form-group row">
                            <label class="col-sm-2 col-form-label">درصد تخفیف</label> {{-- در اینجا همان درصد تخفیف است --}}
                            <div class="col-sm-10">
                                <input type="text" class="form-control text-left" dir="rtl" name="percent" placeholder="مثلا 20">
                            </div>
                        </div>

                        {{-- تاریخ‌ها --}}
                        <div class="form-group row">
                            <label class="col-sm-2 col-form-label"> تاریخ شروع </label>
                            <div class="col-sm-10">
                                <input type="text" id="starts_at" class="text-left form-control" dir="rtl" name="starts_at">
                            </div>
                        </div>

                        <div class="form-group row">
                            <label class="col-sm-2 col-form-label"> تاریخ انقضا </label>
                            <div class="col-sm-10">
                                <input type="text" id="expires_at" class="text-left form-control" dir="rtl" name="expires_at">
                            </div>
                        </div>

                        <div class="form-group row">
                            <button type="submit" class="btn btn-success btn-uppercase">
                                <i class="ti-check-box m-r-5"></i> ذخیره کمپین
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </main>
@endsection

@section('scripts')
    <script>
        // فعال‌سازی سرچ Select2
        $('.select2-single').select2({
            dir: "rtl",
            dropdownAutoWidth: true,
            width: '100%'
        });

        $('.select2-multiple').select2({
            dir: "rtl",
            placeholder: "جستجو و انتخاب کنید...",
            dropdownAutoWidth: true,
            width: '100%'
        });

        // منطق نمایش و مخفی کردن فیلدها
        $('#campaign_type').on('change', function() {
            let type = $(this).val();
            if (type === 'product') {
                $('#product_selection').fadeIn();
                $('#category_selection').hide();
            } else if (type === 'category') {
                $('#product_selection').hide();
                $('#category_selection').fadeIn();
            } else {
                $('#product_selection').hide();
                $('#category_selection').hide();
            }
        });

        // تنظیمات تاریخ
        var customOptions = {
            placeholder: "روز / ماه / سال",
            twodigit: false,
            closeAfterSelect: true,
            nextButtonIcon: "fa fa-arrow-circle-right",
            previousButtonIcon: "fa fa-arrow-circle-left",
            buttonsColor: "#5867dd",
            markToday: true,
            sync: true,
            gotoToday: true
        }
        kamaDatepicker('starts_at', customOptions);
        kamaDatepicker('expires_at', customOptions);
    </script>
@endsection
