<?php

namespace App\Livewire\Seller\SellerRequests;

use App\Enums\SellerRequestStatus;
use App\Enums\SellerStatus;
use App\Models\Seller;
use App\Models\SellerRequest;
use Illuminate\Support\Facades\DB;
use Livewire\Component;
use Livewire\WithPagination;

class SellerRequestList extends Component
{
    use WithPagination;

    protected $paginationTheme = 'bootstrap';

    public $search = '';
    public $sellerRequestId;
    public $admin_note = '';

    public function updatingSearch()
    {
        $this->resetPage();
    }

    /**
     * 🟢 تغییر وضعیت چرخشی کاملاً امن و اتمیک با کلیک روی وضعیت
     */
    public function changeStatus($id)
    {
        DB::transaction(function () use ($id) {
            $sellerRequest = SellerRequest::with('user')->findOrFail($id);
            $user = $sellerRequest->user;

            if (!$user) return;

            // تشخیص وضعیت بعدی (چرخه: در حال بررسی -> تایید شده -> رد شده -> در حال بررسی)
            if ($sellerRequest->status === SellerRequestStatus::Pending->value) {

                // ۱. انتقال به تایید شده
                $sellerRequest->update([
                    'status' => SellerRequestStatus::Approved->value,
                    'reviewed_at' => now(),
                    'admin_note' => null,
                ]);

                // ۲. تخصیص نقش
                $user->assignRole('فروشنده');

                // ۳. ساخت رکورد فروشنده
                if (!$user->seller) {
                    Seller::query()->create([
                        'user_id'    => $user->id,
                        'first_name' => $user->name,
                        'status'     => SellerStatus::Rejected->value,
                    ]);
                }

                session()->flash('message', "وضعیت درخواست به 'تایید شده' تغییر یافت و نقش فروشنده اعمال شد.");

            } elseif ($sellerRequest->status === SellerRequestStatus::Approved->value) {

                // ۱. انتقال به رد شده
                $sellerRequest->update([
                    'status' => SellerRequestStatus::Rejected->value,
                    'reviewed_at' => now(),
                    'admin_note' => 'تغییر وضعیت سریع توسط مدیریت',
                ]);

                // ۲. گرفتن نقش فروشنده (اختیاری - بر اساس بیزینس شما)
                if ($user->hasRole('فروشنده')) {
                    $user->removeRole('فروشنده');
                }

                session()->flash('message', "وضعیت درخواست به 'رد شده' تغییر یافت و نقش فروشنده لغو شد.");

            } else {

                // ۱. بازگشت به در حال بررسی
                $sellerRequest->update([
                    'status' => SellerRequestStatus::Pending->value,
                    'reviewed_at' => null,
                    'admin_note' => null,
                ]);

                // ۲. حذف نقش در صورت وجود
                if ($user->hasRole('فروشنده')) {
                    $user->removeRole('فروشنده');
                }

                session()->flash('message', "وضعیت درخواست به حالت 'در حال بررسی' بازگردانده شد.");
            }
        });
    }

    public function approveRequest($id)
    {
        $this->changeStatus($id); // از منطق یکپارچه بالا استفاده میکند
    }

    public function rejectRequest()
    {
        $this->validate([
            'admin_note' => 'required|string|min:3',
        ], [], ['admin_note' => 'دلیل رد درخواست']);

        $sellerRequest = SellerRequest::with('user')->findOrFail($this->sellerRequestId);
        $user = $sellerRequest->user;

        DB::transaction(function () use ($sellerRequest, $user) {
            $sellerRequest->update([
                'status' => SellerRequestStatus::Rejected->value,
                'admin_note' => $this->admin_note,
                'reviewed_at' => now(),
            ]);

            if ($user && $user->hasRole('فروشنده')) {
                $user->removeRole('فروشنده');
            }
        });

        $this->reset(['sellerRequestId', 'admin_note']);
        $this->dispatch('closeRejectModal');

        session()->flash('message', 'درخواست با موفقیت رد شد و دلیل آن ثبت گردید.');
    }

    public function render()
    {
        $query = SellerRequest::query()->with(['user.userProfile']);

        if (!empty(trim($this->search))) {
            $searchTerm = '%' . trim($this->search) . '%';
            $query->whereHas('user', function ($q) use ($searchTerm) {
                $q->where(function($innerQuery) use ($searchTerm) {
                    $innerQuery->where('name', 'like', $searchTerm)
                        ->orWhere('mobile', 'like', $searchTerm)
                        ->orWhere('email', 'like', $searchTerm);
                });
            });
        }

        $seller_requests = $query->latest()->paginate(10);

        return view('livewire.seller.seller-requests.seller-request-list', compact('seller_requests'));
    }
}
