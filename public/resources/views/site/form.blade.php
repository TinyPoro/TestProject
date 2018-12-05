@if($site->id)
  {!! Former::hidden('id')->id('site_id') !!}
  @endif
{!! Former::url('start_url')->label('Start Url') !!}
<div id="site_list" class="col-lg-10 col-sm-8 offset-lg-2 offset-sm-4"></div>
<div class="row">
  <div class="col-sm-8">
<!--	  --><?php //$former->setType('vertical'); ?>
    {!! Former::setOption('type', 'inline ') !!}
    <h4>Basic</h4>
    {!! Former::select('country')->options(config('country.list'))->id('country')->placeholder("Select a country")->value(config('country.focusing')) !!}
    {!! Former::select('browser_engine')->options(config('crawler.engine'))->value("curl") !!}
    {!! Former::textarea('note')!!}
  </div>
  <div class="col-sm-4" style="display: none;">
	  <?php $former->setType('vertical'); ?>
    <h4>
      Advanced options
      @if($site->id)
        <span class="small">Run {{$site->attempts or 0}} time(s)</span>
      @endif
    </h4>
      {!! Former::text('delay')->label('Delay')->placeholder('Sleep time(second) for each request') !!}
      {!! Former::text('max_step')->label('Max step') !!}
      {!! Former::select('assigned_id')->options($users)->placeholder("Select an user") !!}
      <div class="form-group row">
        <label>
          {!! Former::checkbox('is_auto_restart')->inline()->raw() !!} Enabled
        </label>
      </div>
      <div class="form-group row">
        <label>
          {!! Former::checkbox('pre_render')->inline()->raw() !!} Pre-render when using inspector
        </label>
      </div>
  </div>
</div>


