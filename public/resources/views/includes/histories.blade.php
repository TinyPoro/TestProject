<style>
  .table td{
    text-align: right;
  }
</style>
<table class="table table-bordered table-report table-hover">
  <tr>
    <th>
      {!! \App\Colombo\PageHelper::sortLink($histories, 'id', 'ID', ['filtered' => $filtered]) !!}
    </th>
    <th>
      {!! \App\Colombo\PageHelper::sortLink($histories, 'created_at', 'Reset time', ['filtered' => $filtered]) !!}
    </th>
    <th>
      {!! \App\Colombo\PageHelper::sortLink($histories, 'site_id', 'Site', ['filtered' => $filtered]) !!}
    </th>
    <th>
      {!! \App\Colombo\PageHelper::sortLink($histories, 'crawled_links', 'Crawled before', ['filtered' => $filtered]) !!}
    </th>
    <th>
      {!! \App\Colombo\PageHelper::sortLink($histories, 'last_downloadable', 'Crawled after', ['filtered' => $filtered]) !!}
    </th>
    <th>
      {!! \App\Colombo\PageHelper::sortLink($histories, 'crawled_added', 'Crawled added', ['filtered' => $filtered]) !!}
    </th>
    <th>
      {!! \App\Colombo\PageHelper::sortLink($histories, 'downloadable_links', 'Links before', ['filtered' => $filtered]) !!}
    </th>
    <th>
      {!! \App\Colombo\PageHelper::sortLink($histories, 'last_crawled', 'Links after', ['filtered' => $filtered]) !!}
    </th>
    <th>
      {!! \App\Colombo\PageHelper::sortLink($histories, 'links_added', 'Links added', ['filtered' => $filtered]) !!}
    </th>
    <th>
      {!! \App\Colombo\PageHelper::sortLink($histories, 'stack_count', 'Stack before', ['filtered' => $filtered]) !!}
    </th>
    <th>
      {!! \App\Colombo\PageHelper::sortLink($histories, 'stack_reset_count', 'Reset stack link', ['filtered' => $filtered]) !!}
    </th>
    <th>
      Stop conditions
    </th>
    <th>
      {!! \App\Colombo\PageHelper::sortLink($histories, 'status', 'Status', ['filtered' => $filtered]) !!}
    </th>
    <th>
      {!! \App\Colombo\PageHelper::sortLink($histories, 'attempts', 'Attempts', ['filtered' => $filtered]) !!}
    </th>
    <th>
      {!! \App\Colombo\PageHelper::sortLink($histories, 'priority', 'Priority', ['filtered' => $filtered]) !!}
    </th>
    <th>
      {!! \App\Colombo\PageHelper::sortLink($histories, 'reset_at', 'Reset at', ['filtered' => $filtered]) !!}
    </th>
    <th>
      Crawl Task
    </th>
    <th>
      Predict
    </th>
    <th>
      Result
    </th>
  </tr>
  @foreach($histories as $history)
    <tr>
      <td>{{$history->id}}</td>
      <td>{{$history->created_at->diffForHumans()}}</td>
      <td>
        <a href="{{route('site.run_history', ['site' => $history->site->id])}}">
          [{{$history->site->id}}]{{$history->site->domain}}
        </a>
      </td>
      <td>{{$history->crawled_links}}</td>
      <td>{{$history->last_crawled}}/{{$history->site->crawled_links}}</td>
      <td class="text-danger font-weight-bold">
        {{$history->crawled_added}}
      </td>
      <td>{{$history->downloadable_links}}</td>
      <td>{{$history->last_downloadable}}/{{$history->site->added_links}}</td>
      <td class="text-danger font-weight-bold">
        {{$history->links_added}}
      </td>
      <td>{{$history->stack_count}}</td>
      <td>{{$history->stack_reset_count}}</td>
      <td><pre>{{implode(",",array_get($history->stop_conditions, 'reset'))}}</pre></td>
      <td class="text-danger font-weight-bold">
        @can('active_run_history', $history->site)
          @if($history->status == 1)
            {{$history->status_text}}
            @else
            <a href="{{route('site.active_run_history', ['site' => $history->site->id])}}"> {{$history->status_text}} </a>
            @endif
          @else
          {{$history->status_text}}
        @endcan
      </td>
      <td>{{$history->attempts}}</td>
      <td>{{$history->priority}}</td>
      <td>{{$history->reset_at ?: "Computing"}}</td>
      <td>{{$history->crawl_status}}</td>
      <td>{{$history->predict}}</td>
      <td>{{$history->result}}</td>
    </tr>
  @endforeach
</table>