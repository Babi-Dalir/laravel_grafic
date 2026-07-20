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

    public $search = '';

    public function updatedSearch()
    {
        $this->resetPage();
    }

    #[On('destroy_discount_campaign')]
    public function destroyDiscountCampaign($id)
    {
        DiscountCampaign::destroy($id);

        $campaigns = $this->getCampaignsQuery()->paginate(10);
        $this->setPage(min($this->getPage(), $campaigns->lastPage()));
    }

    public function changeStatus($id)
    {
        $discount_campaign = DiscountCampaign::query()->findOrFail($id);

        // 🔒 گارد امنیتی: جلوگیری از فعال‌سازی کمپین‌های منقضی شده با دقت ثانیه‌ای
        if ($discount_campaign->expires_at && $discount_campaign->expires_at->isBefore(now())) {
            $this->dispatch('showToastCampaignError', message: 'این کمپین منقضی شده است و امکان فعال‌سازی مجدد وجود ندارد.');
            return;
        }

        // 🟢 تغییر وضعیت استاندارد بر اساس Enum Cast
        $newStatus = ($discount_campaign->status === DiscountCampaignStatus::Active)
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
        $campaigns = $this->getCampaignsQuery()->paginate(15);

        return view('livewire.admin.discount-campaigns.discount-campaign-list', compact('campaigns'));
    }
}
