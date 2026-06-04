<div class="dt-sl">
    <div class="table-responsive">

        <table class="table table-order text-center align-middle">

            <thead>
            <tr>
                <th>ردیف</th>
                <th>محصول</th>
                <th>تاریخ خرید</th>
                <th>تعداد دانلود</th>
                <th>وضعیت</th>
                <th>دانلود</th>
            </tr>
            </thead>

            <tbody>

            @forelse($downloads as $index => $download)

                <tr>

                    <td>
                        {{ $downloads->firstItem() + $index }}
                    </td>

                    <td>
                        {{ $download->product->name }}
                    </td>

                    <td>
                        {{ verta($download->created_at)->format('Y/m/d') }}
                    </td>

                    <td>
                        {{ $download->download_count }}
                        /
                        {{ $download->max_download }}
                    </td>

                    <td>

                        @if($download->download_count >= $download->max_download)

                            <span class="badge badge-danger">
                                                            سقف دانلود تکمیل شده
                                                        </span>

                        @else

                            <span class="badge badge-success">
                                                            قابل دانلود
                                                        </span>

                        @endif

                    </td>

                    <td>

                        @if($download->download_count < $download->max_download)

                            <a href="{{ route('download.file', $download->token) }}"
                               class="btn btn-success">
                                دانلود فایل
                            </a>

                        @else

                            <button class="btn btn-secondary btn-sm" disabled>
                                دانلود غیرفعال
                            </button>

                        @endif

                    </td>

                </tr>

            @empty

                <tr>
                    <td colspan="6">
                        <div class="alert alert-warning mb-0">
                            هنوز محصولی خریداری نکرده‌اید.
                        </div>
                    </td>
                </tr>

            @endforelse

            </tbody>

        </table>

    </div>

    <div class="mt-3">
        {{ $downloads->links('vendor.pagination.profile-pagination.profile_downloads') }}
    </div>

</div>
