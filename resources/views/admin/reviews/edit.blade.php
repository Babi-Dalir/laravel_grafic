@extends('admin.layouts.master')
@section('content')
    <main class="main-content">
        @include('admin.layouts.error')
        <div class="card">
            <div class="card-body">
                <div class="container">
                    <h6 class="card-title">ویرایش نقد و بررسی</h6>
                    <form method="POST" action="{{route('update.product.reviews',[$review->id,$product_id])}}" enctype="multipart/form-data">
                        @csrf
                        @method('put  ')
                        <div class="form-group row">
                            <label  class="col-sm-2 col-form-label">عنوان نقد و بررسی</label>
                            <div class="col-sm-10">
                                <input type="text" class="form-control text-left" dir="rtl" name="name" value="{{$review->name}}">
                            </div>
                        </div>
                        <div class="form-group row">
                            <label  class="col-sm-2 col-form-label">توضیحات</label>
                            <div class="col-sm-10">
                                <textarea type="text" class="form-control text-left" dir="rtl" name="description" id="editor1" cols="30" rows="10">{{$review->description}}</textarea>
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
            dir:"rtl",
            dropdownAutoWidth:true,
            $dropdownParent:$('#parent')
        })
        $('.form-select').select2()
    </script>
@endsection
