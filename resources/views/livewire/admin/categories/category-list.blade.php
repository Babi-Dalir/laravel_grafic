<div class="table-responsive overflow-auto" tabindex="8" dir="rtl" style="text-align: right;">

    <div class="form-group row align-items-center mb-4">
        <div class="col-md-8 col-sm-12 d-flex align-items-center mb-3 mb-md-0">
            <label class="ml-3 my-0 font-weight-bold text-secondary" style="min-width: 140px;">جستجو (عنوان دسته‌بندی):</label>
            <div class="position-relative w-100 d-flex align-items-center">
                <input type="text" class="form-control text-left pr-3 pl-5" dir="rtl"
                       wire:model.live.debounce.500ms="search" placeholder="عبارت مورد نظر را تایپ کنید...">
                <div wire:loading class="spinner-border spinner-border-sm text-primary position-absolute" style="left: 15px;"></div>
            </div>
        </div>
        <div class="col-md-4 col-sm-12 text-left">
            <a href="{{ route('categories.trashed') }}" class="btn-trash-modern w-100 d-flex align-items-center justify-content-between p-2 shadow-sm">
                <div class="d-flex align-items-center">
                    <div class="icon-box ml-3">
                        <i class="ti-trash"></i>
                    </div>
                    <div class="text-content text-right">
                        <span class="title d-block font-weight-bold">دسته‌بندی‌های حذف شده</span>
                        <span class="count text-muted small">{{ \App\Models\Category::onlyTrashed()->count() }} مورد</span>
                    </div>
                </div>
                <div class="arrow-box">
                    <i class="ti-angle-left"></i>
                </div>
            </a>
        </div>
    </div>

    @if($search_categories)
        <div class="card card-search-lux mb-4 border-primary">
            <div class="card-header bg-primary text-white text-left py-3">
                <i class="ti-search ml-2"></i> نتایج فیلتر شده دسته‌بندی‌ها
            </div>
            <div class="table-responsive">
                <table class="table table-striped table-hover m-0 text-center align-middle bg-white">
                    <thead class="thead-light">
                    <tr>
                        <th class="text-center align-middle text-primary">عکس</th>
                        <th class="text-right align-middle text-primary pr-4">عنوان دسته‌بندی</th>
                        <th class="text-center align-middle text-primary">دسته پدر</th>
                        <th class="text-center align-middle text-primary">نام انگلیسی</th>
                        <th class="text-center align-middle text-primary">اسلاگ</th>
                        <th class="text-center align-middle text-primary">ویرایش</th>
                        <th class="text-center align-middle text-primary">حذف</th>
                        <th class="text-center align-middle text-primary">تاریخ ایجاد</th>
                    </tr>
                    </thead>
                    <tbody>
                    @forelse($search_categories as $category)
                        <tr>
                            <td class="text-center align-middle">
                                <figure class="avatar mb-0 m-auto">
                                    <img src="{{ $category->image ? url('images/categories/small/'.$category->image) : url('images/categories/default.png') }}" class="rounded-circle shadow-sm" style="width: 40px; height: 40px; object-fit: cover;" alt="image">
                                </figure>
                            </td>
                            <td class="text-right align-middle font-weight-bold pr-4">{{ $category->name }}</td>
                            <td class="text-center align-middle">
                                <span class="badge badge-secondary px-2 py-1">{{ $category->parentCategory->name ?? 'دسته اصلی' }}</span>
                            </td>
                            <td class="text-center align-middle text-muted">{{ $category->e_name }}</td>
                            <td class="text-center align-middle"><code>{{ $category->slug }}</code></td>
                            <td class="text-center align-middle">
                                <a class="btn btn-outline-info btn-sm btn-rounded-lux" href="{{ route('categories.edit', $category->id) }}">
                                    <i class="ti-pencil ml-1"></i> ویرایش
                                </a>
                            </td>
                            <td class="text-center align-middle">
                                <button class="btn btn-outline-danger btn-sm btn-rounded-lux" wire:click="$dispatch('deleteCategory', {'id': {{ $category->id }}})">
                                    <i class="ti-trash ml-1"></i> حذف
                                </button>
                            </td>
                            <td class="text-center align-middle text-secondary small">
                                {{ \Hekmatinasser\Verta\Verta::instance($category->created_at)->format('%d %B، %Y') }}
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="8" class="text-center py-5 bg-white">
                                <div class="empty-state py-4">
                                    <svg width="60" height="60" viewBox="0 0 24 24" fill="none" stroke="#dc3545" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" class="mb-3">
                                        <circle cx="11" cy="11" r="8"></circle>
                                        <line x1="21" y1="21" x2="16.65" y2="16.65"></line>
                                    </svg>
                                    <h5 class="text-dark font-weight-bold">نتیجه‌ای یافت نشد!</h5>
                                    <p class="text-muted">دسته‌بندی با عبارت <strong class="text-danger">"{{ $search }}"</strong> در سیستم ثبت نشده است.</p>
                                    <button wire:click="$set('search', '')" class="btn btn-primary btn-sm mt-2 px-3">
                                        <i class="ti-eraser ml-1"></i> پاکسازی جستجو
                                    </button>
                                </div>
                            </td>
                        </tr>
                    @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    @else
        <div class="accordion text-right" id="mainCategoryAccordion" dir="rtl">
            @forelse($categories as $category)
                <div class="card mb-3 shadow-xs border-lux">
                    <div class="card-header bg-light-lux p-0" id="heading-{{ $category->id }}">
                        <button class="btn btn-link accordion-lux-toggle text-right w-100 d-flex align-items-center justify-content-between p-3 collapsed"
                                type="button" data-toggle="collapse" data-target="#collapse-{{ $category->id }}" aria-expanded="false" aria-controls="collapse-{{ $category->id }}">
                            <span class="font-weight-bold text-dark d-flex align-items-center">
                                <i class="ti-folder ml-2 text-warning font-size-18"></i>
                                {{ $category->name }} <small class="text-muted mr-2">(دسته‌بندی اصلی)</small>
                            </span>
                            <i class="ti-angle-down arrow-toggle"></i>
                        </button>
                    </div>

                    <div id="collapse-{{ $category->id }}" class="collapse" aria-labelledby="heading-{{ $category->id }}" data-parent="#mainCategoryAccordion">
                        <div class="card-body bg-white p-3">

                            <div class="table-responsive mb-4">
                                <table class="table table-striped table-hover table-bordered m-0 text-center">
                                    <thead class="thead-light-lux">
                                    <tr>
                                        <th style="width: 80px;">عکس</th>
                                        <th>عنوان دسته‌بندی</th>
                                        <th>دسته پدر</th>
                                        <th>نام انگلیسی</th>
                                        <th>اسلاگ</th>
                                        <th style="width: 100px;">ویرایش</th>
                                        <th style="width: 100px;">حذف</th>
                                        <th>تاریخ ایجاد</th>
                                    </tr>
                                    </thead>
                                    <tbody>
                                    <tr>
                                        <td class="align-middle">
                                            <figure class="avatar mb-0 m-auto">
                                                <img src="{{ $category->image ? url('images/categories/small/'.$category->image) : url('images/categories/default.png') }}" class="rounded-circle" style="width: 38px; height: 38px; object-fit: cover;" alt="image">
                                            </figure>
                                        </td>
                                        <td class="align-middle font-weight-bold text-right pr-3">{{ $category->name }}</td>
                                        <td class="align-middle text-muted">{{ $category->parentCategory->name ?? 'ندارد (اصلی)' }}</td>
                                        <td class="align-middle">{{ $category->e_name }}</td>
                                        <td class="align-middle"><code>{{ $category->slug }}</code></td>
                                        <td class="align-middle">
                                            <a class="btn btn-outline-info btn-sm btn-rounded-lux" href="{{ route('categories.edit', $category->id) }}">ویرایش</a>
                                        </td>
                                        <td class="align-middle">
                                            <button class="btn btn-outline-danger btn-sm btn-rounded-lux" wire:click="$dispatch('deleteCategory', {'id': {{ $category->id }}})">حذف</button>
                                        </td>
                                        <td class="align-middle text-secondary small">{{ \Hekmatinasser\Verta\Verta::instance($category->created_at)->format('%d %B، %Y') }}</td>
                                    </tr>
                                    </tbody>
                                </table>
                            </div>

                            <div class="accordion mr-4 pr-3 border-right-lux" id="subCategoryAccordion-{{ $category->id }}">
                                @foreach($category->childCategory as $cat1)
                                    <div class="card mb-2 border-info shadow-none">
                                        <div class="card-header bg-soft-info p-0" id="heading-sub-{{ $cat1->id }}">
                                            <button class="btn btn-link text-info text-right w-100 d-flex align-items-center justify-content-between p-2 font-size-13 collapsed"
                                                    type="button" data-toggle="collapse" data-target="#collapse-sub-{{ $cat1->id }}" aria-expanded="false" aria-controls="collapse-sub-{{ $cat1->id }}">
                                                <span class="font-weight-bold">
                                                    <span class="text-muted ml-1">↳</span> {{ $cat1->name }}
                                                </span>
                                                <i class="ti-angle-down arrow-toggle"></i>
                                            </button>
                                        </div>

                                        <div id="collapse-sub-{{ $cat1->id }}" class="collapse" aria-labelledby="heading-sub-{{ $cat1->id }}" data-parent="#subCategoryAccordion-{{ $category->id }}">
                                            <div class="card-body p-3 bg-white">
                                                <div class="table-responsive mb-3">
                                                    <table class="table table-striped table-hover table-bordered m-0 text-center">
                                                        <thead class="thead-light-lux">
                                                        <tr>
                                                            <th style="width: 80px;">عکس</th>
                                                            <th>عنوان دسته‌بندی</th>
                                                            <th>دسته پدر</th>
                                                            <th>نام انگلیسی</th>
                                                            <th>اسلاگ</th>
                                                            <th style="width: 90px;">ویرایش</th>
                                                            <th style="width: 90px;">حذف</th>
                                                            <th>تاریخ ایجاد</th>
                                                        </tr>
                                                        </thead>
                                                        <tbody>
                                                        <tr>
                                                            <td class="align-middle">
                                                                <figure class="avatar mb-0 m-auto">
                                                                    <img src="{{ $cat1->image ? url('images/categories/small/'.$cat1->image) : url('images/categories/default.png') }}" class="rounded-circle" style="width: 35px; height: 35px; object-fit: cover;" alt="image">
                                                                </figure>
                                                            </td>
                                                            <td class="align-middle font-weight-bold text-right pr-3">{{ $cat1->name }}</td>
                                                            <td class="align-middle text-info font-weight-bold">{{ $cat1->parentCategory->name ?? '' }}</td>
                                                            <td class="align-middle text-muted">{{ $cat1->e_name }}</td>
                                                            <td class="align-middle"><code>{{ $cat1->slug }}</code></td>
                                                            <td class="align-middle">
                                                                <a class="btn btn-outline-info btn-sm btn-rounded-lux" href="{{ route('categories.edit', $cat1->id) }}">ویرایش</a>
                                                            </td>
                                                            <td class="align-middle">
                                                                <button class="btn btn-outline-danger btn-sm btn-rounded-lux" wire:click="$dispatch('deleteCategory', {'id': {{ $cat1->id }}})">حذف</button>
                                                            </td>
                                                            <td class="align-middle text-secondary small">{{ \Hekmatinasser\Verta\Verta::instance($cat1->created_at)->format('%d %B، %Y') }}</td>
                                                        </tr>
                                                        </tbody>
                                                    </table>
                                                </div>

                                                <div class="w-100 mr-4 pr-3 border-right-dashed" id="childCategoryAccordion-{{ $cat1->id }}">
                                                    @foreach($cat1->childCategory as $cat2)
                                                        <div class="card mb-2 border-light shadow-none">
                                                            <div class="card-header bg-light py-1 px-2" id="heading-child-{{ $cat2->id }}">
                                                                <button class="btn btn-link btn-sm text-dark text-right w-100 d-flex align-items-center justify-content-between collapsed"
                                                                        type="button" data-toggle="collapse" data-target="#collapse-child-{{ $cat2->id }}" aria-expanded="false" aria-controls="collapse-child-{{ $cat2->id }}">
                                                                    <span class="font-size-12"><i class="ti-line-double text-muted ml-2"></i> {{ $cat2->name }}</span>
                                                                    <i class="ti-angle-down arrow-toggle font-size-10"></i>
                                                                </button>
                                                            </div>
                                                            <div id="collapse-child-{{ $cat2->id }}" class="collapse" aria-labelledby="heading-child-{{ $cat2->id }}" data-parent="#childCategoryAccordion-{{ $cat1->id }}">
                                                                <div class="card-body p-2 bg-light">
                                                                    <table class="table table-sm table-striped table-hover bg-white mb-0 text-center">
                                                                        <thead class="thead-light-lux">
                                                                        <tr>
                                                                            <th style="width: 70px;">عکس</th>
                                                                            <th>عنوان دسته‌بندی</th>
                                                                            <th>دسته پدر</th>
                                                                            <th>نام انگلیسی</th>
                                                                            <th>اسلاگ</th>
                                                                            <th style="width: 80px;">ویرایش</th>
                                                                            <th style="width: 80px;">حذف</th>
                                                                            <th>تاریخ ایجاد</th>
                                                                        </tr>
                                                                        </thead>
                                                                        <tbody>
                                                                        <tr>
                                                                            <td class="align-middle">
                                                                                <figure class="avatar mb-0 m-auto">
                                                                                    <img src="{{ $cat2->image ? url('images/categories/small/'.$cat2->image) : url('images/categories/default.png') }}" class="rounded-circle" style="width: 30px; height: 30px; object-fit: cover;" alt="image">
                                                                                </figure>
                                                                            </td>
                                                                            <td class="align-middle text-right pr-3 font-weight-bold">{{ $cat2->name }}</td>
                                                                            <td class="align-middle text-secondary">{{ $cat2->parentCategory->name ?? '' }}</td>
                                                                            <td class="align-middle text-muted small">{{ $cat2->e_name }}</td>
                                                                            <td class="align-middle"><code>{{ $cat2->slug }}</code></td>
                                                                            <td class="align-middle">
                                                                                <a class="btn btn-outline-info btn-xs btn-rounded-lux" href="{{ route('categories.edit', $cat2->id) }}">ویرایش</a>
                                                                            </td>
                                                                            <td class="align-middle">
                                                                                <button class="btn btn-outline-danger btn-xs btn-rounded-lux" wire:click="$dispatch('deleteCategory', {'id': {{ $cat2->id }}})">حذف</button>
                                                                            </td>
                                                                            <td class="align-middle text-secondary small">{{ \Hekmatinasser\Verta\Verta::instance($cat2->created_at)->format('%d %B، %Y') }}</td>
                                                                        </tr>
                                                                        </tbody>
                                                                    </table>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    @endforeach
                                                </div>

                                            </div>
                                        </div>
                                    </div>
                                @endforeach
                            </div>

                        </div>
                    </div>
                </div>
            @empty
                <div class="text-center py-5 w-100 shadow-sm border rounded bg-light">
                    <div class="empty-state">
                        <svg width="60" height="60" viewBox="0 0 24 24" fill="none" stroke="#6c757d" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" class="mb-3">
                            <circle cx="11" cy="11" r="8"></circle>
                            <line x1="21" y1="21" x2="16.65" y2="16.65"></line>
                        </svg>
                        <h5 class="text-dark font-weight-bold">هیچ دسته‌بندی یافت نشد!</h5>
                        <p class="text-muted mb-0">در حال حاضر هیچ داده‌ای برای نمایش وجود ندارد.</p>
                    </div>
                </div>
            @endforelse
        </div>
    @endif
</div>

@section('scripts')
    <script>
        document.addEventListener('livewire:init', () => {
            Livewire.on('deleteCategory', (event) => {
                const id = event.id ? event.id : (event[0] ? event[0].id : null);

                Swal.fire({
                    title: "آیا از حذف مطمئن هستید؟",
                    text: "با حذف این دسته، تمامی زیرمجموعه‌های آن نیز حذف خواهند شد!",
                    icon: "warning",
                    showCancelButton: true,
                    confirmButtonColor: "#dc3545",
                    cancelButtonColor: "#6c757d",
                    confirmButtonText: "بله، حذف شود",
                    cancelButtonText: "خیر، انصراف",
                }).then((result) => {
                    if (result.isConfirmed) {
                        Livewire.dispatch('destroy_category', {id: id});
                        Swal.fire({
                            title: "حذف با موفقیت انجام شد!",
                            icon: "success",
                            confirmButtonText: "تایید"
                        });
                    }
                });
            });
        });
    </script>
@endsection
