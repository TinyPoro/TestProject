@extends('layouts.master')
@section('content')
    <div class="container-fluid">
        <div>
            {!! Former::inline_open()->method('get')->addClass('form-sm') !!}
            {!! Former::text('filter_time')->value($time_range['start'] . ' - ' . $time_range['end'])->class('form-control date-range-picker')->placeholder('Chọn thời gian') !!}
            {!! Former::select('filter_site')->options(['' => 'Filter site', 1 => 'All Site', 2 => 'Google Site', 3 => 'Not Google Site'])->id('filter_site') !!}
            {!! Former::select('filter_verify')->options(['' => 'All verify status', 1 => 'Verified', -1 => 'UnVerified', 0 => 'Confuse'])->id('filter_verify') !!}
            {!! Former::button('Filter')->type('Submit')->addClass('btn-primary') !!}
            {!! Former::close() !!}
        </div>

        Showing report from <b>{{$time_range['start']}}</b> to <b>{{$time_range['end']}} ({{$filter_status}})</b>
        <div style="padding-top: 10px;">
            <div class="table-responsive">
                <table class="table table-bordered table-report table-hover">
                    <tr>
                        <th>Name</th>
                        <th>Sites</th>
                        <th>Sites ready</th>
                        <th>Sites ready (links >=1)</th>
                        <th>Sites ready (links >={{config('crawler.min_link')}})</th>
                        <th>Sites ready (links <{{config('crawler.min_link')}})</th>
                        <th>Links</th>
                        <th>Ratio</th>
                    </tr>
                    @foreach($users as $user)
                        <tr>
                            <td>
                                <a href="{{ route('report.user.index', ['id' => $user->id]) }}" target="_blank">
                                    {{ $user->name }}
                                </a>
                            </td>
                            <td>
                                {{ $user->sites_number }}
                            </td>
                            <td>
                                {{ $user->sites_ready_number }}
                            </td>
                            <td>
                                {{ $user->sites_ready_number_ten }}
                            </td>
                            <td>
                                {{ $user->sites_ready_min_five }}
                            </td>
                            <td>
                                {{ $user->sites_ready_less_five }}
                            </td>
                            <td>
                                {{ $user->links_number }}
                            </td>
                            <td>
                                {{ $user->ratio_sites_links }}
                            </td>
                        </tr>
                    @endforeach
                </table>
            </div>
        </div>


    </div>
    @endsection
@section('scripts')
    <script type="text/javascript">
        $(function() {
            $('input[name="filter_time"]').daterangepicker({
                autoUpdateInput: false,
                locale: {
                    format: 'YYYY-MM-DD',
                    cancelLabel : 'Clear'
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