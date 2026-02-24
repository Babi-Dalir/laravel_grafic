<?php

namespace App\Livewire\Admin\DiscountCampaigns;

use App\Enums\DiscountCampaignStatus;
use App\Enums\ProductStatus;
use App\Models\Discount;
use App\Models\DiscountCampaign;
use App\Models\Product;
use Livewire\Attributes\On;
use Livewire\Component;
use Livewire\WithPagination;

class DiscountCampaignList extends Component
{
    use WithPagination;
    protected $paginationTheme = 'bootstrap';
    public $search;
    #[On('destroy_discount_campaign')]
    public function destroyDiscountCampaign($id)
    {
        DiscountCampaign::destroy($id);
    }
    public function changeStatus($id)
    {
        $discount_campaign = DiscountCampaign::query()->find($id);
        if ($discount_campaign->status == DiscountCampaignStatus::Active->value){
            $discount_campaign->update([
                'status'=>DiscountCampaignStatus::InActive->value
            ]);
        }elseif ($discount_campaign->status == DiscountCampaignStatus::InActive->value){
            $discount_campaign->update([
                'status'=>DiscountCampaignStatus::Active->value
            ]);
        }
    }

    public function searchData()
    {
        $this->resetPage();
    }
    public function render()
    {
        $campaigns = DiscountCampaign::query()
            ->where('name','like','%'.$this->search.'%')
            ->paginate(10);
        return view('livewire.admin.discount-campaigns.discount-campaign-list',compact('campaigns'));
    }
}
