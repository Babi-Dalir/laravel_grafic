<?php

namespace App\Livewire\Admin\PropertyGroups;

use App\Models\PropertyGroup;
use Livewire\Attributes\On;
use Livewire\Component;
use Livewire\WithPagination;

class PropertyGroupList extends Component
{
    use WithPagination;

    protected $paginationTheme = 'bootstrap';

    // 🟢 اصلاح ۱: مقداردهی اولیه به صورت رشته خالی برای تمیزی لایه کامپوننت
    public $search = '';

    /**
     * 🟢 اصلاح ۳: به محض تایپ کاربر در اینپوت، پاژینیشن به صفحه اول ریست می‌شود (جایگزین سرچ دیتای مرده)
     */
    public function updatedSearch()
    {
        $this->resetPage();
    }

    /**
     * حذف اتمیک گروه ویژگی با مدیریت هوشمند تجربه کاربری پاژینیشن
     */
    #[On('destroy_property_group')]
    public function destroyPropertyGroup($id)
    {
        PropertyGroup::destroy($id);

        // واکشی مجدد دیتا برای محاسبه دقیق صفحه‌ها پس از حذف فیزیکی رکورد
        $property_groups = $this->getGroupsQuery()->paginate(20);

        // 🟢 اصلاح ۲: کاربر فقط در صورتی به صفحه قبل هدایت می‌شود که صفحه فعلی کاملاً خالی شده باشد
        if ($this->page > $property_groups->lastPage()) {
            $this->resetPage();
        }
    }

    /**
     * ابزار کمکی جهت یکپارچه‌سازی کوئری در رندر و متد حذف
     */
    private function getGroupsQuery()
    {
        return PropertyGroup::query()
            ->with('category')

            // 🟢 اصلاح ۴: جلوگیری از اجرای کوئری مخرب LIKE '%%' روی سرور در صورت خالی بودن سرچ باکس
            ->when(trim($this->search), function ($q) {
                $q->where('name', 'like', '%' . $this->search . '%');
            })
            ->latest();
    }

    public function render()
    {
        $property_groups = $this->getGroupsQuery()->paginate(20);

        return view('livewire.admin.property-groups.property-group-list', compact('property_groups'));
    }
}
