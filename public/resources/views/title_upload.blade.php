@extends('layouts.master')



@section('content')
    <div class="container-fluid">
        <div>
            {!! Former::inline_open()->method('get')->addClass('form-sm') !!}
            {!! Former::text('dl_id')->addClass('Dl_Id')->placeholder('Dl Id') !!}
            {!! Former::text('site_id')->addClass('Site_ID')->placeholder('Site Id') !!}
            {!! Former::text('title')->addClass('title')->placeholder('Title') !!}
            {!! Former::select('gen_title_status')->options(['' => 'Gen title status'] + $gen_title_status) !!}
            {!! Former::select('upload_status')->options(['' => 'Upload status'] + $upload_status) !!}
            {!! Former::text('daterange_upload')->class('form-control date-range-picker')
            ->placeholder('Date range')->setAttributes('disabled')!!}
            {!! Former::button('Lọc')->type('Submit') !!}
            {!! Former::close() !!}
        </div>

        <h3>{{$total_report}} report(s)</h3>

        <div class="table-responsive">
            <table class="table table-bordered table-report table-hover">
                <tr>
                    <th>Id</th>
                    <th>Downloadlink Id</th>
                    <th>Site Id</th>
                    <th>Title</th>
                    <th>Status</th>
                    <th>Reason</th>
                    <th>Upload Status</th>
                    <th>Upload Error</th>
                </tr>
                @foreach($reports as $report)
                    <tr>
                        <td>{{$report->id}}</td>
                        <td><a href="{{route('download_link.index', ['site' => 'all'])}}?site_search={{$report->downloadLink->id}}'" target="_blank">{{$report->download_link_id}}</a></td>
                        <td>{{$report->downloadLink->site_id}}</td>
                        <td>{{$report->downloadLink->meta('title')}}</td>
                        <td><a href="{{route('dl.scores', ['id' => $report->id])}}" target="_blank">{{$report->gen_title_status_text}}</a></td>
                        <td>{{$report->gen_title_error}}</td>
                        <td>{{$report->upload_status_text}}</td>
                        <td>{{$report->upload_err}}</td>
                    </tr>
                @endforeach
            </table>
        </div>
    </div>

    {{ $reports->links() }}

@endsection
@section('scripts')
    <script>
        var link_get_reasons = "{{route('reason.api_list')}}";
        var link_update_reasons = "{{route('site.verify')}}";
    </script>
    <script type="text/javascript" src="{{url('assets/plugins/fuse.js')}}"></script>
    <script type="text/javascript" src="{{url('assets/plugins/site_verify.js')}}"></script>
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
            if ($('#filter_upload').val() != ''){
                $('#daterange_upload').removeAttr('disabled');
            }
            $('#filter_upload').change(function () {
                $('#daterange_upload').removeAttr('disabled');
            });
        });
    </script>
@endsection