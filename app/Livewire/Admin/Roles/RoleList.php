<?php

namespace App\Livewire\Admin\Roles;

use Livewire\Attributes\On;
use Livewire\Component;
use Livewire\WithPagination;
use Spatie\Permission\Models\Role;

class RoleList extends Component
{
    use WithPagination;
    protected $paginationTheme = 'bootstrap';
    public $search;

    #[On('destroy_role')]
    public function destroyRole($id)
    {
        Role::destroy($id);
    }
    public function searchData()
    {
        $this->resetPage();
    }
    public function render()
    {
        $roles = Role::query()
            ->where('name','like','%'.$this->search.'%')
            ->paginate(10);
        return view('livewire.admin.roles.role-list',compact('roles'));
    }
}


