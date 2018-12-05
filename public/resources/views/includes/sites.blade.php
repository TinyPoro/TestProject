<div class="{{$no_response or 'table-responsive'}}" id="sites_list">
  <table class="table table-bordered table-report table-hover">
    <tr>
      <th width="40px">
        {!! \App\Colombo\PageHelper::sortLink($sites, 'id', 'ID', ['filtered' => $filtered]) !!}
      </th>
      <th>
        {!! \App\Colombo\PageHelper::sortLink($sites, 'country', "<i class='fa fa-globe' title='Country'></i>", ['filtered' => $filtered]) !!}
      </th>
      <th width="">
        {!! \App\Colombo\PageHelper::sortLink($sites, 'start_url', "Start Url", ['filtered' => $filtered]) !!}
      </th>
      <th>
        Assigned
      </th>
      <th>
        {!! \App\Colombo\PageHelper::sortLink($sites, 'updated_at', "<i class='fa fa-clock-o' title='Updated At'></i>", ['filtered' => $filtered]) !!}
      </th>
      <th>
        {!! \App\Colombo\PageHelper::sortLink($sites, 'header_checked', "HEAD", ['filtered' => $filtered]) !!}
      </th>
      <th>
        {!! \App\Colombo\PageHelper::sortLink($sites, 'downloadable_links', "<i class='fa fa-files-o'></i>", ['filtered' => $filtered]) !!}
      </th>
      <th>
        {!! \App\Colombo\PageHelper::sortLink($sites, 'downloaded_links', "<i class='fa fa-download'></i>", ['filtered' => $filtered]) !!}
      </th>
      <th>
        {!! \App\Colombo\PageHelper::sortLink($sites, 'uploaded', "<i class='fa fa-upload'></i>", ['filtered' => $filtered]) !!}
      </th>
      <th>
        {!! \App\Colombo\PageHelper::sortLink($sites, 'crawled_links', "<i class='fa fa-bug'></i>", ['filtered' => $filtered]) !!}
      </th>
      <th>
        {!! \App\Colombo\PageHelper::sortLink($sites, 'status', "<i class='fa fa-heartbeat'></i>", ['filtered' => $filtered]) !!}
      </th>
      <th width="">
        <i class="fa fa-motorcycle"></i> Run status
      </th>
      <th>
        <i class="fa fa-hand-lizard-o"></i> Actions
      </th>
    </tr>
    @foreach($sites as $site)
      <tr id="row_site_{{$site->id}}" class="row_site">
        <td class="site_id" data-id="{{$site->id}}">{{$site->id}}</td>
        <td class="site_country">
          {{config('country.list.' . $site->country, "N/A")}}
          {!! $site->verifyStatusText(null, true) !!}
        </td>
        <td style="max-width: 300px;" class="site_start_url">
          <ul class="list-report">
            <li>
              <i class="fa fa-chrome"></i> {{$site->browser_engine}}
            </li>
            <li>
              {!! Html::link($site->start_url,
              '<i class="fa fa-external-link"></i> ' . ($site->domain == 'sites.google.com' ? \App\Helpers\PhpUri::googleSiteName($site->start_url) : $site->domain),
              ['target' => '_blank', 'title' => $site->start_url],
              null,
              false) !!}
            </li>
            <li class="">
              <a href="{{route('site.comment', ['site' => $site->id])}}" title="Click to disscus about this" class="{{ strpos($site->note, 'No') === 0 ? '' : 'text-warning' }}">
              <i class="fa fa-comment-o"></i> {{str_limit($site->note, 30)}}
              </a>
            </li>
            @if($site->parent_id)
              <li>
                <i class="fa fa-sitemap"></i> {{$site->parent_id}}
              </li>
              @endif
            @if($site->parent_id)
              <li>
                <i class="fa fa-sitemap"></i> {{$site->parent_id}}
              </li>
              @endif
          </ul>
        </td>
        <td class="site_assigned text-info">
          <div class='pb-1'>
          <i class="fa fa-user"></i> {{$site->created_user->name or "NA"}}
          </div>
          <i class="fa fa-user-o"></i> {{$site->assigned_user->name or "NA"}}
        </td>
        <td class="site_updated_at">
          <a href="{{route('site.run_history', ['site' => $site->id])}}" target="_blank">
          {{$site->updated_at->diffForHumans()}}
          </a><br/>
          {{$site->created_at->diffForHumans()}}
        </td>
        <td class="site_header_checked">
          {{$site->header_checked}}
        </td>
        <td class="site_files">
          <a href="{{route('download_link.index', ['site' => $site->id])}}">
            {{$site->added_links}}/{{$site->downloadable_links}}
          </a>
        </td>
        <td class="site_downloaded_files">
          {{$site->downloaded_links}}
        </td>
        <td class="site_uploaded">
          {{$site->uploaded}}
        </td>
        <td class="site_crawled">
          <a href="{{route('site.stack_view', ['site' => $site->id])}}" target="_blank">
          {{$site->crawled_links}}
          </a>
        </td>
        <td class="site_status">
          <span class="text-{{$site->status == 10 ? "warning" : ($site->status == 100 ? "danger" : ($site->status == 1 ? "success" : "info"))}}">{{$site->status_text}}</span>
          @if($site->status == 1 && Auth::user()->can('manage', $site))
            {!! Html::link(route('site.tasks', ['site' => $site->id]), '<i class="fa fa-gear"></i>', ['target' => '_blank', 'class' => 'pull-right'], null, false) !!}
            @endif
        </td>
        <td class="site_tasks_status">
          {!! \App\Helpers\TaskAction::controls($site, Auth::user()->can('manage', $site)) !!}
        </td>
        <td>
          <div class="btn-group btn-group-sm" role="group">
            <button id="actions_{{$site->id}}" type="button"
                    class="btn btn-secondary dropdown-toggle"
                    data-toggle="dropdown"
                    aria-haspopup="true"
                    aria-expanded="false">
              Actions
            </button>
            <div class="dropdown-menu" aria-labelledby="btnGroupDrop1">
              {!! Html::linkRoute('site.edit_selectors', 'Edit Rules', ['id' => $site->id], ['target' => '_blank', 'class' => 'dropdown-item']) !!}
              @can('edit', $site)
              {!! Html::linkRoute('site.edit', 'Edit Info', ['id' => $site->id], ['target' => '_blank', 'class' => 'dropdown-item']) !!}
              @endcan
              {!! Html::linkRoute('site.test', 'Test 5 step', ['site' => $site->id], ['target' => '_blank', 'class' => 'dropdown-item']) !!}
              {!! Html::linkRoute('site.test', 'Test 10 step', ['site' => $site->id, 'max_step' => 10], ['target' => '_blank', 'class' => 'dropdown-item']) !!}
              {!! Html::linkRoute('site.stack_view', 'Stack', ['site' => $site->id], ['target' => '_blank', 'class' => 'dropdown-item']) !!}
              @can('manage', $site)
              @if($site->deleted_at)
                <li class="dropdown-item btn_restore">Restore</li>
                @else
                <li class="dropdown-item btn_delete">Delete</li>
                @endif
                {!! Html::linkRoute('site.make_run_history', 'Make run history', ['site' => $site->id], ['target' => '_blank', 'class' => 'dropdown-item']) !!}
                {!! Html::linkRoute('site.export', 'Export', ['site' => $site->id], ['target' => '_blank', 'class' => 'dropdown-item']) !!}
              @endcan
              {!! Html::linkRoute('site.check_new', 'Check New', ['site' => $site->id], ['target' => '_blank', 'class' => 'dropdown-item']) !!}
            </div>
          </div>
        </td>
      </tr>
    @endforeach
  </table>
</div>