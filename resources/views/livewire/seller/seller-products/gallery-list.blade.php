<div class="table overflow-auto" tabindex="8">
    <table class="table table-striped table-hover">
        <thead class="thead-light">
        <tr>
            <th class="text-center align-middle text-primary">ردیف</th>
            <th class="text-center align-middle text-primary">عکس</th>
            <th class="text-center align-middle text-primary">حذف</th>
            <th class="text-center align-middle text-primary">تاریخ ایجاد</th>
        </tr>
        </thead>
        <tbody>
        @foreach($galleries as $index=>$gallery)
            <tr>
                <td class="text-center align-middle">{{$galleries->firstItem()+$index}}</td>
                <td class="text-center align-middle">
                    <figure class="avatar avatar">
                        <img src="{{url('images/products/small/'.$gallery->image)}}" class="rounded-circle" alt="image">
                    </figure>
                </td>
                <td class="text-center align-middle">
                    <a class="btn btn-outline-danger"
                       wire:click="$dispatch('deleteSellerProductGallery',{'id':{{$gallery->id}}})">
                        حذف
                    </a>
                </td>
                <td class="text-center align-middle">{{\Hekmatinasser\Verta\Verta::instance($gallery->created_at)->format('%d%B، %Y')}}</td>

            </tr>
        @endforeach
    </table>
    @if($product->galleries()->exists() && $product->status !== \App\Enums\ProductStatus::Approved->value)
        <a href="{{ route('create.seller.product.properties', $product->id) }}"
           class="btn btn-success">
            ادامه تکمیل محصول →
        </a>
    @endif
    <div style="margin: 40px !important;"
         class="pagination pagination-rounded pagination-sm d-flex justify-content-center">
        {{$galleries->appends(Request::except('page'))->links()}}
    </div>

    <script>
        document.addEventListener('livewire:init', () => {
            Livewire.on('deleteSellerProductGallery', (event) => {
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
                        Livewire.dispatch('destroy_seller_product_gallery', {id: event.id})
                        Swal.fire({
                            title: "حذف با موفقیت انجام شد!",
                            icon: "success"
                        });
                    }
                });
            });
        });
    </script>
</div>


