@extends('layouts.master')

@section('scripts')
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
    <h3>Run histories</h3>
    {!! Former::inline_open()->method('get')->addClass('form-sm') !!}
    {!! Former::select('filter_country')->options(['' => 'All countries'] + config('country.list'))->id('filter_country') !!}
    {!! Former::select('filter_status')->options(['' => 'All Status'] + \App\Models\RunHistory::all_status_texts())->id('filter_status') !!}
    {!! Former::text('filter_time')->addClass('date-range-picker')->placeholder('Created at')!!}
    {!! Former::select('filter_reset_option')->options(['' => 'Reset in range', 'future' => 'Reset on future in range', 'pass' => 'Reset on pass in range']) !!}
    {!! Former::text('filter_reset_time')->addClass('date-range-picker')->placeholder('Date range')!!}
      {!! Former::select('crawl_task_status')->options(['' => 'Crawl task status', '1' => 'Done']) !!}
    {!! Former::button('Filter')->type('Submit')->addClass('btn btn-primary') !!}
    {!! Former::close() !!}
    <div class="row pt-2">
      <div>
        <i class="small mr-2 pt-2 pb-2">
          Showing {{$histories->firstItem()}} to {{$histories->lastItem()}} of {{$histories->total()}}
        </i>
          <span> {{$predicted_success}}/{{$predicted_fail}} success/fail</span>
      </div>
      @include('includes.histories', ['histories' => $histories, 'filtered' => $filtered])
    </div>
    <div class="d-flex flex-row-reverse">
      {!! $histories->appends($querying)->render()  !!}
    </div>
  </div>
@endsection
