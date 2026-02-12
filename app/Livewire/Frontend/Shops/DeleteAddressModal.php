<?php

namespace App\Livewire\Frontend\Shops;

use App\Models\Address;
use Livewire\Attributes\On;
use Livewire\Component;

class DeleteAddressModal extends Component
{
    public $address_id;

    #[On('openModalDeleteAddress')]
    public function openModalDeleteAddress($address_id)
    {
        $this->address_id = $address_id;
    }

    public function deleteAddress($address_id)
    {
        Address::destroy($address_id);
        $address = Address::query()->where('user_id',auth()->user()->id)->first();
        $address->update([
            'is_default'=>true
        ]);
        $this->dispatch('closeDeleteAddressModal');
        $this->dispatch('refreshAddressList');
        $this->dispatch('refreshAddressProfile');
    }
    public function render()
    {
        return view('livewire.frontend.shops.delete-address-modal');
    }
}
