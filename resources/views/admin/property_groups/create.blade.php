@extends('admin.layouts.master')
@section('content')
    <main class="main-content">
        @include('admin.layouts.error')
        <div class="card">
            <div class="card-body">
                <div class="container">
                    <h6 class="card-title">ایجاد گروه ویژگی</h6>
                    <form method="POST" action="{{route('property_groups.store')}}" enctype="multipart/form-data">
                        @csrf
                        <div class="form-group row mb-4">
                            <label class="col-sm-2 col-form-label">عنوان گروه ویژگی</label>
                            <div class="col-sm-10">
                                <input type="text" class="form-control text-right" dir="rtl" name="name" value="{{ old('name') }}">
                            </div>
                        </div>

                        <div class="form-group row mb-4">
                            <label class="col-sm-2 col-form-label">دسته بندی (لایه دوم)</label>
                            <div class="col-sm-10">
                                <select name="category_id" class="form-select text-right" dir="rtl">
                                    <option value="">-- انتخاب کنید --</option>

                                    @foreach($categories as $mainCategoryName => $subCategories)
                                        <optgroup label="📂 {{ $mainCategoryName }}">
                                            @foreach($subCategories as $subId => $subName)
                                                <option value="{{ $subId }}" {{ old('category_id') == $subId ? 'selected' : '' }}>
                                                    🔹 {{ $subName }}
                                                </option>
                                            @endforeach
                                        </optgroup>
                                    @endforeach

                                </select>
                            </div>
                        </div>

                        <div class="form-group row">
                            <div class="col-sm-10 offset-sm-2">
                                <button type="submit" class="btn btn-success btn-uppercase">
                                    <i class="ti-check-box m-r-5"></i> ذخیره
                                </button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </main>
@endsection
@section('scripts')
    <script src="{{url('panel/plugins/ckeditor/ckeditorConf.js')}}"></script>
    <script>
        // فعال‌سازی پلاگین select2 روی فیلد جدید
        $('.form-select').select2({
            dir: "rtl" // تنظیم راست‌چین شدن آپشن‌ها در Select2
        });
    </script>
@endsection
