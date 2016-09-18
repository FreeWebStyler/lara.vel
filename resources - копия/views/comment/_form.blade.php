<div class='form-group'>
    {{-- !! Form::label('Comment', null, ['style' => 'text-align:left']) !! --}}
    {!! Form::textarea('comment', null, ['id' => 'comment', 'class'=>'form-control my']) !!}
</div>

<div class='form-group'>
    {!! Form::submit('Отправить', ['class'=>'btn btn-primary']) !!}
</div>

    {{ Form::hidden('post_id', $id) }}

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

    function storeComment(comment, id = null){
        var url='{{ route('comment.store') }}';
        $.ajax({
            type:    "POST",
            url:     url,
            data:    { "_token": $("input[name='_token']").val(), "id": id, "comment": comment},
            success: function(data) {
                if(data==1) $.notify("Комментарий успешно добавлен!", {
                    animate: {
                        enter: 'animated zoomInDown',
                        exit: 'animated zoomOutUp'
                    }, type: 'success'
                });
               // $("article[postid="+id+"]").remove();
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

