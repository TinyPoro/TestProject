@extends('layouts.master')


@section('content')

  <div class="container">
    <h3>Edit user <span class="small">{{$user->name}}</span></h3>
    <div class="row">
      <div class="col-sm-12">
        @include('users.form', compact('user'))
      </div>
    </div>
  </div>

  @endsection