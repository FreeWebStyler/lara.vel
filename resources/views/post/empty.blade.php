@extends('layouts.app')
@section('content')

<!--
 //!! route('post', ['id' => $post->id]) !! 111
-->

<div>
    · {!! link_to_route('posts','published') !!}  &nbsp; · &nbsp;
    {!! link_to_route('posts.unpublished','unpublished') !!} &nbsp; · &nbsp;
     {!! link_to_route('post.create','new') !!} · 
</div>

<p></p>
<div> Sorry, there's no posts with tags

    {{--@for ($i = 0; $i < $c; $i++) @if($i != 0),{{$tags[$i]}}@else{{$tags[$i]}}@endif @endfor--}}
    <? //$c=count($tags); for($i=0; $i < $c; $i++){ if($i != 0) echo ', '; echo $tags[$i]; } ?>
    {{-- @for ($i = 0; $i < $c; $i++) @if($i != 0),{{$tags[$i]}}@else{{$tags[$i]}}@endif @endfor --}}
    {{-- @foreach($tags as $tag) @if($tags->first() == $tag) <div class="rtag">{{ $tag }} </div> @endif @endforeach, create new one? </div> --}}

     @foreach($tags as $tag)<? if($tag !== reset($tags)) echo ', ';  echo "<div class='label label-default'>".$tag."</div>"; ?>@endforeach

    {{-- < foreach($tags as $tag){ if($tag !== reset($tags)) echo ' , '; echo "<div class='label label-default'>".$tag."</div>"; } ?>
    , create new one? </div> --}}

<div class='label label-default'>Default</div>

@stop
