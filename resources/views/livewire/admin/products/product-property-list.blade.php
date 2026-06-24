<div class="container">
    <h6 class="card-title"> ایجاد ویژگی های محصول {{$product->name}}</h6>

    {{-- پیام موفقیت --}}
    @if(session()->has('message'))
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            {{ session('message') }}
        </div>
    @endif

    {{-- 🟢 نمایش پیام خطا برای جلوگیری از مقدار تکراری --}}
    @if(session()->has('error'))
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
            {{ session('error') }}
        </div>
    @endif

    <form wire:submit.prevent="submit">
        <div class="form-group row">
            <div class="col-sm-3">
                <select wire:model="property_group_id" class="form-control @error('property_group_id') is-invalid @enderror">
                    <option value="">انتخاب گروه ویژگی</option>
                    @foreach($property_groups as $property_group)
                        <option value="{{$property_group->id}}">{{$property_group->name}}</option>
                    @endforeach
                </select>
                @error('property_group_id')
                <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>

            <div class="col-sm-6">
                <input type="text"
                       class="form-control text-right @error('name') is-invalid @enderror"
                       placeholder="مقدار ویژگی (مثلاً: 8 گیگابایت)"
                       wire:model="name">
                @error('name')
                <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>

            <div class="col-sm-3">
                <button type="submit" class="btn btn-success btn-block" wire:loading.attr="disabled">
                    <span wire:loading.remove><i class="ti-check-box m-r-5"></i> ذخیره</span>
                    <span wire:loading><i class="fa fa-spinner fa-spin"></i> در حال ثبت...</span>
                </button>
            </div>
        </div>
    </form>

    <table class="table table-striped table-hover">
        <thead class="thead-light">
        <tr>
            <th class="text-center align-middle text-primary">گروه ویژگی</th>
            <th class="text-center align-middle text-primary">ویژگی محصول</th>
            <th class="text-center align-middle text-primary">حذف</th>
        </tr>
        </thead>
        <tbody>
        @foreach($product_property_groups as $property_group)
            <tr>
                <td class="text-center align-middle">{{$property_group->name}}</td>
                <td class="text-center align-middle">
                    <ul class="list-group">
                        {{-- 🟢 بهینه‌سازی آنلاین: استفاده از ریلیشن لود شده در کامپوننت بدون صدا زدن پرانتز متد یا اجرای کدهای دیتابیس در نمایش --}}
                        @foreach($property_group->properties as $property)
                            <div class="row flex justify-content-center align-items-center mb-1">
                                <li class="list-group-item col-9 text-right">{{$property->name}}</li>
                                <div class="col-2 text-center">
                                    <i style="cursor: pointer;" class="ti-trash text-danger" wire:click="$dispatch('deleteProductProperty',{'property_group_id':{{$property_group->id}}, 'property_id':{{$property->id}}})"></i>
                                </div>
                            </div>
                        @endforeach
                    </ul>
                </td>
                <td class="text-center align-middle">
                    <a class="btn btn-outline-danger" wire:click="$dispatch('deleteProductPropertyGroup',{'property_group_id':{{$property_group->id}}})">
                        حذف فله‌ای
                    </a>
                </td>
            </tr>
        @endforeach
        </tbody>
    </table>

    {{-- 🟢 بهینه‌سازی آنلاین: استفاده از دیتای لود شده برای دکمه فلو کنترل به جای زدن چند کوئری exists مجزا به دیتابیس سرور --}}
    @if($product_property_groups->isNotEmpty())
        <div class="text-center mt-4">
            <a href="{{ route('product.file.list', $product->id) }}"
               class="btn btn-primary btn-lg">
                مرحله بعد: آپلود فایل‌ها →
            </a>
        </div>
    @endif
</div>

@section('scripts')
    <script>
        Livewire.on('deleteProductPropertyGroup', (event) => {
            Swal.fire({
                title: "آیا از حذف گروه و تمام ویژگی‌های آن مطمئن هستید؟",
                icon: "warning",
                showCancelButton: true,
                confirmButtonColor: "#3085d6",
                cancelButtonColor: "#d33",
                confirmButtonText: "بله",
                cancelButtonText: "خیر",
            }).then((result) => {
                if (result.isConfirmed) {
                    Livewire.dispatch('destroy_product_property_group',{property_group_id : event.property_group_id})
                    Swal.fire({
                        title: "حذف با موفقیت انجام شد!",
                        icon: "success"
                    });
                }
            });
        })
    </script>
    <script>
        Livewire.on('deleteProductProperty', (event) => {
            Swal.fire({
                title: "آیا از حذف این ویژگی مطمئن هستید؟",
                icon: "warning",
                showCancelButton: true,
                confirmButtonColor: "#3085d6",
                cancelButtonColor: "#d33",
                confirmButtonText: "بله",
                cancelButtonText: "خیر",
            }).then((result) => {
                if (result.isConfirmed) {
                    Livewire.dispatch('destroy_product_property',{
                        property_group_id : event.property_group_id,
                        property_id : event.property_id
                    })
                    Swal.fire({
                        title: "حذف با موفقیت انجام شد!",
                        icon: "success"
                    });
                }
            });
        })
    </script>
@endsection
