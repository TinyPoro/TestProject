<li class="nav-item dropdown">
  <a class="nav-link dropdown-toggle" data-toggle="dropdown" href="#" role="button" aria-haspopup="true" aria-expanded="false">Goto</a>
  <div class="dropdown-menu">
    <a class="dropdown-item" href="{{route('site.edit', ['site' => $site->id])}}">Edit info</a>
    {{--<a class="dropdown-item" href="{{route('site.edit_selectors', ['site' => $site->id])}}">Edit Rules</a>--}}
    <a class="dropdown-item" href="{{route('site.tasks', ['site' => $site->id])}}">Task</a>
    <a class="dropdown-item" href="{{route('download_link.index', ['site' => $site->id])}}">Downloadable links</a>
    <a class="dropdown-item" href="{{route('site.stack_view', ['site' => $site->id])}}">Stack</a>
    <a class="dropdown-item" href="{{route('site.run_history', ['site' => $site->id])}}">History</a>
    <a class="dropdown-item" href="{{route('site.comment', ['site' => $site->id])}}">Comments</a>
    <a class="dropdown-item" target="_blank" href="{{route('site.test', ['site' => $site->id,'step' => 5])}}">Test 5 steps</a>
  </div>
</li>