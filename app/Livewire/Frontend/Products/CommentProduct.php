<?php

namespace App\Livewire\Frontend\Products;

use App\Enums\CommentStatus;
use App\Models\Comment;
use App\Models\Order;
use App\Models\ProductStar;
use Illuminate\Support\Facades\DB;
use Livewire\Attributes\On;
use Livewire\Component;

class CommentProduct extends Component
{
    public $product;
    public $name;
    public $advantage;
    public $disadvantage;
    public $suggestion;
    public $body;
    public $advantageList=[];
    public $disadvantageList=[];
    public $scoreList=[];

    public function submitComment()
    {
        DB::beginTransaction();
        try {
            $user = auth()->user();
            $comment = new Comment();
            $comment->user_id = $user->id;
            $comment->product_id = $this->product->id;
            $comment->name= $this->name;
            $comment->advantage= implode('#',$this->advantageList);
            $comment->disadvantage= implode('#',$this->disadvantageList);
            $comment->is_buyer = Order::isBuyer($this->product->id,$user->id);
            $comment->suggestion= $this->suggestion;
            $comment->body= $this->body;
            $comment->status = CommentStatus::Draft->value;
            $this->product->comments()->save($comment);


            foreach ($this->scoreList as $key=>$value){
                $star_item = ProductStar::query()
                    ->where('product_id',$this->product->id)
                    ->where('id',$key)
                    ->first();
                $star_item->increment('score',$value);
                $star_item->increment('count');
            }
            session()->flash('message','نظر شما ثبت شد و پس از تایید مدیر به نمایش گذاشته میشود');
            $this->reset('name','advantage','disadvantage','suggestion','body','advantageList','disadvantageList');
            DB::commit();

        } catch (\Exception $exception) {

            DB::rollBack();
        }
    }

    public function addAdvantage()
    {
        if ($this->advantage){
            array_push($this->advantageList,$this->advantage);
            $this->reset('advantage');
        }
    }

    public function addDisAdvantage()
    {
        if ($this->disadvantage){
            array_push($this->disadvantageList,$this->disadvantage);
            $this->reset('disadvantage');
        }
    }
    #[On('sendScore')]
    public function sendScore($scoreList)
    {
        $this->scoreList = $scoreList;
    }
    public function render()
    {
        return view('livewire.frontend.products.comment-product');
    }
}
