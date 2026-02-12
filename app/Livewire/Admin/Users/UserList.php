<?php

namespace App\Livewire\Admin\Users;

use App\Enums\UserStatus;
use App\Models\User;
use Livewire\Component;
use Livewire\WithPagination;

class UserList extends Component
{
    use WithPagination;
    protected $paginationTheme = 'bootstrap';
    public $search;
    public function changeStatus($id)
    {
        $user = User::query()->find($id);
        if ($user->status == UserStatus::Active->value){
            $user->update([
                'status'=>UserStatus::InActive->value
            ]);
        }elseif ($user->status == UserStatus::InActive->value){
            $user->update([
                'status'=>UserStatus::Banned->value
            ]);
        }elseif ($user->status == UserStatus::Banned->value){
            $user->update([
                'status'=>UserStatus::Active->value
            ]);
        }
    }

    public function searchData()
    {
        $this->resetPage();
    }
    public function render()
    {
        $users = User::query()
            ->where('name','like','%'.$this->search.'%')
            ->orWhere('mobile','like','%'.$this->search.'%')
            ->orWhere('email','like','%'.$this->search.'%')
            ->paginate(10);
        return view('livewire.admin.users.user-list',compact('users'));
    }
}
