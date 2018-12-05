@extends('layouts.master')


@section('content')

  <div class="container-fluid">
    <h3>Notification detail <small>Go to <a href="{{route('notifications.mine')}}">Your notifications </a> or
        <a href="{{route('notifications.public')}}">Public notifications</a></small></h3>
    <div class="row">
      <div class="col-sm-12">Created at :: {{$notification->created_at->diffForHumans()}}</div>
      <div class="col-sm-12">Sent to :: {{$notification->notifiable->email}}</div>
      @if($notification->unread())
      <div class="col-sm-12 font-weight-bold">
        <a href="{{route('notifications.detail', ['id' => $notification->id, 'read' => 'read'])}}" onclick="return confirm('Please sure that you read it and errors was fixed(if need)?')">Đã đọc và giải quyết</a>
      </div>
      @else
        <div class="col-sm-12">Read by :: {{array_get($notification->data,'read_by', '')}} {{$notification->read_at->diffForHumans()}}</div>
        @endif
      <div class="col-sm-12 content pt-3">
        <div style="border: 1px solid #cccccc; padding: 10px;">
        {!! \App\TextFormatter::renderNotificationContent($notification) !!}
        </div>
      </div>
    </div>
  </div>

@endsection