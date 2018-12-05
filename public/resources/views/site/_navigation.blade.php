<ul class="nav nav-tabs">
  <li class="nav-item">
    <a class="nav-link" href="{{route('site.edit', ['site' => $site->id])}}">Edit info</a>
  </li>
  <li class="nav-item">
    <a class="nav-link" href="{{route('site.edit_selectors', ['site' => $site->id])}}">Edit Rules</a>
  </li>
  <li class="nav-item">
    <a class="nav-link" href="{{route('site.tasks', ['site' => $site->id])}}">Task</a>
  </li>
  <li class="nav-item">
    <a class="nav-link" href="{{route('download_link.index', ['site' => $site->id])}}">Downloadable links</a>
  </li>
  <li class="nav-item">
    <a class="nav-link" href="{{route('site.stack_view', ['site' => $site->id])}}">Stack</a>
  </li>
  <li class="nav-item">
    <a class="nav-link" href="{{route('site.run_history', ['site' => $site->id])}}">History</a>
  </li>
  <li class="nav-item">
    <a class="nav-link" href="{{route('site.comment', ['site' => $site->id])}}">Comments</a>
  </li>
</ul>