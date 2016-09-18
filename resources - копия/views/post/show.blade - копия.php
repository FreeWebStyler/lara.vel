@extends('layouts.app')

<!--
 //!! route('post', ['id' => $post->id]) !! 111
-->
@section('top')
<div>
    · {!! link_to_route('posts','published') !!}  &nbsp; · &nbsp;
    {!! link_to_route('posts.unpublished','unpublished') !!} &nbsp; · &nbsp;
     {!! link_to_route('post.create','new') !!} · 
</div>
@stop

@section('content')

     {{-- $phone       $phone = User::find($post->user_id)->phone; --}}
    <article postid={{$post->id}}>
        <a href={!! route('post.show', [$post->id])!!}><span class=title style=display:inline-block id="post{{$post->id}}">{!! $post->title !!} </span></a> &nbsp; @if(
        ($cans['create-post'] && $post->user_id == $userid) || $cans['editall-posts'])
        <button type="button" class="btn btn-primary btn-xs" onclick=location.href="{!! route('post.edit', [$post->id])
        !!}">Edit</button>
        @endif <!--  {--
        link_to_route
        ('post.edit', 'Edit'
         , [$post->id]) --} ]  [ <a href=>Edit</a> ] -->

        @if($cans['deleteAll-posts']) <button type="button" postid={{$post->id}} class="btn btn-danger btn-xs" onclick=confirmDelete(this,{{$post->id}})
        >Delete</button>   <!-- [ <a postid={{$post->id}} class=postdel onclick=confirmDelete(this,{{ $post->id }})
        >Delete</a> ] --> @endif
        <p>
        {{$post->id}} {!! $post->excerpt !!}
        <p>
        author: @if($post->author==null) unknown @else {{ $post->author }} @endif published: {{ $post->published_at }} <br>
        tags: {{ $post->tags }}
    </article>

    @if($cans['deleteAll-posts'])

        <div class="modal" id="sentDialog" tabindex="-1">
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header">
                        <a href="#" class="close" data-dismiss="modal">&times;</a>
                        <h4>Удаление</h4>
                    </div>
                    <div class="modal-body">
                        <p>Точно удалить <span id="delid"></span>?</p>
                    </div>
                    <div class="modal-footer">
                        <button class="btn btn-success" data-dismiss="modal">Close</button>
                    </div>
                </div>
            </div>
        </div>

    {{ csrf_field() }}
    <p></p>
    {!! Form::label('Comments:') !!}
    <p></p>

    <div id="comments">
        @foreach($comments as $comment) {{-- $phone       $phone = User::find($post->user_id)->phone; --}}
            <article postid={{$comment->id}}>
                {!! $comment->comment !!} author: @if($comment->author==null) unknown @else {{ $comment->author->name }} @endif published: {{ $comment->created_at }} #{{$comment->id}}
                <button type="button" commentid={{$comment->id}} class="btn-link btn-xs" onclick=setReplyForm(this,{{$comment->id}}) >Ответить</button>
                @if(($cans['create-post'] && $comment->user_id == $userid) || $cans['editall-posts'])
                    <button type="button" class="btn btn-link btn-xs" onclick=location.href="{!! route('post.edit', [$post->id]) !!}">Edit</button>
                @endif
                @if($cans['deleteAll-posts'])
                    <button type="button" postid={{$comment->id}} class="btn btn-link btn-xs" onclick=confirmDelete(this,{{$comment->id}}) >Delete</button>
                @endif

                {{-- <button type="button" commentid={{$comment->id}} class="btn-default btn-xs" onclick=setReplyForm(this,{{$comment->id}}) >Ответить</button>
                @if(($cans['create-post'] && $comment->user_id == $userid) || $cans['editall-posts'])
                    <button type="button" class="btn btn-primary btn-xs" onclick=location.href="{!! route('post.edit', [$post->id]) !!}">Edit</button>
                @endif
                @if($cans['deleteAll-posts'])
                    <button type="button" postid={{$comment->id}} class="btn btn-danger btn-xs" onclick=confirmDelete(this,{{$comment->id}}) >Delete</button>
                @endif --}}
            </article><p></p>
        @endforeach
    </div>

        {!! Form::open(['route'=>'comment.store']) !!}
        @include('comment._commentform',['id' => $post->id])
        {!! Form::close() !!}
        <div id="res">Result div</div>

<script>
        (function () {
            "use strict";

            var unpostedComment = localStorage.getItem('unpostedComment');
            if(unpostedComment != null) $('#comment').val(unpostedComment);
            $('#comment').keyup(function(k){ localStorage.setItem('unpostedComment', $('#comment').val()); });

            var $sentDialog = $("#sentDialog");

            $(".postdel2").on("click", function () {
                //console.log(this.getAttribute('postid'));
                $('#delid').html($('#post'+el3.getAttribute('postid')).html());
                $sentDialog.modal('show');
                return false;
            });

            var $sentAlert = $("#sentAlert");

            $sentDialog.on("hidden.bs.modal", function () {
                //alert("close");
                $sentAlert.show();
            });

            $sentAlert.on("close.bs.alert", function () {
                $sentAlert.hide();
                return false;
            });
        })();

    function setReplyForm(el,id){
        cl(el+' '+id);
    if(!$('#ans'+id).length)
       $('#comments > article[postid='+id+']').append('<div id=ans'+id+' align=right style=margin-top:5px;width:555px;margin-left:20px><textarea rows="10" cols="50" name="comment" style="height:100px;" class="form-control" id="comment"></textarea>' +
                '<input type=button value=Отправить class="btn btn-primary" style=margin-top:10px onclick=reply(this,'+ id +')></div><br><p>'); else $('#ans'+id).toggle();
        if(typeof lastansid != 'undefined' && lastansid!=id) $('#ans'+lastansid).hide();
        lastansid=id;

           /* var title=$('#post'+el.getAttribute('postid')).html();
            bootbox.confirm({
                size: 'small',
                message: "Точно удалить "+ title + " (#"+id+")?",
                callback: function(result){ if(result) deletePost(title,id); // your callback code
                 }
            })*/
    }

    function reply(el,id){
        //alert($('article:eq(0)').attr('postid')); return;

        $('#ans'+id).hide();
        cl(el+' '+id);

        $.ajax({
            type:    "POST",
            url:    "{{ route('comment.store') }}",
            data:    { "_token": $("input[name='_token']").val(), "post_id": $('article:eq(0)').attr('postid'), "parent_id" : id , "comment" : $('#ans'+id+' > textarea').val() },
            success: function(data) {
                if(data==1) $.notify("Запись "+title+" (#"+id+") успешно удалена!", {
                    animate: {
                        enter: 'animated zoomInDown',
                        exit: 'animated zoomOutUp'
                    }, type: 'success'
                });
                $("article[postid="+id+"]").remove();
                $('#res').html(data); //alert('call back');
            },
            // vvv---- This is the new bit
            error:   function(jqXHR, textStatus, errorThrown) {

                $('#res').html(jqXHR.responseText);
                cl("Error, status = " + textStatus + ", " +
                        "error thrown: " + errorThrown
                );
                /*alert("Error, status = " + textStatus + ", " +
                 "error thrown: " + errorThrown
                 );*/
            }
        });

        }



        function confirmDelete(el,id){
            var title=$('#post'+el.getAttribute('postid')).html();
            bootbox.confirm({
                size: 'small',
                message: "Точно удалить "+ title + " (#"+id+")?",
                callback: function(result){ if(result) deletePost(title,id); /* your callback code */ }
            })
        }


    function deletePost(title,id){
        var url='{{ route('post.delete') }}';

        //console.log(id);
        //console.log($("input[name='_token']").val());

        /*$.post(
                'post/delete',
                {
                    "_token": $("input[name='_token']").val(),
                    "post_id": id,
                }, function( data ) {
                    alert(333);
                    console.log( data ); // John
                },
                'json'
        );*/

        $.ajax({
            type:    "POST",
            url:     url,
            data:    { "_token": $("input[name='_token']").val(),"post_id": id,},
            success: function(data) {
                if(data==1) $.notify("Запись "+title+" (#"+id+") успешно удалена!", {
                    animate: {
                        enter: 'animated zoomInDown',
                        exit: 'animated zoomOutUp'
                    }, type: 'success'
                });
                $("article[postid="+id+"]").remove();
                $('#res').html(data); //alert('call back');
            },
            // vvv---- This is the new bit
            error:   function(jqXHR, textStatus, errorThrown) {

                $('#res').html(jqXHR.responseText);
                cl("Error, status = " + textStatus + ", " +
                        "error thrown: " + errorThrown
                );
                /*alert("Error, status = " + textStatus + ", " +
                        "error thrown: " + errorThrown
                );*/
            }
        });

    }
    </script>
    @endif
@stop
