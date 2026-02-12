<?php

namespace App\Livewire\Admin\Permissions;

use Livewire\Attributes\On;
use Livewire\Component;
use Livewire\WithPagination;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

class PermissionList extends Component
{
    use WithPagination;
    protected $paginationTheme = 'bootstrap';
    public $search;

    #[On('destroy_permission')]
    public function destroyPermission($id)
    {
        Permission::destroy($id);
    }
    public function searchData()
    {
        $this->resetPage();
    }
    public function render()
    {
        $permissions = Permission::query()
            ->where('name','like','%'.$this->search.'%')
            ->paginate(10);
        return view('livewire.admin.permissions.permission-list',compact('permissions'));
    }
}
