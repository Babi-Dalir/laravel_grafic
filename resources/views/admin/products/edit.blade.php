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
                        <div class="form-group row">
                            <label class="col-sm-2 col-form-label">دسته بندی</label>
                            <div class="col-sm-10">
                                <select name="category_id" class="form-select">
                                    @foreach($categories as $key => $value)
                                        @if($product->category_id == $key)
                                            <option selected value="{{$key}}">{{$value}}</option>
                                        @else
                                            <option value="{{$key}}">{{$value}}</option>
                                        @endif
                                    @endforeach
                                </select>
                            </div>
                        </div>
                        <div class="form-group row">
                            <label class="col-sm-2 col-form-label">قیمت اصلی</label>
                            <div class="col-sm-10">
                                <input type="text" class="form-control text-left" dir="rtl" name="main_price"
                                       value="{{$product->main_price}}">
                            </div>
                        </div>
                        <div class="form-group row">
                            <label class="col-sm-2 col-form-label">درصد تخفیف محصول</label>
                            <div class="col-sm-10">
                                <input type="text" class="form-control text-left" dir="rtl" name="discount"
                                       value="{{$product->discount}}">
                            </div>
                        </div>
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
                            <label class="col-sm-2 col-form-label">فایل اصلی محصول</label>
                            <div class="col-sm-10">
                                <input type="file" class="form-control-file" name="main_file">
                                @if($product->main_file)
                                    <small>فایل فعلی: {{ basename($product->main_file) }}</small>
                                @endif
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
                                <input type="text" id="spacial_start" class="text-left form-control" dir="rtl"
                                       name="spacial_start"
                                       value="{{$product->spacial_start==null ? null : \Hekmatinasser\Verta\Verta::instance($product->spacial_start)}}">
                            </div>
                        </div>
                        <div class="form-group row">
                            <label class="col-sm-2 col-form-label"> تاریخ انقضای شگفت انگیز</label>
                            <div class="col-sm-10">
                                <input type="text" id="spacial_expiration" class="text-left form-control" dir="rtl"
                                       name="spacial_expiration"
                                       value="{{$product->spacial_start==null ? null : \Hekmatinasser\Verta\Verta::instance($product->spacial_expiration)}}">
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
    </script>
@endsection
