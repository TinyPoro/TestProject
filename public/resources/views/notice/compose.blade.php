@extends('layouts.master')

@section('scripts')
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/simplemde/latest/simplemde.min.css">
  <script src="https://cdn.jsdelivr.net/simplemde/latest/simplemde.min.js"></script>
  <script>
      var simplemde = new SimpleMDE({
          autofocus : true
      });
  </script>
  @endsection

@section('content')

  <div class="container">
    <h3>{{$notification->id ? "Edit notification " . $notification->id : "New notification" }}</h3>
    <div class="row">
      {!! Form::open(['method' => 'post', 'route' => 'notifications.save', "style" => "width: 100%;"]) !!}
      @if($notification->id)
        {!! Form::hidden('id', $notification->id) !!}
        @endif
      {!! Form::textarea('message', array_get($notification->data, 'message')) !!}
      <button>Save</button>
      {!! Form::close() !!}
    </div>
  </div>

@endsection