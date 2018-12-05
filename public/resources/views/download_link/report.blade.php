@extends('layouts.master')



@section('content')
    <div class="container-fluid">
        <div>
            {!! Former::inline_open()->method('get')->addClass('form-sm') !!}
            {!! Former::select('country_id')->options(['' => 'All country'] + $countries)->id('country_id') !!}
            {!! Former::text('filter_time')->value($time_range['start'] . ' - ' . $time_range['end'])->class('form-control date-range-picker')->placeholder('Chọn thời gian') !!}
            {!! Former::button('Filter')->type('Submit') !!}
            {!! Former::close() !!}
        </div>
        Showing report from <b>{{$time_range['start']}}</b> to <b>{{$time_range['end']}}</b>
        <div style="padding-top: 10px;">
            <div class="table-responsive">
                <table class="table table-bordered table-report table-hover">
                    <tr>
                        <th>Date</th>
                        <th>First Attempt</th>
                        <th>Get new</th>
                        <th>Downloaded inday document</th>
                        <th>Uploaded inday document</th>
                        <th>Downloaded</th>
                        <th>Uploaded</th>
                    </tr>
                    @if($reports->count() == 0)
                        <p>No report now</p>
                    @else
                        @foreach($reports as $report)
                            <tr>
                                <td>
                                    {{ $report->date }}
                                </td>
                                <td>
                                    <a href="{{route('download_link.index', ['site'=>'all'])}}?daterange_crawl={{$report->date}}+-+{{$report->date}}&attemp=first" target="_blank">
                                        {{ $report->first_attempt }}
                                    </a>
                                </td>
                                <td>
                                    <a href="{{route('download_link.index', ['site'=>'all'])}}?daterange_crawl={{$report->date}}+-+{{$report->date}}&attemp=new" target="_blank">
                                        {{ $report->get_new }}
                                    </a>
                                </td>
                                <td>
                                    <a href="{{route('download_link.index', ['site'=>'all'])}}?filter_download=1&daterange_download={{$report->date}}+-+{{$report->date}}&daterange_crawl={{$report->date}}+-+{{$report->date}}" target="_blank">
                                        {{ $report->inday_download }}
                                    </a>
                                </td>
                                <td>
                                    <a href="{{route('download_link.index', ['site'=>'all'])}}?filter_upload=1&daterange_upload={{$report->date}}+-+{{$report->date}}&daterange_crawl={{$report->date}}+-+{{$report->date}}" target="_blank">
                                        {{ $report->inday_upload }}
                                    </a>
                                </td>
                                <td>
                                    <a href="{{route('download_link.index', ['site'=>'all'])}}?filter_download=1&daterange_download={{$report->date}}+-+{{$report->date}}" target="_blank">
                                        {{ $report->download }}
                                    </a>
                                </td>
                                <td>
                                    <a href="{{route('download_link.index', ['site'=>'all'])}}?filter_upload=1&daterange_uploadload={{$report->date}}+-+{{$report->date}}" target="_blank">
                                        {{ $report['upload'] }}
                                    </a>
                                </td>
                            </tr>
                        @endforeach
                    @endif
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