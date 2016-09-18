<div class='form-group'>
    {!! Form::label('title') !!}
    {!! Form::text('title', isset($post->title) ?  $post->title : null, ['class'=>'form-control']) !!}
</div>

<div class='form-group'>
    {!! Form::label('slug') !!}
    {!! Form::text('slug',isset($post->slug) ?  $post->slug : rand(999,9999999),['class'=>'form-control']) !!}
</div>

<div class='form-group'>
    {!! Form::label('excerpt') !!}
    {!! Form::textarea('excerpt',isset($post->excerpt) ?  $post->excerpt : null, ['class'=>'form-control']) !!}
</div>

<div class='form-group'>
    {!! Form::label('content') !!}
    {!! Form::textarea('content', isset($post->content) ?  $post->content : null, ['class'=>'form-control']) !!}
</div>

<div class='form-group'>
    {!! Form::label('tags') !!}:<div id=rtags></div>
    {{--!! Form::text('tags', isset($post->tags) ?  $post->tags : null ,['class'=>'form-control']) !!--}}
    {{--!! Form::text('tags', isset($post->tags) ?  @foreach ($post->tags as $tag) {{ $tag }} @endforeach : null ,['class'=>'form-control']) !!--}}

    {{--!! Form::text('tags', @foreach ($post->tags as $tag) {{ $tag }} @endforeach , ['class'=>'form-control']) !!--}}

    {{  Form::text('tags', isset($tags) ? $tags : null , ['class'=>'form-control']) }}

    {{-- @if(isset($post->tags)) @foreach ($post->tags as $tag) {{ $tag }} @endforeach @else null @endif --}}
</div>

<div class='form-group'>
    {!! Form::label('published') !!}
    {!! Form::checkbox('published', null, ['class'=>'form-control']) !!}
</div>

<div class='form-group'>
    {!! Form::label('published_at') !!}
    {{--  {!! Form::input('date', 'published_at', date('d-m-Y'), ['class'=>'form-control']) !!} --}}
    {{--  {!! Form::text('published_at', date('d-m-Y'), ['class'=>'form-control']) !!} --}}
    
    {{-- Form::input('date', 'published_at', date('d.m.Y'), ['class'=>'form-control']) --}}
    
    {!! Form::text('published_at', isset($post->publish_date) ?  $post->publish_date : date('d.m.Y') , ['class'=>'datepicker','data-date-format'=>'dd.mm.yyyy']) !!}
    
    {{-- Form::select('size', array('L' => 'Large', 'S' => 'Small'))  --}}

    {!! Form::select('hours', array(0=>'00',1=>'01',2=>'02',3=>'03',4=>'04',5=>'05',6=>'06',7=>'07',8=>'08',9=>'09',10,11,12,13,14,15,16,17,18,19,20,21,22,23),$post->hours)  !!}
    {!! Form::select('minutes', array(0=>'00',1=>'01',2=>'02',3=>'03',4=>'04',5=>'05',6=>'06',7=>'07',8=>'08',9=>'09',10,11,12,13,14,15,16,17,18,19,20,21,22,23,24,25,26,27,28,29,30,31,32,33,34,35,36,37,38,39,40,41,42,43,44,45,46,47,48,49,50,51,52,53,54,55,56,57,58,59), isset($post->minutes) ?  $post->minutes : null)  !!}
    {!! Form::select('seconds', array(0=>'00',1=>'01',2=>'02',3=>'03',4=>'04',5=>'05',6=>'06',7=>'07',8=>'08',9=>'09',10,11,12,13,14,15,16,17,18,19,20,21,22,23,24,25,26,27,28,29,30,31,32,33,34,35,36,37,38,39,40,41,42,43,44,45,46,47,48,49,50,51,52,53,54,55,56,57,58,59), isset($post->seconds) ?  $post->seconds : null)  !!}

    {{-- Form::text('published_at', date('H:i:s'), ['class'=>'timepicker','type'=>'time']) --}}
</div>

<div class='form-group'>
    {!! Form::submit(isset($post->title) ? 'Save' : 'Create', ['class'=>'btn btn-primary']) !!}
</div>

<script> $('.datepicker').datepicker({language: 'ru'}); 

function checkTags(k){
     var arr = $('#tags').val();
     arr=arr.replace(/\s{3,}/,'  ');
     $('#tags').val(arr);
     arr=arr.split('  ');
     var rtags='';
     for (var i = 0; i < arr.length; i++) {
        if(arr[i]!='') rtags+= '<div class=rtag>'+arr[i]+'</div>';
     }
     $('#rtags').html(rtags);
}

$('#tags').keyup(function(k){ checkTags(k); });
checkTags();
//$('#tags').val('2frefrw   fgdfdgfd');

</script>
