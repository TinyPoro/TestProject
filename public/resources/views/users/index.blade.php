@extends('layouts.master')


@section('content')
  <div class="container">
    <h3>User Manage <span class="small">user list</span></h3>
    <div class="col-sm-12 pb-2">
      {!! Former::inline_open()->method('get') !!}
      {!! Former::text('user')->label("User ID/Email")->placeholder("User ID/Email") !!}
      {!! Former::text('name')->label("Name")->placeholder("Name") !!}
      {!! Former::select('group')->options(["" => "All group"] + config('group.list')) !!}
      {!! Former::select('status')->options(["" => "All status"] + config('group.status')) !!}
      {!! Former::primary_button('Filter')->type("submit") !!}
      {!! Former::close() !!}
    </div>
    <div class="col-sm-12">
      <table class="table table-striped table-hover">
        <tr>
          <th>
            {!! \App\Colombo\PageHelper::sortLink($users, "id") !!}
          </th>
          <th>
            {!! \App\Colombo\PageHelper::sortLink($users, "name") !!}
          </th>
          <th>
            {!! \App\Colombo\PageHelper::sortLink($users, "email") !!}
          </th>
          <th>
            {!! \App\Colombo\PageHelper::sortLink($users, "group") !!}
          </th>
          <th>
            Sites
          </th>
          <th>
            Actions
          </th>
        </tr>
        @foreach($users as $user)
          <tr>
            <td>{{$user->id}}</td>
            <td>{{$user->name}}</td>
            <td>{{$user->email}}</td>
            <td>{{$user->group_name}}</td>
            <td>{{rand(0,10)}}</td>
            <td>
              @can('manage', $user)
                {!! Html::link(route('user.edit', ['id' => $user->id]), "<i class='fa fa-pencil'></i>", [], null, false) !!}
              @endcan
              @can('ban', $user)
                @if($user->banned)
                  {!! Html::link(route('user.un_ban', ['id' => $user->id]), '<i class="fa fa-ban text-danger"></i>', ['title' => 'Un-Ban this user', 'onclick' => 'return sure();'], null, false) !!}
                @else
                    {!! Html::link(route('user.ban', ['id' => $user->id]), '<i class="fa fa-ban text-primary"></i>', ['title' => 'Ban this user', 'onclick' => 'return sure();'], null, false) !!}
                  @endif
              @endcan
              @can('login_as', $user)
                  {!! Html::link(route('user.login_as', ['id' => $user->id]), '<i class="fa fa-sign-in text-danger"></i>', ['title' => 'Login as this user', 'onclick' => 'return sure();'], null, false) !!}
                @endcan
            </td>
          </tr>
          @endforeach
      </table>
      <div class="pagination">
        {!! $users->appends(['sort' => Request::query('sort')])->render('') !!}
      </div>
    </div>

  </div>






  @endsection