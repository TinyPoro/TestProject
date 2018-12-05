<div>
  <p>Start url : {{$site->start_url}}</p>
  <p>Created by : {{$site->created_user->name or "Unknown"}}</p>
  <p>Assigned to : {{$site->assigned_user->name or "Unknown"}}</p>
  <p>Max steps : {{$site->max_step ?: "No limit"}}</p>
  <p>Delay : {{$site->delay or "0"}} s</p>
  @if($site->pre_render)
    <p class="text-warning">Using Pre-render for inspector</p>
    @endif
  <form method="post" action="{{ route('site.change.browser_engine') }}">
    <input type="hidden" name="id" value="{{ $site->id }}">
    {{ csrf_field() }}
    <div class="form-group row">
      <label class="col-form-label col-lg-4 col-sm-4">Browser engine</label>
      <div class="col-lg-6 col-sm-6">
        <select class="form-control" onchange="saveBrowserEngine(this)"  name="browser_engine">
          <option @if($site->browser_engine == 'phantomjs' && $site->pre_render) selected @endif value="phantomjs:1">Phantomjs with Pre-render</option>
          <option @if($site->browser_engine == 'phantomjs' && !$site->pre_render) selected @endif value="phantomjs">Phantomjs</option>
          <option @if($site->browser_engine == 'curl') selected @endif value="curl">Curl</option>
        </select>
      </div>
    </div>
  </form>

  <p class="font-weight-bold">Downloadable link filters</p>
  <ul>
    <li>Enabled Ignore filter</li>
    <li>Enabled Short link filter</li>
    <li>Enabled Google driver filter</li>
    <li>Enabled Mediafire filter</li>
    <li>Enabled BoxCom filter</li>
    <li>
      <form method="post" action="{{route('site.change_filter')}}">
        <input type="hidden" name="id" value="{{ $site->id }}">
        {{ csrf_field() }}
        <label for="internal_link_filter">Internal Link Filter</label>
        {!! Form::checkbox('internal_link_filter',($site->internal_link_filter) ? false :true,($site->internal_link_filter)? true : false,['onClick'=>'this.form.submit()'])!!}
      </form>
    </li>
  </ul>
</div>
