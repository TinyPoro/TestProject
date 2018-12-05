<nav class="navbar navbar-toggleable-md navbar-inverse bg-inverse mb-4">
  <button class="navbar-toggler navbar-toggler-right" type="button" data-toggle="collapse" data-target="#navbarCollapse" aria-controls="navbarCollapse" aria-expanded="false" aria-label="Toggle navigation">
    <span class="navbar-toggler-icon"></span>
  </button>
  <a class="navbar-brand" href="{{route('home')}}">Crawl System</a>
  <div class="collapse navbar-collapse" id="navbarCollapse">
    <ul class="navbar-nav mr-auto">
      <li class="nav-item active">
        <a class="nav-link" href="{{route('home')}}">Home <span class="sr-only">(current)</span></a>
      </li>
      <li class="nav-item">
        <a class="nav-link" href="{{route('download_link.index', ['site' => 'all'])}}">Downloadable Links</a>
      </li>
      <li class="nav-item">
        <a class="nav-link" href="{{route('site.check_url')}}">Url checker</a>
      </li>
      <li class="nav-item dropdown">
        <a class="nav-link dropdown-toggle" href="#" id="reportDropdown" role="button" data-toggle="dropdown"
           aria-haspopup="true" aria-expanded="false">
          Reports
        </a>
        <div class="dropdown-menu" aria-labelledby="reportDropdown">
          <a class="dropdown-item" href="{{route('report.users.index')}}">Sites report</a>
          <a class="dropdown-item" href="{{route('report.verify.index')}}">Verify status</a>
          <a class="dropdown-item" href="{{route('title.report')}}">Title report</a>
          <a class="dropdown-item" href="{{route('title.message')}}">Title message</a>
        </div>
      </li>
      @if(Auth::check() && Auth::user()->isSuperAdmin())
      <li class="nav-item dropdown">
        <a class="nav-link dropdown-toggle" href="#" id="adminDropdown" role="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
          Admin
        </a>
        <div class="dropdown-menu" aria-labelledby="adminDropdown">
          <a class="dropdown-item" href="{{route('home_trash')}}">Deleted sites</a>
          <a class="dropdown-item" href="{{route('home_run_histories')}}">All run histories</a>
          <a class="dropdown-item" href="{{route('user.list')}}">Users</a>
          <a class="dropdown-item" href="{{route('notifications.compose')}}">New notification</a>
          <a class="dropdown-item" href="{{route('reasons.list')}}">Verify reasons</a>
        </div>
      </li>
        @endif
    </ul>

      @if(Auth::check())
      {!! Form::open(['route' => 'logout', 'class' => 'form-inline mt-2 mt-md-0']) !!}
      <a href="{{route('notifications.public')}}" class="{{\App\Notice::hasNewPublic() ? "text-danger blinking" : ""}}"><span class="fa fa-bell mr-2"></span></a>
        <a href="{{route('user.edit', ['id' => 'you'])}}"> <span class="mr-2 text-info">{{Auth::user()->name}} </span></a>
        <button class="btn btn-xs my-2 my-sm-0" type="submit">Logout</button>
      {!! Form::close() !!}
        @else
      <ul class="navbar-nav">
        <li class="nav-item">
          <a class="nav-link" href="{{route('login')}}">Login</a>
        </li>
      </ul>
      @endif
  </div>
</nav>