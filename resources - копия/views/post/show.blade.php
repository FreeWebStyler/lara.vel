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
    <article class=post postid={{$post->id}}>
        <a href={!! route('post.show', [$post->id])!!}><span class=title style=display:inline-block id="post{{$post->id}}">{!! $post->title !!} </span></a> &nbsp; @if(
        (isset($cans['create-post']) && $post->user_id == $userid) || isset($cans['editall-posts']))
        <button type="button" class="btn btn-primary btn-xs" onclick=location.href="{!! route('post.edit', [$post->id])
        !!}">Edit</button>
        @endif <!--  {--
        link_to_route
        ('post.edit', 'Edit'
         , [$post->id]) --} ]  [ <a href=>Edit</a> ] -->

        @if(isset($cans['deleteAll-posts'])) <button type="button" postid={{$post->id}} class="btn btn-danger btn-xs" onclick=confirmDelete(this,{{$post->id}})
        >Delete</button>   <!-- [ <a postid={{$post->id}} class=postdel onclick=confirmDelete(this,{{ $post->id }})
        >Delete</a> ] --> @endif
        <div class="content">
        {{$post->id}} <br> {!! $post->content !!}
        </div>
        author: @if($post->author==null) unknown @else {{ $post->author }} @endif published: {{ $post->published_at }} <br>
        tags: {{ $post->tags }}
    </article>

    @if(isset($cans['deleteAll-posts']))

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
    @endif
    {{ csrf_field() }}
    <p></p>
    {!! Form::label('Comments:') !!}
    <p></p>

    <div id="comments"><?= $comments ?></div>

        {!! Form::open(['route'=>'comment.store', 'class' => 'comm_form']) !!}
        <p>
         Комментировать пост:
        </p>
        <div align="right">
            @include('comment._form',['id' => $post->id])
        </div>
        {!! Form::close() !!}

        <div id="res">Result div</div>

     @if($delUnpostedComment)
         <script>localStorage.setItem("unpostedComment",""); cl('delunp');</script>
     @endif

@if(isset($cans['deleteAll-posts']))

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

    function setReplyForm(el, id){
        //cl(el+' '+id); if(!$('#ans'+id).length) cl($('#com'+id));
        if($('#edit'+id).length) $('#edit'+id).hide();
        if(!$('#ans'+id).length)
            $('#com'+id).append('<div id=ans'+id+' align=right style=margin-top:5px;width:555px;margin-left:20px><textarea rows="10" cols="50" name="comment" style="height:100px;" class="form-control" id="comment"></textarea>' +
            '<input type=button value=Оветить class="btn btn-primary" style=margin-top:10px onclick=reply(this,'+ id +')></div>'); else $('#ans'+id).toggle();
        if(typeof lastansid != 'undefined' && lastansid!=id) $('#ans'+lastansid).hide();
        lastansid=id;
        //if($('#ans'+id).is(':visible')) $('.comm_form').hide(); else $('.comm_form').show();
        //$('.comm_form').toggle();
        //cl($('#ans'+id).attr('style'));

           /* var title=$('#post'+el.getAttribute('postid')).html();
            bootbox.confirm({
                size: 'small',
                message: "Точно удалить "+ title + " (#"+id+")?",
                callback: function(result){ if(result) deletePost(title,id); // your callback code
                 }
            })*/
    }

    function setEditForm(el, id){
        //cl(el+' '+id); if(!$('#edit'+id).length) cl($('#com'+id));
        //cl($('#com'+id+' > span').html());
        if($('#ans'+id).length) $('#ans'+id).hide();
        if(!$('#edit'+id).length)
                $('#com'+id).append('<div id=edit'+id+' align=right style=margin-top:5px;width:555px;margin-left:20px><textarea rows="10" cols="50" name="comment" style="height:100px;" class="form-control" id="comment">' + $('#com'+id+' > span').html() + '</textarea>' +
                        '<input type=button value=Изменить class="btn btn-primary" style=margin-top:10px onclick=edit(this,'+ id +')></div>'); else $('#edit'+id).toggle();
        if(typeof lasteditid != 'undefined' && lasteditid!=id) $('#edit'+lasteditid).hide();
        lasteditid=id;

        //if($('#edit'+id).is(':visible')) $('.comm_form').hide(); else $('.comm_form').show();

        $('#edit'+id+' > textarea').focus();
        //
        setTimeout(function(){  $('#edit'+id+' > textarea').focus().select(); }, 100);

        /*$('#edit'+id).child(0).focusin(function() {
               var $this = $(this);
               $this.select();
           });*/
        /* var title=$('#post'+el.getAttribute('postid')).html();
            bootbox.confirm({
            size: 'small',
            message: "Точно удалить "+ title + " (#"+id+")?",
            callback: function(result){ if(result) deletePost(title,id); // your callback code
            }
            })*/
    }

    function edit(el, id){
        //alert($('article:eq(0)').attr('postid')); return;
        $el = $(el); //cl($el.parent().children(0).html());
        $('#ans'+id).hide();          //cl(el+' '+id);
        $.ajax({
            type:    "POST",
            url:    "{{ route('comment.update') }}",
            data:    { "_token": $("input[name='_token']").val(), "id" : id, "comment" : $el.parent().children(0).val() },
            success: function(data) {
                if(data==1) $.notify("Запись "+title+" (#"+id+") успешно удалена!", {
                    animate: {
                        enter: 'animated zoomInDown',
                        exit: 'animated zoomOutUp'
                    }, type: 'success'
                });
                $("article[postid="+id+"]").remove();
                $('#res').html(data); //alert('call back');
                eval(data);
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
                if(data){ localStorage.setItem("unpostedComment","");  $('#ans'+id+' > textarea').val(''); location.reload(); }
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

        function confirmDelete(el, id, item, type){
            if(item!='комментарий') var title=$('#post'+el.getAttribute('postid')).html(); else var title='';
            words={'delete':'удалить', 'destroy':'уничтожить'};
            bootbox.confirm({
                size: 'small',
                message: "Точно " + words[type] + " " + item + " " + title + " (#"+id+")?",
                callback: function(result) {
                    if (result)  deleteItem(title, id, item, type);
                }
            })
        }


    function deleteItem(title, id, item, type){
        if(title != '') var url='{{ route('post.delete') }}'; else {
            if(type=='destroy') var url='{{ route('comment.destroy') }}'; else var url='{{ route('comment.delete') }}';
        }

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
            data:    { "_token": $("input[name='_token']").val(), "id": id, "post_id": $('article:eq(0)').attr('postid')},
            success: function(data) {
                //if(data='location.reload');

                if(data==1) $.notify("Запись "+title+" (#"+id+") успешно удалена!", {
                    animate: {
                        enter: 'animated zoomInDown',
                        exit: 'animated zoomOutUp'
                    }, type: 'success'
                });
                $("article[postid="+id+"]").remove();
                $('#res').html(data); //alert('call back');
                eval(data);
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

    function getOrig(id){
        var oDiv = document.getElementById('com'+id);
        return oDiv.childNodes[0].innerHTML;
        /*var firstText = "";
        for (var i = 0; i < oDiv.childNodes.length; i++) {
            var curNode = oDiv.childNodes[i];
            if (curNode.nodeName === "#text") {
                return curNode.nodeValue;
                break;
            }
        }*/
    }

    function showOrig(el, event, id){
        if(el == 'destr'){ document.getElementById('showOrigCom').style.display = 'none'; return;}
        var text = getOrig(id);
        var top = event.pageY; //cl('top '+top);
        if(top < 0) top*= -1;
        var left = event.pageX+20;
        if(document.getElementById('showOrigCom') == null) {
            var div = document.createElement('div');
            div.className = "alert alert-success";
            div.innerHTML = text;
            div.setAttribute('style',top+"px; left:"+left+"px");
            div.id = 'showOrigCom';
            document.body.appendChild(div);
        } else {
            cl(top);
            div = document.getElementById('showOrigCom');
            div.innerHTML = text;
            div.style.top = top-25+'px';
            div.style.left = left+25+'px';
            div.style.display = 'block';
        }
    }
    </script>

    <script>
        $('title').html('{{ $post->id }}');
    </script>
    @endif
@stop
