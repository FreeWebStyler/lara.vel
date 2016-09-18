
    <div class="col-md-12 center">
        <h4>Последние комментарии:</h4>
    </div>


    {{-- $comments --}}

    @foreach($LatestComments as $comment)

        {{-- $phone       $phone = User::find($post->user_id)->phone; --}}
        <article postid={{$comment->id}}>
            @if($comment->author==null) unknown: @else {{ $comment->author->name }}: @endif {!! $comment->comment !!} <br> published: {{ $comment->created_at }} #{{$comment->id}}

            {{-- published: {{ $comment->created_at }} #{{$comment->id}} --}}
        </article><p></p>

    @endforeach
