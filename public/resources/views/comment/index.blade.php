@extends('layouts.master')


@section('content')
  @include('site._navigation', ['site' => $site])
  <div class="container">
    <h4>
      <a target="_blank" href="{{$site->start_url}}">
        [ID:{{$site->id}}] {{$site->start_url}}
        <i class="fa fa-external-link"></i>
      </a>
    </h4>
    <p>
      {{$site->note}}
      @if(Auth::user()->can('edit', $site))
        <a href="{{route('site.edit', ['site' => $site->id])}}"><i class="fa fa-pencil"></i> Edit </a>
        @endif
    </p>
    <div class="comments">
      @foreach($comments as $comment)
        <div class="card mb-1">
          <div class="card-block">
            <h4 class="card-title">{{$comment->commented->email}}</h4>
            <h6 class="card-subtitle mb-2 text-muted">{{$comment->created_at->diffForHumans()}}</h6>
            <p class="card-text">{!! \App\ParseDown::instance('comment')->parse($comment->comment) !!}</p>
          </div>
        </div>
        @endforeach
    </div>
    @include('comment.form', compact('site'))
  </div>
  @endsection