@extends('layouts.master')



@section('content')
  @include('site._navigation', ['site' => $site])
  <div class="container-fluid">
    <div class="page-header">
      <h3 class="page-header mt-2">Stack of <a href="{{$site->start_url}}">{{$site->start_url}}</a></h3>
    </div>
    <div class="text-center">

    </div>
    <div>
      {!! Former::inline_open()->method('get')->addClass('form-sm') !!}
      {!! Former::text('path_id')->label('Path run') !!}
      {!! Former::button('Filter')->type('Submit') !!}
      {!! Former::close() !!}
    </div>
    <div>
      <span>Number: {{ $steps->total() }}</span>
    </div>
    <div class="table-responsive">
      <table class="table table-bordered table-report table-hover">
        <tr>
          <th>
            {!! \App\Colombo\PageHelper::sortLink($steps, 'id', "ID") !!}
          </th>
          <th>
            Parent
          </th>
          <th>
            Path run
          </th>
          <th>
            Url
          </th>
          <th>
            {!! \App\Colombo\PageHelper::sortLink($steps, 'attempts', "Attempts") !!}
          </th>
          <th>
            {!! \App\Colombo\PageHelper::sortLink($steps, 'state', "Status") !!}
          </th>
        </tr>
        @foreach($steps as $step)
          <tr>
            <td>
              {{$step->id}}
            </td>
            <td>
              {{$step->parent}}
            </td>
            <td>
              {{$step->path_run}}
            </td>
            <td>
              <a href="{{route('site.check_url', ['url' => $step->url])}}" target="_blank" title="Check">{{$step->url}}</a>
            </td>
            <td class="text-right">
              {{$step->attempts}}
            </td>
            <td class="text-right">
              {{$step->state}}
            </td>
          </tr>
        @endforeach
      </table>
    </div>
    <div>
      {{$steps->appends(\App\Colombo\PageHelper::getSort())->render()}}
    </div>
  </div>

@endsection
@section('scripts')
  <script type="text/javascript">
    $('.table-report td').hover(function(e){
        var content = $(this).text();
        $('.table-report td').removeClass('bg-primary text-white');
        $('.table-report td:contains("' + content + '")').addClass('bg-primary text-white');
    });
  </script>
@endsection