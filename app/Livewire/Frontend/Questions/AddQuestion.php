<?php

namespace App\Livewire\Frontend\Questions;

use App\Models\Question;
use Livewire\Attributes\On;
use Livewire\Component;

class AddQuestion extends Component
{
    public $product,$question;
    public $question_id;
    public $is_reply=false;
    #[On('replyQuestion')]
    public function replyQuestion($question_id)
    {
        $this->question_id = $question_id;
        $this->is_reply=true;
    }
    public function createQuestion()
    {
        if (auth()->user()){
            Question::query()->create([
                'user_id'=>auth()->user()->id,
                'product_id'=>$this->product->id,
                'question'=>$this->question,
                'parent_id'=>null,
            ]);
            session()->flash('message','پرسش شما ثبت شد و پس از تایید مدیر به نمایش گذاشته میشود');

        }else{
            session()->flash('message','برای ثبت پرسش حتما باید در سایت ثبت نام کنید');

        }
    }
    public function createReply()
    {
        if (auth()->user()){
            Question::query()->create([
                'user_id'=>auth()->user()->id,
                'product_id'=>$this->product->id,
                'question'=>$this->question,
                'parent_id'=>$this->question_id,
            ]);
            $this->reset(['question','question_id','is_reply']);
            session()->flash('message','پاسخ شما ثبت شد و پس از تایید مدیر به نمایش گذاشته میشود');

        }else{
            session()->flash('message','برای ثبت پاسخ حتما باید در سایت ثبت نام کنید');

        }
    }
    public function render()
    {
        return view('livewire.frontend.questions.add-question');
    }
}
