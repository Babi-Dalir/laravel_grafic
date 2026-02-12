<?php

namespace App\Livewire\Frontend\Shops;

use App\Models\Address;
use App\Models\City;
use App\Models\Province;
use Livewire\Attributes\On;
use Livewire\Component;

class EditAddressModal extends Component
{
    public $name;
    public $mobile;
    public $province;
    public $city;
    public $address;
    public $postal_code;
    public $provinces;
    public $cities;
    public $address_id;

    protected $rules = [
        'name' => 'required',
        'mobile' => 'required|digits:11',
        'province' => 'required',
        'city' => 'required',
        'address' => 'required',
        'postal_code' => 'required|digits:10',
    ];

    public function mount()
    {
        $this->provinces = Province::query()->pluck('province', 'id');
        $this->cities = collect();
    }

    public function changeProvince($province_id)
    {
        $this->cities = City::query()->where('province_id', $province_id)->pluck('city', 'id');
    }

    public function submit()
    {
        $this->validate();
        $edit_address = Address::query()
            ->where('user_id', auth()->user()->id)
            ->where('is_default', true)
            ->first();
        if ($edit_address) {
            $edit_address->update([
                'is_default' => false
            ]);
        }
        $address = Address::query()->find($this->address_id);
        $address->update([
            'name' => $this->name,
            'mobile' => $this->mobile,
            'province_id' => $this->province,
            'city_id' => $this->city,
            'address' => $this->address,
            'postal_code' => $this->postal_code,
            'is_default' => true,
        ]);

        $this->reset([
            'name',
            'mobile',
            'province',
            'city',
            'address',
            'postal_code',
        ]);
        $this->dispatch('closeEditAddressModal');
        $this->dispatch('refreshAddressList');
        $this->dispatch('refreshAddressProfile');
    }

    #[On('editAddress')]
    public function editAddress($address_id)
    {
        $address = Address::query()->find($address_id);
        $this->address_id = $address_id;
        $this->name = $address->name;
        $this->mobile = $address->mobile;
        $this->province = $address->province_id;
        $this->city = $address->city_id;
        $this->address = $address->address;
        $this->postal_code = $address->postal_code;
        $this->cities = City::query()->where('province_id', $address->province_id)->pluck('city', 'id');
    }

    public function render()
    {
        return view('livewire.frontend.shops.edit-address-modal');
    }
}
