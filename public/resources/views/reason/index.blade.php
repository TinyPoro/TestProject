@extends('layouts.master')
@section('content')
  <div class="col-sm-12" style="padding-top: 30px">
    <div style="padding-bottom: 20px;">
      {!! Former::inline_open()->method('get') !!}
      {!! Former::select("status", Request::query('status', 1), ['1' => 'Active', '0' => 'InActive', '-1' => 'All',]) !!}
      {!! Former::button("Filter")->addClass('btn-primary')->type('submit')->value("filter") !!}
      {!! Former::close() !!}
    </div>
    <div class="row">
      <div class="col-sm-6 col-md-7 col-lg-8">
        <table class="table table-striped table-hover">
          <thead>
          <tr>
            <th>
              ID
            </th>
            <th>
              <i class="fa fa-check-square"/>
            </th>
            <th>
              Content
            </th>
            <th>
              Updated at
            </th>
            <th>
              Actions
            </th>
          </tr>
          </thead>
          <tbody>
          @foreach($reasons as $reason)
            <tr>
              <td>{{$reason->id}}</td>
              <td><i class="fa fa-check-square {{$reason->activated ? "text-success" : ""}}"/></td>
              <td>{{$reason->content}}</td>
              <td>{{$reason->updated_at}}</td>
              <td>
                <a href="{{route("reasons.list", ['editing' => $reason])}}" class="btn btn-sm btn-primary"><i class="fa fa-pencil"></i> Edit</a>
                <a href="{{route('reasons.activate', ['id' => $reason->id, 'activation' => $reason->activated ? "0" : "1"])}}"
                   class="btn btn-sm btn-{{$reason->activated ? "danger" : "primary"}} mt-1">
                  <i class="fa fa-check-square"></i>
                  {{$reason->activated ? "Deactivate" : "Activate"}}
                </a>
              </td>
            </tr>
          @endforeach
          </tbody>
        </table>
      </div>
      <div class="col-sm-6 col-md-5 col-lg-4">
        {!! Former::open()->method('post')->route('reasons.update') !!}
        {!! Former::text("id")->label("ID")->placeholder("bỏ trống để thêm mới")->value($editing ? $editing->id : "") !!}
        {!! Former::textarea("content")->rows('5')->placeholder("Content 1\n\nContent2\n\n...")->value($editing ? $editing->content : "") !!}
        <i>Khi thêm mới, thêm nhiều lý do bằng cách viết mỗi lý do cách nhau 1 dòng trống</i>
        {!! Former::button('Submit')->value('update')->addClass('btn-primary')->type('submit') !!}
        {!! Former::close() !!}
      </div>
    </div>
  </div>
@endsection