@extends('layouts.app') <!-- //!! route('post', ['id' => $post->id]) !! 111 -->

@section('top')
<div>
    · {!! link_to_route('posts','published') !!}  &nbsp; · &nbsp;
    {!! link_to_route('posts.unpublished','unpublished') !!} &nbsp; · &nbsp;
     {!! link_to_route('post.create','new') !!} ·
</div>
@stop

@section('content')
 @foreach($posts as $post)

     {{-- $phone       $phone = User::find($post->user_id)->phone; --}}
    <article postid={{$post->id}}>
        <a href={!! route('post.show', [$post->id])!!}><h3 style=display:inline-block id="post{{$post->id}}">{!! $post->title !!} </h3></a> &nbsp; @if(
        ($canCreatePost && $post->user_id == $userid) || $canEditAll)
        <button type="button" class="btn btn-primary btn-xs" onclick=location.href="{!! route('post.edit', [$post->id])
        !!}">Edit</button>
        @endif <!--  {--
        link_to_route
        ('post.edit', 'Edit'
         , [$post->id]) --} ]  [ <a href=>Edit</a> ] -->

        @if($canDeleteAll) <button type="button" postid={{$post->id}} class="btn btn-danger btn-xs" onclick=confirmDelete(this,{{$post->id}})
        >Delete</button>   <!-- [ <a postid={{$post->id}} class=postdel onclick=confirmDelete(this,{{ $post->id }})
        >Delete</a> ] --> @endif
        <p>
        {{$post->id}} {!! $post->excerpt !!}
        <p>
        author: @if($post->author==null) unknown @else {{ $post->author }} @endif published: {{ $post->published_at }} <br>
        tags: {{ trim($post->tags, ",") }}
    </article>

 @endforeach {{-- $posts->render(new PaginationPresenter()) --}} {{-- $posts->pages() --}} {{-- $posts->render() --}}

{{ $posts->render(new \App\Models\PaginationPresenter($posts)) }}

    @if($canDeleteAll)

        <div id="res">Result div</div>

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
    <script>

        (function () {
            "use strict";

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
