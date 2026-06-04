<?php

namespace App\Livewire\Frontend\Products;

use App\Models\Category;
use Livewire\Attributes\On;
use Livewire\Component;
use Livewire\WithPagination;

class CategoryProduct extends Component
{
    use WithPagination;

    protected $paginationTheme = 'bootstrap';

    public $main_slug;
    public $sub_slug;
    public $child_slug;
    public $page=1;
    private $products;
    private $newest;
    private $more_sold;
    public $compare_product_list=[];
    public $currentCategory;
    public function mount()
    {
        $this->currentCategory = Category::getCategoryBySlug(
            $this->main_slug,
            $this->sub_slug,
            $this->child_slug
        );
        $this->products = Category::getProductByCategory($this->main_slug, $this->sub_slug, $this->child_slug, 'id', 'DESC', $this->page);
        $this->newest = [];
        $this->more_sold = [];
    }

    public function allProducts()
    {
        $this->products = Category::getProductByCategory($this->main_slug, $this->sub_slug, $this->child_slug, 'id', 'DESC', $this->page);
        $this->newest = [];
        $this->more_sold = [];
    }

    public function newestProducts()
    {
        $this->products = [];
        $this->newest = Category::getProductByCategory($this->main_slug, $this->sub_slug, $this->child_slug, 'created_at', 'DESC', $this->page);;
        $this->more_sold = [];
    }

    public function moreSoldProducts()
    {
        $this->products = [];
        $this->newest = [];
        $this->more_sold = Category::getProductByCategory($this->main_slug, $this->sub_slug, $this->child_slug, 'sold', 'DESC', $this->page);;
    }

    public function changePage($page, $index)
    {
        $this->page = $page;
        switch ($index) {
            case 1:
                $this->allProducts();
                break;
            case 2:
                $this->newestProducts();
                break;
            case 3:
                $this->moreSoldProducts();
                break;
            default:
                $this->allProducts();
        }
    }

    public function compareProducts($product_id)
    {
        if (!in_array($product_id,$this->compare_product_list)){
            array_push($this->compare_product_list,$product_id);
        }else{
            if (($key=array_search($product_id,$this->compare_product_list)) !== false){
                unset($this->compare_product_list[$key]);
            }
        }
        if (count($this->compare_product_list) == 2){
            return redirect()->route('compare.products',[$this->compare_product_list[0],$this->compare_product_list[1]]);
        }
        $this->allProducts();
    }

    public function render()
    {
        $products = $this->products;
        $newest = $this->newest;
        $more_sold = $this->more_sold;
        return view('livewire.frontend.products.category-product', compact('products', 'newest', 'more_sold'));
    }
}
