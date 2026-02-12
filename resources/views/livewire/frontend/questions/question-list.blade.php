<div class="comments-area default">
    <div
        class="section-title text-sm-title title-wide no-after-title-wide mt-5 mb-0 dt-sl">
        <h2>پرسش ها و پاسخ ها</h2>
        <p class="count-comment">123 پرسش</p>
    </div>
    <ol class="comment-list">
        @foreach($questions as $question)
            <li>
                <div class="comment-body">
                    <div class="comment-author">
                        <span class="icon-comment">?</span>
                        <cite class="fn">{{$question->user->name}}</cite>
                        <span class="says">گفت:</span>
                        <div class="commentmetadata">
                            <a href="#">
                                {{\Hekmatinasser\Verta\Verta::instance($question->created_at)->format('%d%B، %Y')}}
                            </a>
                        </div>
                    </div>
                    <p>{{$question->question}}</p>

                    <div class="reply"><a class="comment-reply-link" href="#"
                                          wire:click.prevent="addReply({{$question->id}})">پاسخ</a></div>
                </div>
                @foreach($question->childQuestion as $reply)
                    <ol class="children">
                        <li>
                            <div class="comment-body">
                                <div class="comment-author">
                                                            <span
                                                                class="icon-comment mdi mdi-lightbulb-on-outline"></span>
                                    <cite class="fn">{{$reply->user->name}}</cite> <span
                                        class="says">گفت:</span>
                                    <div class="commentmetadata">
                                        <a href="#">
                                            {{\Hekmatinasser\Verta\Verta::instance($reply->created_at)->format('%d%B، %Y')}}
                                        </a>
                                    </div>
                                </div>
                                <p>{{$reply->question}}</p>

                            </div>
                        </li>
                    </ol>
                @endforeach
            </li>
        @endforeach
    </ol>
</div>
