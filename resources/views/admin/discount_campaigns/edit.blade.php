@extends('admin.layouts.master')
@section('content')
    <main class="main-content">
        @include('admin.layouts.error')
        <div class="card">
            <div class="card-body">
                <div class="container">
                    <h6 class="card-title">ویرایش کمپین: {{ $discount_campaign->name }}</h6>
                    <form method="POST" action="{{ route('discount_campaigns.update', $discount_campaign->id) }}">
                        @csrf
                        @method('PUT')

                        {{-- نام کمپین --}}
                        <div class="form-group row">
                            <label class="col-sm-2 col-form-label">نام کمپین</label>
                            <div class="col-sm-10">
                                <input type="text" class="form-control text-left" dir="rtl" name="name" value="{{ $discount_campaign->name }}">
                            </div>
                        </div>

                        {{-- نوع تخفیف --}}
                        <div class="form-group row">
                            <label class="col-sm-2 col-form-label">نوع تخفیف</label>
                            <div class="col-sm-10">
                                <select name="type" id="campaign_type" class="form-select select2-single">
                                    <option @if($discount_campaign->type == \App\Enums\DiscountCampaignType::Product->value) selected @endif value="{{\App\Enums\DiscountCampaignType::Product->value}}">محصول خاص</option>

                                    <option @if($discount_campaign->type == \App\Enums\DiscountCampaignType::Category->value) selected @endif value="{{\App\Enums\DiscountCampaignType::Category->value}}">دسته بندی</option>

                                    <option @if($discount_campaign->type == \App\Enums\DiscountCampaignType::Global->value) selected @endif value="{{\App\Enums\DiscountCampaignType::Global->value}}">کل سایت</option>
                                </select>
                            </div>
                        </div>

                        {{-- انتخاب محصولات --}}
                        {{-- بخش انتخاب محصولات --}}
                        <div id="product_selection" class="form-group row" style="{{ $discount_campaign->type == \App\Enums\DiscountCampaignType::Product->value ? '' : 'display:none' }}">
                            <label class="col-sm-2 col-form-label">انتخاب محصولات</label>
                            <div class="col-sm-10">
                                <select name="target_ids[]" class="form-control select2-multiple" multiple>
                                    @foreach($products as $key => $value)
                                        <option value="{{$key}}" {{ in_array($key, $selectedTargets) ? 'selected' : '' }}>
                                            {{$value}}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                        </div>

                        {{-- انتخاب دسته‌بندی‌ها --}}
                        <div id="category_selection" class="form-group row" style="{{ $discount_campaign->type == \App\Enums\DiscountCampaignType::Category->value ? '' : 'display:none' }}">
                            <label class="col-sm-2 col-form-label">انتخاب دسته‌بندی‌ها</label>
                            <div class="col-sm-10">
                                <select name="target_ids[]" class="form-control select2-multiple" multiple>
                                    @foreach($categories as $mainCategory)
                                        <optgroup label="{{ $mainCategory->name }}">
                                            @foreach($mainCategory->childCategory as $subCategory)
                                                <option value="{{ $subCategory->id }}" {{ in_array($subCategory->id, $selectedTargets) ? 'selected' : '' }}>
                                                    - {{ $subCategory->name }}
                                                </option>

                                                @foreach($subCategory->childCategory as $childCategory)
                                                    <option value="{{ $childCategory->id }}" {{ in_array($childCategory->id, $selectedTargets) ? 'selected' : '' }}>
                                                        -- {{ $childCategory->name }}
                                                    </option>
                                                @endforeach
                                            @endforeach
                                        </optgroup>
                                    @endforeach
                                </select>
                            </div>
                        </div>

                        {{-- درصد تخفیف --}}
                        <div class="form-group row">
                            <label class="col-sm-2 col-form-label">درصد تخفیف</label>
                            <div class="col-sm-10">
                                <input type="text" class="form-control text-left" dir="rtl" name="percent" value="{{ $discount_campaign->percent }}">
                            </div>
                        </div>

                        {{-- تاریخ شروع --}}
                        <div class="form-group row">
                            <label class="col-sm-2 col-form-label"> تاریخ شروع </label>
                            <div class="col-sm-10">
                                <input type="text" id="starts_at" class="text-left form-control" dir="rtl" name="starts_at" value="{{ $discount_campaign->starts_at ? Verta::instance($discount_campaign->starts_at)->format('Y/m/d') : '' }}">
                            </div>
                        </div>

                        {{-- تاریخ انقضا --}}
                        <div class="form-group row">
                            <label class="col-sm-2 col-form-label"> تاریخ انقضا </label>
                            <div class="col-sm-10">
                                <input type="text" id="expires_at" class="text-left form-control" dir="rtl" name="expires_at" value="{{ $discount_campaign->expires_at ? Verta::instance($discount_campaign->expires_at)->format('Y/m/d') : '' }}">
                            </div>
                        </div>

                        <div class="form-group row">
                            <button type="submit" class="btn btn-primary btn-uppercase">
                                <i class="ti-reload m-r-5"></i> بروزرسانی کمپین
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
        $('.select2-single').select2({ dir: "rtl", width: '100%' });
        $('.select2-multiple').select2({ dir: "rtl", width: '100%', placeholder: "جستجو و انتخاب کنید..." });

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

        var customOptions = { placeholder: "روز / ماه / سال", sync: true, gotoToday: true }
        kamaDatepicker('starts_at', customOptions);
        kamaDatepicker('expires_at', customOptions);
    </script>
@endsection
