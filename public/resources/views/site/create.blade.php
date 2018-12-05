@extends('layouts.master')

@section('scripts')
  <script>
    $('input[name=start_url]').on('change', function(e){
        checkStartUrl();
    });
    function checkStartUrl() {
        var id = $('#site_id');
        var start_url = $('#start_url').val();
        if(id.length){
            id = id.val();
        }else{
            id = 0;
        }
        axios.post('{{route('site.check_start_url')}}', {
            id: id,
            start_url: start_url
        }).then(function(response){
                $('#site_list').html(response.data);
        });
    }
    $(document).ready(function(){
        checkStartUrl();
    });
  </script>
  @endsection

@section('content')
  @includeWhen($site->id, 'site._navigation', ['site' => $site])
  <div class="container">
    <h3>{{$site->id ? "Edit " . $site->start_url : "Create new site"}} <a class="small" href="{{route('site.import')}}">or import</a></h3>
    <div class="col-sm-12">
      {!! $former = Former::open(route('site.store'))->method('post')->id('site_form') !!}
      @php
      Former::populate($site);
      @endphp
      @include('site.form')
      <button class="btn btn-block btn-primary" name="submit" value="edit_rules" type="submit">Save</button>
      {!! Former::close() !!}
    </div>
  </div>
  @endsection