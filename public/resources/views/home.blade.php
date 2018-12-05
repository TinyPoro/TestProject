@extends('layouts.master')

@section('scripts')
  <script>
    var refresh_url = '{{route('site.get_info')}}';
    var update_tasks_url = '{{route('site.tasks', ['site' => ''])}}';
    var refresh_crawl_url = '{{route('site.refresh_crawl')}}';
    var get_json_url = '{{route('site.get_json', ['site' => '__id__'])}}';
    var delete_site_url = '{{route('site.delete')}}';
    var restore_site_url = '{{route('site.restore')}}';
  </script>
  <script src="{{url('assets/plugins/vis/vis.js')}}"></script>
  <script src="{{url('assets/plugins/bootbox.min.js')}}"></script>
  {!! Html::script("assets/plugins/graph_preview.js") !!}
  {!! Html::script("assets/plugins/site_list.js") !!}

  <script type="text/javascript">
      $(function() {
          $('.date-range-picker').daterangepicker({
              autoUpdateInput: false,
              locale: {
                  cancelLabel: 'Clear'
              },
              ranges: {
                  'Today': [moment(), moment()],
                  'Yesterday': [moment().subtract(1, 'days'), moment().subtract(1, 'days')],
                  'Last 7 Days': [moment().subtract(6, 'days'), moment()],
                  'Last 30 Days': [moment().subtract(29, 'days'), moment()],
                  'This Month': [moment().startOf('month'), moment().endOf('month')],
                  'Last Month': [moment().subtract(1, 'month').startOf('month'), moment().subtract(1, 'month').endOf('month')]
              }
          });

          $('.date-range-picker').on('apply.daterangepicker', function(ev, picker) {
              $(this).val(picker.startDate.format('MM/DD/YYYY') + ' - ' + picker.endDate.format('MM/DD/YYYY'));
          });

          $('.date-range-picker').on('cancel.daterangepicker', function(ev, picker) {
              $(this).val('');
          });
      });
  </script>
  @endsection
@section('content')
  <div class="container-fluid">
    <h3>Sites list</h3>
    {!! Former::inline_open()->method('get')->addClass('form-sm') !!}
    {!! Former::text('site_search')->placeholder('ID/Url......') !!}
    {!! Former::select('filter_country')->options(['' => 'All countries'] + config('country.list'))->id('filter_country') !!}
    {!! Former::select('filter_verified')->options(['' => 'Verified status', 0 => 'Confuse', 1 => 'Verified', -1 => 'Un-Verified'])->id('filter_verified') !!}
    {!! Former::select('verified_by')->options(['' => 'Verified by'] + $users_list)->id('verified_by') !!}
    {!! Former::text('filter_verified_at')->addClass('date-range-picker')->placeholder('Verified at')!!}
    {!! Former::select('filter_creator')->options(['' => 'All creators'] + $users_list)->id('filter_creator') !!}
    {!! Former::select('filter_assignee')->options(['' => 'All assignees'] + $users_list)->id('filter_assignee') !!}
    {!! Former::select('filter_status')->options(['' => 'All status'] + $site_status)->id('filter_status') !!}
      {!! Former::select('filter_driver')->options(['' => 'All driver', 0 => 'Curl', 1 => 'Phantomjs'])->id('filter_driver') !!}
      {!! Former::select('filter_crawl_status')->options(['' => 'All crawl st', -1 => 'Stop', 0 => 'Wait', 1 => 'Done'
      , 100 => 'Running', 101 => 'Rerun', 110 => 'Fail'])->id('filter_crawl_status') !!}
    {!! Former::text('filter_time')->addClass('date-range-picker')->placeholder('Created date range')!!}
    {!! Former::text('filter_utime')->addClass('date-range-picker')->placeholder('Updated date range')!!}
    {!! Former::select('reason_id')->options(['' => 'All reason'] + $reasons)->id('reason_id') !!}
    {!! Former::button('Filter')->type('Submit')->addClass('btn-info') !!}
    @can('create', \App\Models\Site::class)
      {!! Former::primary_button_link('Add')->href(route('site.create')) !!}
    @endcan
    {!! Former::close() !!}
    {!! Former::inline_open()->method('get')->addClass('form-sm') !!}
    {!! Former::select('filter_task_name')->options(['' => 'Task name'] + $tasks_name_list) !!}
    {!! Former::select('filter_task_status')->options(['' => 'Task status'] + $tasks_status_list) !!}
    {!! Former::button('Filter')->type('Submit')->addClass('btn-info') !!}
    {!! Former::close() !!}
    <div class="row pt-2">
      <div class="col-sm-12">
        <div class="row">
          <div class="col-sm-2">
            Sites number : {{ $counter['sites_number'] }}
          </div>
          <div class="col-sm-3">
            Crawled links number : {{ $counter['crawled_links_number'] }}
          </div>
        </div>

      </div>
      <div class="col-sm-12 small">
        <input type="checkbox" id="auto_refresh_enabled" checked/>
        Auto refresh each <input type="text" id="auto_refresh_time" style="width: 20px;" value="5"/> second(s)
      </div>
      <div class="table-responsive">
        @include('includes.sites', ['sites' => $all_sites, 'no_response' => 'col-sm-12'])
      </div>
    </div>
    <div class="d-flex flex-row-reverse">
      {!! $all_sites->appends($querying)->render()  !!}
      <i class="small mr-2 pt-2 pb-2">
        Showing {{$all_sites->firstItem()}} to {{$all_sites->lastItem()}} of {{$all_sites->total()}}
      </i>
    </div>
  </div>
@endsection
