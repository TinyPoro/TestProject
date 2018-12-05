@extends('layouts.master')
@section('content')
<div class="container-fluid">
	<div>
		{!! Former::inline_open()->method('get')->addClass('form-sm') !!}
		{!! Former::text('filter_time')->value($time_range['start'] . ' - ' . $time_range['end'])->class('form-control date-range-picker')->placeholder('Chọn thời gian')->autocomplete('off') !!}
		{!! Former::select('filter_user')->options(['' => 'Filter user'] + $users_list)->id('filter_user') !!}
    {!! Former::select('filter_country')->options(['' => 'All countries'] + config('country.list'))->id('filter_country') !!}
		{!! Former::button('Filter')->type('Submit')->addClass('btn-primary') !!}
		{!! Former::close() !!}
	</div>


	<div style="padding-top: 10px;">
    @if(!empty($user_reports))
      <h3>Report for user {{$user->name}}</h3>
      <div class="table-responsive">
        <table class="table table-bordered table-report table-hover">
          <tr>
            <th>Date</th>
            <th>Verified</th>
            <th>Un-Verified</th>
            <th>Total</th>
          </tr>
          @php
            $total_unveried = 0; $total_veried = 0;
          @endphp
          @foreach($user_reports as $report)
            @php
              $total_unveried += $report[1]; $total_veried += $report[2];
            @endphp
            <tr>
              <td align="right">{{$report[0]}}</td>
              <td align="right">{{$report[1]}}</td>
              <td align="right">{{$report[2]}}</td>
              <td align="right">{{$report[1] + $report[2]}}</td>
            </tr>
            @endforeach
          <tr>
            <td><b>Total</b></td>
            <td align="right">{{$total_unveried}}</td>
            <td align="right">{{$total_veried}}</td>
            <td align="right">{{$total_unveried + $total_veried}}</td>
          </tr>
        </table>
      </div>
    @endif
    @if(!empty($users_reports))
      <h3>Report for all active users in {{implode(" - ",$time_range)}}</h3>
      <div class="table-responsive">
        <table class="table table-bordered table-report table-hover">
          <tr>
            <th>Name</th>
            <th>Verified</th>
            <th>Un-Verified</th>
            <th>Total</th>
          </tr>
          @php
            $total_unveried = 0; $total_veried = 0;
          @endphp
          @foreach($users_reports as $report)
            @php
            $total_unveried += $report[1]; $total_veried += $report[2];
            @endphp
            <tr>
              <td>{{$report[0]}}</td>
              <td align="right">{{$report[1]}}</td>
              <td align="right">{{$report[2]}}</td>
              <td align="right">{{$report[1] + $report[2]}}</td>
            </tr>
            @endforeach
          <tr>
            <td><b>Total</b></td>
            <td align="right">{{$total_unveried}}</td>
            <td align="right">{{$total_veried}}</td>
            <td align="right">{{$total_unveried + $total_veried}}</td>
          </tr>
        </table>
      </div>
    @endif

	</div>


</div>
@endsection
@section('scripts')
<script type="text/javascript">

    $(function() {
        $('.date-range-picker').daterangepicker({
            autoUpdateInput: false,
            locale: {
                cancelLabel: 'Clear',
                format: 'YYYY-MM-DD'
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
            $(this).val(picker.startDate.format('YYYY-MM-DD') + ' - ' + picker.endDate.format('YYYY-MM-DD'));
        });

        $('.date-range-picker').on('cancel.daterangepicker', function(ev, picker) {
            $(this).val('');
        });
    });

</script>
@endsection