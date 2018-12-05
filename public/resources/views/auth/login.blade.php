@extends('layouts.master')

@section('content')
<div class="col-sm-6 m-auto">
    {!! Former::open(route('login'))->method('post') !!}
        {{ csrf_field() }}
        {!! Former::email('email')->label('Email') !!}
        {!! Former::password('password')->label('Password') !!}
        {!! Former::checkbox('remember')->label('Remember me??') !!}
        {!! Former::primary_button('Login')->type('submit') !!}
        {{--{!! Former::link('Forgot Your Password?')->href(route('password.request')) !!}--}}
        {!! Former::link('Register')->href(route('register')) !!}
    {!! Former::close() !!}
</div>
@endsection
