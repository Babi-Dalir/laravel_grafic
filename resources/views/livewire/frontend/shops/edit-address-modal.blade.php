<div class="modal fade" id="modal-location-edit" role="dialog" wire:ignore.self aria-labelledby="exampleModalCenterTitle"
    aria-hidden="true">
    <div class="modal-dialog modal-lg send-info modal-dialog-centered" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="exampleModalCenterTitle">
                    <i class="now-ui-icons location_pin"></i>
                    ویرایش آدرس
                </h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <div class="row">
                    <div class="col-lg-12 col-md-12">
                        <div class="form-ui dt-sl">
                            <form class="form-account" wire:submit.prevent="submit">
                                <div class="row">
                                    <div class="col-md-6 col-sm-12 mb-2">
                                        <div class="form-row-title">
                                            <h4>
                                                نام و نام خانوادگی
                                            </h4>
                                        </div>
                                        <div class="form-row">
                                            <input class="input-ui pr-2 text-right" wire:model="name"
                                                value="{{ $name }}" type="text"
                                                placeholder="نام خود را وارد نمایید">
                                            @error('name')
                                                <div class="alert alert-danger">{{ $message }}</div>
                                            @enderror
                                        </div>
                                    </div>
                                    <div class="col-md-6 col-sm-12 mb-2">
                                        <div class="form-row-title">
                                            <h4>
                                                شماره موبایل
                                            </h4>
                                        </div>
                                        <div class="form-row">
                                            <input value="{{ $mobile }}" wire:model="mobile"
                                                class="input-ui pl-2 dir-ltr text-left" type="text"
                                                placeholder="09xxxxxxxxx">
                                            @error('mobile')
                                                <div class="alert alert-danger">{{ $message }}</div>
                                            @enderror
                                        </div>
                                    </div>
                                    <div class="col-md-6 col-sm-12 mb-2">
                                        <div class="form-row-title">
                                            <h4>
                                                استان
                                            </h4>
                                        </div>
                                        <div class="form-row">
                                            <div class="form-group" style="width: 100% !important;">
                                                <select class="form-control" style="width: 100% !important;"
                                                    wire:model="province"
                                                    wire:change="changeProvince($event.target.value)">

                                                    @foreach ($provinces as $key => $value)
                                                        @if ($key == $province)
                                                            <option selected value="{{ $key }}">
                                                                {{ $value }}
                                                            </option>
                                                        @else
                                                            <option value="{{ $key }}">
                                                                {{ $value }}
                                                            </option>
                                                        @endif
                                                    @endforeach

                                                </select>
                                                @error('province')
                                                    <div class="alert alert-danger">{{ $message }}</div>
                                                @enderror
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-md-6 col-sm-12 mb-2">
                                        <div class="form-row-title">
                                            <h4>
                                                شهر
                                            </h4>
                                        </div>
                                        <div class="form-row">
                                            <div class="form-group" style="width: 100% !important;">
                                                <select class="form-control" style="width: 100% !important;"
                                                    wire:model="city">

                                                    @foreach ($cities as $key => $value)
                                                        @if ($key == $city)
                                                            <option selected value="{{ $key }}">
                                                                {{ $value }}
                                                            </option>
                                                        @else
                                                            <option value="{{ $key }}">
                                                                {{ $value }}
                                                            </option>
                                                        @endif
                                                    @endforeach
                                                </select>
                                                @error('city')
                                                    <div class="alert alert-danger">{{ $message }}</div>
                                                @enderror
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-12 mb-2">
                                        <div class="form-row-title">
                                            <h4>
                                                آدرس پستی
                                            </h4>
                                        </div>
                                        <div class="form-row">
                                            <textarea wire:model="address" class="input-ui pr-2 text-right" placeholder=" آدرس تحویل گیرنده را وارد نمایید">{{ $address }}</textarea>
                                            @error('address')
                                                <div class="alert alert-danger">{{ $message }}</div>
                                            @enderror
                                        </div>
                                    </div>
                                    <div class="col-12 mb-2">
                                        <div class="form-row-title">
                                            <h4>
                                                کد پستی
                                            </h4>
                                        </div>
                                        <div class="form-row">
                                            <input value="{{ $postal_code }}" wire:model="postal_code"
                                                class="input-ui pl-2 dir-ltr text-left placeholder-right" type="text"
                                                placeholder=" کد پستی را بدون خط تیره بنویسید">
                                            @error('postal_code')
                                                <div class="alert alert-danger">{{ $message }}</div>
                                            @enderror
                                        </div>
                                    </div>
                                    <div class="col-12 pr-4 pl-4">
                                        <button type="submit" class="btn btn-sm btn-primary btn-submit-form">ثبت
                                            و
                                            ارسال به این آدرس</button>
                                        <button type="button" data-dismiss="modal" aria-label="Close"
                                            class="btn-link-border float-left mt-2">انصراف
                                            و بازگشت</button>
                                    </div>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@push('scripts')
    <script>
        window.addEventListener('closeEditAddressModal', event => {
            $("#modal-location-edit").modal('toggle')
        })
    </script>
@endpush
