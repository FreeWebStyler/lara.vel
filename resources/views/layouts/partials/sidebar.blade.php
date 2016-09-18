<div class="row">
    <div class="col-md-12 center">
        <h4>Sidebar комментарии:</h4>
    </div>
</div>
<div class="row">
    {{-- $comments --}}

    @foreach($comments as $comment)

        {{-- $phone       $phone = User::find($post->user_id)->phone; --}}
        <article postid={{$comment->id}}>
            {!! $comment->comment !!} author: @if($comment->author==null) unknown @else {{ $comment->author->name }} @endif published: {{ $comment->created_at }} #{{$comment->id}}
        </article><p></p>

    @endforeach
</div>