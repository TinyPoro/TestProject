@extends('layouts.master')
@section('content')
    <div class="col-sm-12">
        <span> Total download: <b>{{ $number_download }}</b></span>
        <span style="padding-left: 50px"> Total upload: <b>{{ $number_upload }}</b></span>
        <span style="padding-left: 50px">Number ready to upload: <b>{{ $number_upload_ready }}</b></span>
    </div>
    <div class="col-sm-12" style="padding-top: 30px">
        <div style="padding-bottom: 20px;">
            {!! Former::inline_open()->method('get') !!}
            {!! Former::select('filter_download')
            ->options(['' => 'Download status'] + $filter_data['download_status']) !!}
            {!! Former::select('filter_upload')->options(['' => 'Upload status'] + $filter_data['upload_status']) !!}
            {!! Former::text('daterange')->class('form-control date-range-picker')->placeholder('Date range')->value('') !!}
            {!! Former::button('Filter')->type('Submit') !!}
            {!! Former::close() !!}
        </div>

        <table class="table table-striped table-hover">
            <tr>
                <th>
                    Time
                </th>
                <th>
                    Downloaded number
                </th>
                <th>
                    Avaiable upload number
                </th>
                <th>
                    Upload number
                </th>
            </tr>
            @foreach($reports as $key => $report)
                <tr>
                    <td>{{$key}}</td>
                    <td>{{ array_get($report, 'download', 0) }}</td>
                    <td>{{ array_get($report, 'avaiable_upload', 0) }}</td>
                    <td>{{ array_get($report, 'upload', 0) }}</td>
                </tr>
            @endforeach
        </table>
    </div>
    @endsection
@section('scripts')
    <script type="text/javascript">
        $(function() {
            $('input[name="daterange"]').daterangepicker({
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