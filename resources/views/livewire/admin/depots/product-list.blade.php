<div>
    <div class="table overflow-auto" tabindex="8">
        <div class="form-group row">
            <label class="col-sm-2 col-form-label">عنوان جستجو در انبار</label>
            <div class="col-sm-10">
                <input type="text" @keyup.enter="$wire.searchData" class="form-control text-left" dir="rtl" wire:model="search_depot">
            </div>
        </div>
        <div class="row">
            @if(session()->has('messageAdd'))
                <div class="alert alert-success">
                    <div>{{session('messageAdd')}}</div>
                </div>
            @endif
        </div>
        <div class="row">
            @if(session()->has('messageDelete'))
                <div class="alert alert-danger">
                    <div>{{session('messageDelete')}}</div>
                </div>
            @endif
        </div>
        <table class="table table-striped table-hover">
            <thead class="thead-light">
            <tr>
                <th class="text-center align-middle text-primary">ردیف</th>
                <th class="text-center align-middle text-primary">نام محصول</th>
                <th class="text-center align-middle text-primary">گارانتی</th>
                <th class="text-center align-middle text-primary">تعداد</th>
                <th class="text-center align-middle text-primary">رنگ ها</th>
                <th class="text-center align-middle text-primary">حذف از انبار</th>
                <th class="text-center align-middle text-primary">ورود یا خروج</th>
                <th class="text-center align-middle text-primary">تاریخ ایجاد</th>
            </tr>
            </thead>
            <tbody>
            @foreach($depot_products as $index=>$depot_product)
                <tr>
                    <td class="text-center align-middle">{{$depot_products->firstItem()+$index}}</td>
                    <td class="text-center align-middle">{{$depot_product->productPrice->product->name}}</td>
                    <td class="text-center align-middle">{{$depot_product->productPrice->guaranty->name}}</td>
                    <td class="text-center align-middle">{{$depot_product->count}}</td>
                    <td class="text-center align-middle">{{$depot_product->productPrice->color->name}}</td>
                    <td class="text-center align-middle">
                        <a class="btn btn-outline-danger" wire:click="deleteDepot({{$depot_product->id}})">
                            حذف از انبار
                        </a>
                    </td>
                    <td class="text-center align-middle" wire:click="$dispatch('addOrOutDepot',{'product_price_id':{{$depot_product->productPrice->id}},'depot_id':{{$depot_id}}})">
                        <a class="btn btn-outline-success" data-toggle="modal" data-target="#ModalCenter">
                            ورود <=> خروج => محصول
                        </a>
                    </td>
                    <td class="text-center align-middle">{{\Hekmatinasser\Verta\Verta::instance($depot_product->created_at)->format('%d%B، %Y')}}</td>
                </tr>
            @endforeach
        </table>
        <div style="margin: 40px !important;"
             class="pagination pagination-rounded pagination-sm d-flex justify-content-center">
            {{$depot_products->appends(Request::except('page'))->links()}}
        </div>
    </div>
    <div class="table overflow-auto" tabindex="8">
        <div class="form-group row">
            <label class="col-sm-2 col-form-label">عنوان جستجو</label>
            <div class="col-sm-10">
                <input type="text" @keyup.enter="$wire.searchData" class="form-control text-left" dir="rtl" wire:model="search">
            </div>
        </div>
        <table class="table table-striped table-hover">
            <thead class="thead-light">
            <tr>
                <th class="text-center align-middle text-primary">ردیف</th>
                <th class="text-center align-middle text-primary">نام محصول</th>
                <th class="text-center align-middle text-primary">گارانتی</th>
                <th class="text-center align-middle text-primary">تعداد</th>
                <th class="text-center align-middle text-primary">رنگ ها</th>
                <th class="text-center align-middle text-primary">افزودن به انبار</th>
                <th class="text-center align-middle text-primary">تاریخ ایجاد</th>
            </tr>
            </thead>
            <tbody>
            @foreach($product_prices as $index=>$product_price)
                <tr>
                    <td class="text-center align-middle">{{$product_prices->firstItem()+$index}}</td>
                    <td class="text-center align-middle">{{$product_price->product->name}}</td>
                    <td class="text-center align-middle">{{$product_price->guaranty->name}}</td>
                    <td class="text-center align-middle">{{$product_price->count}}</td>
                    <td class="text-center align-middle">{{$product_price->color->name}}</td>
                    <td class="text-center align-middle" wire:click="addDepot({{$product_price->id}},{{$product_price->count}},{{$depot_id}})">
                        <a class="btn btn-outline-info">
                            افزودن به انبار
                        </a>
                    </td>
                    <td class="text-center align-middle">{{\Hekmatinasser\Verta\Verta::instance($product_price->created_at)->format('%d%B، %Y')}}</td>
                </tr>
            @endforeach
        </table>
        <div style="margin: 40px !important;"
             class="pagination pagination-rounded pagination-sm d-flex justify-content-center">
            {{$product_prices->appends(Request::except('page'))->links()}}
        </div>
    </div>

    <livewire:admin.depots.add-or-out-depot-modal/>

</div>

