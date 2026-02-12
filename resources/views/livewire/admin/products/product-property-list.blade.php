<div class="container">
    <h6 class="card-title"> ایجاد ویژگی های محصول {{$product->name}}</h6>
    <form wire:submit.prevent="submit">
        <div class="form-group row">
                <select wire:model="property_group_id" class="col-sm-3">
                    <option>انتخاب کنید</option>
                    @foreach($property_groups as $property_group)
                        <option value="{{$property_group->id}}">{{$property_group->name}}</option>
                    @endforeach
                </select>
            <div class="col-sm-6">
                <input type="text" class="form-control text-left" dir="rtl" wire:model="name">
            </div>
            <div class="col-sm-3">
                <button type="submit" class="btn btn-success btn-uppercase">
                    <i class="ti-check-box m-r-5"></i> ذخیره
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
                        @foreach($property_group->properties()->where('product_id',$product->id)->get() as $property)
                            <div class="row flex justify-content-center align-item-center">
                                <li class="list-group-item col-9">{{$property->name}}</li>
                                <i style="cursor: pointer;" class="ti-trash m-r-5 col-2" wire:click="$dispatch('deleteProductProperty',{'property_group_id':{{$property_group->id}},
                                'property_id':{{$property->id}}})"></i>
                            </div>
                        @endforeach
                    </ul>
                </td>
                <td class="text-center align-middle">
                    <a class="btn btn-outline-danger" wire:click="$dispatch('deleteProductPropertyGroup',{'property_group_id':{{$property_group->id}}})">
                        حذف
                    </a>
                </td>
            </tr>
        @endforeach
    </table>
</div>
@section('scripts')
    <script>
        Livewire.on('deleteProductPropertyGroup', (event) => {
            Swal.fire({
                title: "آیا از حذف مطمئن هستید؟",
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
                title: "آیا از حذف مطمئن هستید؟",
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
