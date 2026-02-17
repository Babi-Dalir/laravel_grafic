<div class="navigation">
    <div class="navigation-icon-menu">
        <ul>
{{--            @hasanyrole('مدیر کاربران|مدیر کل')--}}
            <li data-toggle="tooltip" title="کاربران">
                <a href="#users" title=" کاربران">
                    <i class="icon ti-user"></i>
                </a>
            </li>
{{--            @endhasanyrole--}}
{{--            @hasanyrole('مدیر فروش|مدیر کل')--}}
            <li data-toggle="tooltip" title="فروشگاه">
                <a href="#store" title=" فروشگاه">
                    <i class="icon ti-folder"></i>
                </a>
            </li>
{{--            @endhasanyrole--}}
{{--            @hasanyrole('مدیر سفارشات|مدیر کل')--}}
            <li data-toggle="tooltip" title="سفارشات">
                <a href="#orders" title=" سفارشات">
                    <i class="icon ti-shopping-cart"></i>
                </a>
            </li>
{{--            @endhasanyrole--}}
{{--            @hasanyrole('فروشنده|مدیر کل')--}}
            <li data-toggle="tooltip" title="پنل فروشنده">
                <a href="#seller" title=" پنل فروشنده">
                    <i class="icon ti-panel"></i>
                </a>
            </li>
{{--            @endhasanyrole--}}
        </ul>
        <ul>
            <li data-toggle="tooltip" title="خروج">
                <a href="{{route('logout')}}" class="go-to-page">
                    <i class="icon ti-power-off"></i>
                </a>
            </li>
        </ul>
    </div>
    <div class="navigation-menu-body">
{{--        @hasanyrole('مدیر کاربران|مدیر کل')--}}
        <ul id="users">
            <li>
                <a href="#">کاربران</a>
                <ul>
                    <li><a href="{{route('users.create')}}">ایجاد کاربر</a></li>
                    <li><a href="{{route('users.index')}}">لیست کاربران</a></li>
                </ul>
            </li>
            <li>
                <a href="#">نقش ها</a>
                <ul>
                    <li><a href="{{route('roles.create')}}">ایجاد نقش</a></li>
                    <li><a href="{{route('roles.index')}}">لیست نقش ها</a></li>
                </ul>
            </li>
            <li>
                <a href="#">مجوز ها</a>
                <ul>
                    <li><a href="{{route('permissions.create')}}">ایجاد مجوز</a></li>
                    <li><a href="{{route('permissions.index')}}">لیست مجوز ها</a></li>
                </ul>
            </li>
            <li>
                <a href="#">فروشندگان</a>
                <ul>
                    <li><a href="{{route('seller.list')}}">لیست فروشندگان</a></li>
                </ul>
            </li>
        </ul>
{{--        @endhasanyrole--}}
{{--        @hasanyrole('مدیر فروش|مدیر کل')--}}
        <ul id="store">
            <li>
                <a href="#">کمیسیون ها</a>
                <ul>
                    <li><a href="{{route('categories.create')}}">ایجاد کمیسیون</a></li>
                    <li><a href="{{route('categories.index')}}">لیست کمیسیون ها</a></li>
                </ul>
            </li>
            <li>
                <a href="#">دسته بندی ها</a>
                <ul>
                    <li><a href="{{route('categories.create')}}">ایجاد دسته بندی</a></li>
                    <li><a href="{{route('categories.index')}}">لیست دسته بندی ها</a></li>
                </ul>
            </li>
            <li>
                <a href="#">بنر ها</a>
                <ul>
                    <li><a href="{{route('banners.create')}}">ایجاد بنر</a></li>
                    <li><a href="{{route('banners.index')}}">لیست بنر ها</a></li>
                </ul>
            </li>
            <li>
                <a href="#">اسلایدر ها</a>
                <ul>
                    <li><a href="{{route('sliders.create')}}">ایجاد اسلایدر</a></li>
                    <li><a href="{{route('sliders.index')}}">لیست اسلایدر ها</a></li>
                </ul>
            </li>
            <li>
                <a href="#">تگ ها</a>
                <ul>
                    <li><a href="{{route('tags.create')}}">ایجاد تگ</a></li>
                    <li><a href="{{route('tags.index')}}">لیست تگ ها</a></li>
                </ul>
            </li>
            <li>
                <a href="#">گروه ویژگی ها</a>
                <ul>
                    <li><a href="{{route('property_groups.create')}}">ایجاد گروه ویژگی</a></li>
                    <li><a href="{{route('property_groups.index')}}">لیست گروه ویژگی ها</a></li>
                </ul>
            </li>
            <li>
                <a href="#">محصولات</a>
                <ul>
                    <li><a href="{{route('products.create')}}">ایجاد محصول</a></li>
                    <li><a href="{{route('products.index')}}">لیست محصولات</a></li>
                </ul>
            </li>
            <li>
                <a href="#">نظرات</a>
                <ul>
                    <li><a href="{{route('users.comments')}}">لیست نظرات محصول</a></li>
                </ul>
            </li>
            <li>
                <a href="#">تخفیفات</a>
                <ul>
                    <li><a href="{{route('discounts.create')}}">ایجاد تخفیف</a></li>
                    <li><a href="{{route('discounts.index')}}">لیست تخفیفات</a></li>
                </ul>
            </li>
            <li>
                <a href="#">کارتهای هدیه</a>
                <ul>
                    <li><a href="{{route('gift_carts.create')}}">ایجاد کارت هدیه</a></li>
                    <li><a href="{{route('gift_carts.index')}}">لیست کارتهای هدیه</a></li>
                </ul>
            </li>
        </ul>
{{--        @endhasanyrole--}}
{{--        @hasanyrole('مدیر سفارشات|مدیر کل')--}}
        <ul id="orders">
            <li>
                <a href="#">سفارشات</a>
                <ul>
                    <li><a href="{{route('admin.orders.list')}}">لیست سفارشات</a></li>
                </ul>
            </li>
        </ul>
{{--        @endhasanyrole--}}
{{--        @hasanyrole('فروشنده|مدیر کل')--}}
        <ul id="seller">
            <li>
                <a href="#">محصولات</a>
                <ul>
                    <li><a href="{{route('products.index')}}">لیست همه محصولات</a></li>
                    <li><a href="{{route('seller.product.list')}}">لیست محصولات من</a></li>
                </ul>
            </li>
            <li>
                <a href="#">تراکنش ها</a>
                <ul>
                    <li><a href="{{route('seller.transaction.list')}}">لیست تراکنش ها</a></li>
                </ul>
            </li>
        </ul>
{{--        @endhasanyrole--}}
    </div>
</div>
