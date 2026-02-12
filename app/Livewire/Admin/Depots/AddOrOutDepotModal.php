<?php

namespace App\Livewire\Admin\Depots;

use App\Enums\DepotType;
use App\Models\DepotControl;
use App\Models\DepotProduct;
use App\Models\ProductPrice;
use Livewire\Attributes\On;
use Livewire\Component;

class AddOrOutDepotModal extends Component
{
    public $type = DepotType::Enter->value;
    public $count;
    public $name;
    public $depot_product;
    public $product_price;
    #[On('addOrOutDepot')]
    public function addOrOutDepot($product_price_id,$depot_id)
    {
        $this->depot_product = DepotProduct::query()
            ->where('depot_id',$depot_id)
            ->where('product_price_id',$product_price_id)
            ->first();
        $this->product_price = ProductPrice::query()->find($product_price_id);
        $this->name = $this->product_price->product->name;
    }

    public function submitAddOrOut()
    {
        if ($this->type == DepotType::Enter->value){
            $this->product_price->update([
                'count'=>$this->product_price->count + $this->count
            ]);
            $this->depot_product->update([
                'count'=>$this->depot_product->count + $this->count
            ]);
            DepotControl::query()->create([
                'user_id'=>auth()->user()->id,
                'depot_id'=>$this->depot_product->depot_id,
                'product_price_id'=>$this->product_price->id,
                'count'=>$this->count,
                'event_type'=>DepotType::Enter->value,
            ]);
            $this->dispatch('closeDepotModal');
            $this->reset(['type','count']);
            $this->dispatch('refreshDepotList');
            session()->flash('messageAdd','محصول وارد و به تعداد آن اضافه شد');
        }else{
            $this->product_price->update([
                'count'=>$this->product_price->count - $this->count
            ]);
            $this->depot_product->update([
                'count'=>$this->depot_product->count - $this->count
            ]);
            DepotControl::query()->create([
                'user_id'=>auth()->user()->id,
                'depot_id'=>$this->depot_product->depot_id,
                'product_price_id'=>$this->product_price->id,
                'count'=>$this->count,
                'event_type'=>DepotType::Exit->value,
            ]);
            $this->dispatch('closeDepotModal');
            $this->reset(['type','count']);
            $this->dispatch('refreshDepotList');
            session()->flash('messageDelete','محصول خارج و از تعداد آن کم شد');
        }
    }
    public function render()
    {
        return view('livewire.admin.depots.add-or-out-depot-modal');
    }
}
