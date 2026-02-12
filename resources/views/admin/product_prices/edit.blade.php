@extends('admin.layouts.master')
@section('content')
    <main class="main-content">
        @include('admin.layouts.error')
        <div class="card">
            <div class="card-body">
                <div class="container">
                    <h6 class="card-title">ویرایش تنوع قیمت محصول</h6>
                    <form method="POST" action="{{route('update.product.prices',[$product_price->id,$product->id])}}" enctype="multipart/form-data">
                        @csrf
                        @method('put  ')
                        <div class="form-group row">
                            <label  class="col-sm-2 col-form-label">قیمت اصلی محصول</label>
                            <div class="col-sm-10">
                                <input type="text" class="form-control text-left" dir="rtl" name="main_price" value="{{$product_price->main_price}}">
                            </div>
                        </div>
                        <div class="form-group row">
                            <label  class="col-sm-2 col-form-label">درصد تخفیف محصول</label>
                            <div class="col-sm-10">
                                <input type="text" class="form-control text-left" dir="rtl" name="discount" value="{{$product_price->discount}}">
                            </div>
                        </div>
                        <div class="form-group row">
                            <label  class="col-sm-2 col-form-label">تعداد محصول</label>
                            <div class="col-sm-10">
                                <input type="text" class="form-control text-left" dir="rtl" name="count" value="{{$product_price->count}}">
                            </div>
                        </div>
                        <div class="form-group row">
                            <label  class="col-sm-2 col-form-label">مقدار مجاز فروش محصول</label>
                            <div class="col-sm-10">
                                <input type="text" class="form-control text-left" dir="rtl" name="max_sell" value="{{$product_price->max_sell}}">
                            </div>
                        </div>
                        <div class="form-group row">
                            <div class="col-sm-10">
                                <input type="hidden" class="form-control text-left" dir="rtl" name="product_id" value="{{$product->id}}" >
                            </div>
                        </div>
                        <div class="form-group row">
                            <label  class="col-sm-2 col-form-label">رنگ محصول</label>
                            <div class="col-sm-10">
                                <select name="color_id" class="form-select">
                                    @foreach($colors as $key => $value)
                                        @if($product_price->color_id == $key)
                                            <option selected value="{{$key}}">{{$value}}</option>
                                        @else
                                            <option value="{{$key}}">{{$value}}</option>
                                        @endif
                                    @endforeach
                                </select>
                            </div>
                        </div>
                        <div class="form-group row">
                            <label  class="col-sm-2 col-form-label">گارانتی محصول</label>
                            <div class="col-sm-10">
                                <select name="guaranty_id" class="form-select">
                                    @foreach($guaranties as $key => $value)
                                        @if($product_price->guaranty_id == $key)
                                            <option selected value="{{$key}}">{{$value}}</option>
                                        @else
                                            <option value="{{$key}}">{{$value}}</option>
                                        @endif
                                    @endforeach
                                </select>
                            </div>
                        </div>
                        <div class="form-group row">
                            <label class="col-sm-2 col-form-label"> تاریخ شروع شگفت انگیز</label>

                            <div class="col-sm-10">
                                <input type="text" id="spacial_start" class="text-left form-control" dir="rtl"
                                       name="spacial_start" value="{{$product_price->spacial_start==null ? null : \Hekmatinasser\Verta\Verta::instance($product_price->spacial_start)}}">
                            </div>
                        </div>
                        <div class="form-group row">
                            <label class="col-sm-2 col-form-label"> تاریخ انقضای شگفت انگیز</label>

                            <div class="col-sm-10">
                                <input type="text" id="spacial_expiration" class="text-left form-control" dir="rtl"
                                       name="spacial_expiration" value="{{$product_price->spacial_expiration==null ? null : \Hekmatinasser\Verta\Verta::instance($product_price->spacial_expiration)}}">
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
    <script>
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

