<?php

namespace App\Livewire\Admin\Questions;

use App\Enums\CommentStatus;
use App\Enums\QuestionStatus;
use App\Models\Comment;
use App\Models\Question;
use Livewire\Component;
use Livewire\WithPagination;

class QuestionList extends Component
{
    use WithPagination;
    protected $paginationTheme = 'bootstrap';
    public $search;

    public function searchData()
    {
        $this->resetPage();
    }

    public function submitQuestion($question_id)
    {
        $question = Question::query()->find($question_id);
        if ($question->status == QuestionStatus::Draft->value){
            $question->update([
                'status'=>QuestionStatus::Approved->value
            ]);
        }elseif ($question->status == QuestionStatus::Approved->value){
            $question->update([
                'status'=>QuestionStatus::Rejected->value
            ]);
        }else{
            $question->update([
                'status'=>QuestionStatus::Approved->value
            ]);
        }
    }
    public function render()
    {
        $questions = Question::query()
            ->orderBy('created_at','DESC')
            ->paginate(20);
        return view('livewire.admin.questions.question-list',compact('questions'));
    }
}
