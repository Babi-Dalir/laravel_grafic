<div class="table overflow-auto" tabindex="8">
    <div class="form-group row align-items-center">
        <label class="col-sm-2 col-form-label font-weight-bold">جستجوی دسته‌بندی:</label>
        <div class="col-sm-10">
            <input type="text"
                   class="form-control text-left"
                   dir="rtl"
                   wire:model.live.debounce.500ms="search"
                   placeholder="نام دسته‌بندی را تایپ کنید...">
        </div>
    </div>

    <table class="table table-striped table-hover mt-3">
        <thead class="thead-light">
        <tr>
            <th class="text-center align-middle text-primary">ردیف</th>
            <th class="text-center align-middle text-primary">درصد کمیسیون</th>
            <th class="text-center align-middle text-primary">دسته بندی</th>
            <th class="text-center align-middle text-primary">ویرایش</th>
            <th class="text-center align-middle text-primary">تاریخ ایجاد</th>
        </tr>
        </thead>
        <tbody>
        @forelse($commissions as $index => $commission)
            <tr>
                <td class="text-center align-middle">{{ $commissions->firstItem() + $index }}</td>
                <td class="text-center align-middle font-weight-bold text-success">{{ $commission->commission_percent }}%</td>
                <td class="text-center align-middle">{{ $commission->category?->name ?? 'فاقد دسته‌بندی' }}</td>
                <td class="text-center align-middle">
                    <a class="btn btn-sm btn-outline-info" href="{{ route('commissions.edit', $commission->id) }}">
                        <i class="fa fa-edit"></i> ویرایش
                    </a>
                </td>
                <td class="text-center align-middle">{{ verta($commission->created_at)->format('%d %B، %Y') }}</td>
            </tr>
        @empty
            <tr>
                <td colspan="5">
                    <div class="alert alert-warning text-center mb-0">
                        هیچ کمیسیونی برای این جستجو یافت نشد.
                    </div>
                </td>
            </tr>
        @endforelse
        </tbody>
    </table>

    <div class="pagination pagination-rounded pagination-sm d-flex justify-content-center mt-4">
        {{ $commissions->appends(Request::except('page'))->links() }}
    </div>
</div>
