<?php

namespace App\Livewire\Admin\DiscountCampaigns;

use App\Enums\DiscountCampaignStatus;
use App\Models\DiscountCampaign;
use Livewire\Attributes\On;
use Livewire\Component;
use Livewire\WithPagination;

class DiscountCampaignList extends Component
{
    use WithPagination;

    protected $paginationTheme = 'bootstrap';

    // 🟢 اصلاح ۱: یکپارچه‌سازی متغیر جستجو به عنوان رشته خالی
    public $search = '';

    /**
     * 🟢 اصلاح ۲: ریست کردن پاژینیشن به محض تایپ ادمین در اینپوت جستجو
     */
    public function updatedSearch()
    {
        $this->resetPage();
    }

    /**
     * حذف اتمیک کمپین و کنترل هوشمند موقعیت پاژینیشن
     */
    #[On('destroy_discount_campaign')]
    public function destroyDiscountCampaign($id)
    {
        DiscountCampaign::destroy($id);

        $campaigns = $this->getCampaignsQuery()->paginate(10);

        // 🟢 لایووایر را مستقیماً به کمترین مقدار بین صفحه فعلی و آخرین صفحه موجود ببر
        $this->setPage(min($this->getPage(), $campaigns->lastPage()));
    }

    /**
     * تغییر وضعیت سریع و تعاملی فعال/غیرفعال کمپین
     */
    public function changeStatus($id)
    {
        $discount_campaign = DiscountCampaign::query()->findOrFail($id);

        $newStatus = $discount_campaign->status === DiscountCampaignStatus::Active->value
            ? DiscountCampaignStatus::InActive->value
            : DiscountCampaignStatus::Active->value;

        $discount_campaign->update([
            'status' => $newStatus
        ]);
    }

    private function getCampaignsQuery()
    {
        return DiscountCampaign::query()

            ->when(trim($this->search), function ($q) {
                $q->where('name', 'like', '%' . $this->search . '%');
            })
            ->latest();
    }

    public function render()
    {
        $campaigns = $this->getCampaignsQuery()->paginate(10);

        return view('livewire.admin.discount-campaigns.discount-campaign-list', compact('campaigns'));
    }
}
