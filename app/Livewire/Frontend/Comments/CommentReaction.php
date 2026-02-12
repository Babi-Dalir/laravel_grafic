<?php

namespace App\Livewire\Frontend\Comments;

use App\Models\UserVote;
use Livewire\Attributes\On;
use Livewire\Component;

class CommentReaction extends Component
{
    public $comment;
    public $product;

    #[On('refreshVote')]
    public function refreshVote()
    {
        $this->dispatch('$refresh');
    }
    public function like($comment_id)
    {
        $user_id = auth()->user()->id;
        $user_vote = UserVote::query()
            ->where('user_id',$user_id)
            ->where('comment_id',$comment_id)
            ->where('vote_type',"comment")
            ->first();
        if ($user_vote){
            if ($user_vote->type == "dislike"){
                $user_vote->update([
                    'type'=>'like'
                ]);
                $user_vote->comment()->increment('like');
                $user_vote->comment()->decrement('dislike');
                $this->dispatch('refreshVote');
            }
        }else{
            $create_user_vote = UserVote::query()->create([
                'user_id'=>$user_id,
                'comment_id'=>$comment_id,
                'type'=>'like',
                'vote_type'=>"comment"
            ]);
            $create_user_vote->comment()->increment('like');
            $this->dispatch('refreshVote');
        }
    }
    public function dislike($comment_id)
    {
        $user_id = auth()->user()->id;
        $user_vote = UserVote::query()
            ->where('user_id',$user_id)
            ->where('comment_id',$comment_id)
            ->where('vote_type',"comment")
            ->first();
        if ($user_vote){
            if ($user_vote->type == "like"){
                $user_vote->update([
                    'type'=>'dislike'
                ]);
                $user_vote->comment()->increment('dislike');
                $user_vote->comment()->decrement('like');
                $this->dispatch('refreshVote');
            }
        }else{
            $create_user_vote = UserVote::query()->create([
                'user_id'=>$user_id,
                'comment_id'=>$comment_id,
                'type'=>'dislike',
                'vote_type'=>"comment"
            ]);
            $create_user_vote->comment()->increment('dislike');
            $this->dispatch('refreshVote');
        }
    }
    public function render()
    {
        return view('livewire.frontend.comments.comment-reaction');
    }
}
