<?php

namespace App\Livewire\Frontend\Questions;

use App\Enums\QuestionStatus;
use App\Models\Question;
use Livewire\Component;

class QuestionList extends Component
{
    public $product;

    public function addReply($question_id)
    {
        $this->dispatch('replyQuestion',$question_id);
    }
    public function render()
    {
        $questions = Question::query()
            ->where('product_id',$this->product->id)
            ->where('status',QuestionStatus::Approved->value)
            ->where('parent_id',null)
            ->get();
        return view('livewire.frontend.questions.question-list',compact('questions'));
    }
}
