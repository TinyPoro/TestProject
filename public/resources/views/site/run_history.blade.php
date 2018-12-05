@extends('layouts.master')

@section('scripts')
  <script src="{{url('assets/plugins/vis/vis.js')}}"></script>
  {!! Html::script("assets/plugins/graph_preview.js") !!}
  <script>
    var site_info = {!! $site->getJson(true) !!};
    $(document).ready(function () {
        popup_graph(site_info, 'container', document.getElementById('graph_preview'));
    });
  </script>
  @endsection

@section('content')
  @include('site._navigation', ['site' => $site])
  <div class="container-fluid">
    <div class="page-header">
      <h3 class="page-header mt-2">Run history of <a href="{{$site->start_url}}">{{$site->domain}}</a></h3>
    </div>
    <div>
      <h4>Status</h4>
      <div class="row">
        <div class="col-md-6">
          <ul>
            <li>Start url {{$site->start_url}}</li>
            <li>Note : {{$site->note}}</li>
            <li>Creator : {{$site->created_user->name}}</li>
            <li>Crawled links : {{$site->crawled_links}}</li>
            <li>Document links : {{$site->document_links}}</li>
            <li>Downloadable links : {{$site->downloadable_links}}</li>
            <li>Added links : {{$site->added_links}}</li>
            <li>Header checked : {{$site->header_checked}}</li>
            <li>Status : {{$site->status_text}}</li>
            <li>Attempts : {{$site->crawled_attempts}}</li>

            <li>Created at : {{$site->created_at}}</li>
            <li>Updated at : {{$site->updated_at}}</li>
          </ul>
          <a href="{{route('site.detect_title_setting', ['site_id' => $site->id])}}">Check title setting</a>
          {!! dump($site->settings()) !!}
        </div>
        <div class="col-md-6">
          <div id="graph_preview" class="embed-responsive" style="height: 90%;width: 100%;"></div>
        </div>
      </div>

    </div>
    <div>
      <h4>Run history</h4>
    </div>
    <div class="table-responsive">
      @include('includes.histories', ['histories' => $run_histories, 'filtered' => []])
    </div>
    <div>

    </div>
  </div>

@endsection
@section('scripts')
  <script type="text/javascript">

  </script>
@endsection