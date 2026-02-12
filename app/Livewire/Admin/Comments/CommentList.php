<?php

namespace App\Livewire\Admin\Comments;


use App\Enums\CommentStatus;
use App\Models\Brand;
use App\Models\Comment;
use Livewire\Attributes\On;
use Livewire\Component;
use Livewire\WithPagination;

class CommentList extends Component
{
    use WithPagination;
    protected $paginationTheme = 'bootstrap';
    public $search;

    public function searchData()
    {
        $this->resetPage();
    }

    public function submitComment($comment_id)
    {
        $comment = Comment::query()->find($comment_id);
        if ($comment->status == CommentStatus::Draft->value){
            $comment->update([
                'status'=>CommentStatus::Approved->value
            ]);
        }elseif ($comment->status == CommentStatus::Approved->value){
            $comment->update([
                'status'=>CommentStatus::Rejected->value
            ]);
        }else{
            $comment->update([
                'status'=>CommentStatus::Approved->value
            ]);
        }
    }
    public function render()
    {
        $comments = Comment::query()
            ->orderBy('created_at','DESC')
            ->paginate(20);
        return view('livewire.admin.comments.comment-list',compact('comments'));
    }
}
