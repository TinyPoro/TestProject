@extends('layouts.master')


@section('content')

  <div class="container-fluid">
    <h3>Notification <span class="small">{{$is_public ? "Public" : "Private"}} notifications</span></h3>
    <div class="row">
      <div class="col-sm-12">
        <div class="">
          Go to
          @if($is_public)
            <a href="{{route('notifications.mine')}}">Your notifications</a>
            @else
            <a href="{{route('notifications.public')}}">Public notifications</a>
          @endif
        </div>
      </div>
      <div class="col-sm-12 content">
        {!! Former::inline_open()->method('get')->addClass('form-sm mb-2') !!}
        {!! Former::select('read')->options(['' => 'Read status', '0' => 'Unread', '1' => 'Read']) !!}
        @if(auth()->user()->isSuperAdmin() && $is_public && $user === null)
          {!! Former::link('All notifications')->class('btn btn-warning mr-2')->href(route('notifications.public', ['user' => 'all'])) !!}
          @endif
      @if(auth()->user()->isSuperAdmin() && $is_public && $user == 'all')
        {!! Former::text('email')->placeholder('User email') !!}
          {!! Former::select('types[]')->options($types)->multiple(true) !!}
          {!! Former::link('Public only')->class('btn btn-warning mr-2')->href(route('notifications.public')) !!}
        @endif
        {!! Former::submit('Filter')->class('btn btn-success') !!}
        {!! Former::close() !!}
        <div class="row">
        @include('notice._notifications', ['notifications', $notifications])
        </div>
      </div>
    </div>
  </div>

@endsection