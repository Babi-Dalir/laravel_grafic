<div class="container">
    <h6 class="card-title">ایجاد ویژگی‌های محصول: <strong class="text-info">{{ $product->name }}</strong></h6>

    @if (session()->has('message'))
        <div class="alert alert-success text-right">{{ session('message') }}</div>
    @endif

    <form wire:submit.prevent="submit" class="mb-4">
        <div class="form-group row">
            <div class="col-sm-3">
                <select wire:model="property_group_id" class="form-control @error('property_group_id') is-invalid @enderror">
                    <option value="">انتخاب گروه ویژگی</option>
                    @foreach($property_groups as $property_group)
                        <option value="{{ $property_group->id }}">{{ $property_group->name }}</option>
                    @endforeach
                </select>
                @error('property_group_id') <div class="invalid-feedback">{{ $message }}</div> @enderror
            </div>

            <div class="col-sm-6">
                <input type="text"
                       class="form-control text-right @error('name') is-invalid @enderror"
                       placeholder="مقدار ویژگی (مثلاً: 8 گیگابایت)"
                       wire:model="name">
                @error('name') <div class="invalid-feedback">{{ $message }}</div> @enderror
            </div>

            <div class="col-sm-3">
                <button type="submit" class="btn btn-success btn-block" wire:loading.attr="disabled">
                    <span wire:loading.remove><i class="ti-check-box m-r-5"></i> ذخیره</span>
                    <span wire:loading><i class="fa fa-spinner fa-spin"></i> در حال ثبت...</span>
                </button>
            </div>
        </div>
    </form>

    <table class="table table-striped table-hover border">
        <thead class="thead-light">
        <tr>
            <th class="text-center align-middle text-primary" style="width: 25%;">گروه ویژگی</th>
            <th class="text-center align-middle text-primary" style="width: 55%;">ویژگی محصول</th>
            <th class="text-center align-middle text-primary" style="width: 20%;">حذف کامل گروه</th>
        </tr>
        </thead>
        <tbody>
        @foreach($product_property_groups as $property_group)
            <tr>
                <td class="text-center align-middle font-weight-bold">{{ $property_group->name }}</td>
                <td class="text-center align-middle">
                    <ul class="list-group p-0 m-0">
                        {{-- واکشی کاملاً بهینه از کش حافظه (بدون کوئری تکراری در حلقه) --}}
                        @foreach($property_group->properties as $property)
                            <li class="list-group-item d-flex justify-content-between align-items-center mb-1 border rounded">
                                <span>{{ $property->name }}</span>
                                <i style="cursor: pointer;" class="ti-trash text-danger"
                                   wire:click="$dispatch('triggerDeleteProperty', { 'property_group_id': {{ $property_group->id }}, 'property_id': {{ $property->id }} })"></i>
                            </li>
                        @endforeach
                    </ul>
                </td>
                <td class="text-center align-middle">
                    <button class="btn btn-sm btn-outline-danger"
                            wire:click="$dispatch('triggerDeletePropertyGroup', { 'property_group_id': {{ $property_group->id }} })">
                        حذف گروهی
                    </button>
                </td>
            </tr>
        @endforeach
        </tbody>
    </table>

    {{-- گام بعدی هوشمند --}}
    @if($product->properties()->exists() && $product->propertyGroups()->exists())
        <div class="text-center mt-4">
            <a href="{{ route('seller.product.file.list', $product->id) }}" class="btn btn-primary btn-lg px-5">
                مرحله بعد: آپلود فایل‌ها ←
            </a>
        </div>
    @endif
</div>

@section('scripts')
    <script>
        // ساختار بهینه لایووایر ۳ برای ریجستر اتمیک اسکریپت‌ها
        document.addEventListener('livewire:init', () => {

            Livewire.on('triggerDeletePropertyGroup', (event) => {
                Swal.fire({
                    title: "آیا از حذف کامل این گروه و تمام ویژگی‌های زیرمجموعه آن مطمئن هستید؟",
                    icon: "warning",
                    showCancelButton: true,
                    confirmButtonColor: "#d33",
                    cancelButtonColor: "#3085d6",
                    confirmButtonText: "بله، حذف کن",
                    cancelButtonText: "خیر"
                }).then((result) => {
                    if (result.isConfirmed) {
                        Livewire.dispatch('destroy_seller_product_property_group', { property_group_id: event.property_group_id });
                    }
                });
            });

            Livewire.on('triggerDeleteProperty', (event) => {
                Swal.fire({
                    title: "آیا از حذف این ویژگی مطمئن هستید؟",
                    icon: "warning",
                    showCancelButton: true,
                    confirmButtonColor: "#d33",
                    cancelButtonColor: "#3085d6",
                    confirmButtonText: "بله",
                    cancelButtonText: "خیر"
                }).then((result) => {
                    if (result.isConfirmed) {
                        Livewire.dispatch('destroy_seller_product_property', {
                            property_group_id: event.property_group_id,
                            property_id: event.property_id
                        });
                    }
                });
            });

            Livewire.on('propertyDeletedSuccess', () => {
                Swal.fire({ title: "با موفقیت حذف شد!", icon: "success", timer: 1500, showConfirmButton: false });
            });
        });
    </script>
@endsection
