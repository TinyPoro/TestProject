@extends('layouts.master')

@section('scripts')

@endsection

@section('content')
  <div class="container">
    <h3>Import site info</h3>
    <div class="text-danger">
      @if($errors)
        <ul>
        @foreach($errors->messages() as $k => $v)
          <li> {{$k}} :: {{implode("<br/>",$v)}} </li>
          @endforeach
        @endif
        </ul>
    </div>
    <div class="col-sm-12">
      {!! $former = Former::open(route('site.import'))->method('post')->id('site_form')->enctype('multipart/form-data') !!}
      {!! Former::file('site_info') !!}
      <button class="btn btn-block btn-primary" name="submit" value="edit_rules" type="submit">Save</button>
      {!! Former::close() !!}
    </div>
  </div>
@endsection