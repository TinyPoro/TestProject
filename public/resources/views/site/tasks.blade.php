@extends('layouts.master')


@section('content')
  <div class="">
    @include('site._navigation', ['site' => $site])
    <div class="">
      <div class="col-sm-12">
        <h3 class="mt-3">{{$site->start_url}} <span class="small"> by {{$site->created_user->name}}</span> </h3>
      </div>
    </div>
    <div class="">
      <div class="col-sm-12">
        <h4>Tasks status</h4>
        <div class="table-responsive">
          <table class="table table-bordered table-striped table-report table-hover">
            <tr>
              <th>ID</th>
              <th>Name</th>
              <th>Status</th>
              <th>Attempts</th>
              <th>Created at</th>
              <th>Updated at</th>
              <th>Old status</th>
              <th>Browser engine</th>
              <th>Priority</th>
            </tr>
            @foreach($tasks as $task)
              <tr>
                <td>{{$task->id}}</td>
                <td>{{$task->name}}</td>
                <td>{{$task->status_label}}</td>
                <td>{{$task->attempts}}</td>
                <td>{{$task->created_at}}</td>
                <td>{{$task->updated_at}}</td>
                <td>{{$task->old_status_label}}</td>
                <td>{{$task->browser_engine}}</td>
                <td>{{$task->priority}}</td>
              </tr>
            @endforeach
          </table>
        </div>

      </div>
      <div class="col-sm-12">
        <h4>Tasks manage</h4>
        {!! Former::open(route('site.tasks', ['site' => $site->id]))->method('post') !!}
        {!! Former::checkbox('task_crawl')->check(in_array('crawl', $enabled_task)) !!}
        {!! Former::checkbox('task_download_link')->check(in_array('download_link', $enabled_task)) !!}
        {!! Former::checkbox('task_upload')->check(in_array('upload', $enabled_task)) !!}
        {!! Former::primary_button('Save')->type('submit') !!}
        {!! Former::close() !!}

      </div>
    </div>
  </div>




@endsection