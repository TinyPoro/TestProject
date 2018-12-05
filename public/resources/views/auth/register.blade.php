@extends('layouts.master')

@section('content')
    <div class="container">
        <h3 class="text-center">Create new account</h3>
        <div class="col-sm-6 m-auto">
            {!! Former::open(route('register'))->method('post') !!}
            {{ csrf_field() }}
            {!! Former::text('name')->label('Name') !!}
            {!! Former::email('email')->label('Email') !!}
            {!! Former::password('password')->label('Password') !!}
            {!! Former::password('password_confirmation')->label('Confirm pasword') !!}
            {!! Former::primary_button('Register')->type('submit') !!}
            {!! Former::link('Login')->href(route('login')) !!}
            {!! Former::close() !!}
        </div>
    </div>
@endsection
