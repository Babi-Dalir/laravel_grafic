<div class="form-question-answer dt-sl mb-3">
    <form>
        <textarea class="form-control mb-3" rows="5" wire:model="question"></textarea>
        @if($is_reply)
            <button wire:click.prevent="createReply" type="submit" class="btn btn-dark float-right ml-3">ثبت پاسخ</button>
        @else
            <button wire:click.prevent="createQuestion" type="submit" class="btn btn-dark float-right ml-3">ثبت پرسش</button>
        @endif
    </form>
    <div class="row">
        @if(session()->has('message'))
            <div class="alert alert-success">
                <div>{{session('message')}}</div>
            </div>
        @endif

    </div>
</div>
