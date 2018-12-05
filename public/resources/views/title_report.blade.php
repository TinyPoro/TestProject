@extends('layouts.master')



@section('content')
    <div class="container-fluid">
        <div>
            {!! Former::inline_open()->method('get')->addClass('form-sm') !!}
            {!! Former::text('daterange_upload')->class('form-control date-range-picker')
            ->placeholder('Date range')->setAttributes('disabled')!!}
            {!! Former::button('Lọc')->type('Submit') !!}
            {!! Former::close() !!}
        </div>

        <h4>Thống kê download link</h4>
        <div class="table-responsive">
            <table class="table table-bordered table-report table-hover">
                <tr>
                    <th>#</th>
                    <th>Nội dung</th>
                    <th>Số lượng</th>
                </tr>
                @foreach($reports as $report)
                    <tr>
                        <th scope="row"></th>
                        <td align="center">{{$report['content']}}</td>
                        <td align="center">{{$report['count']}}</td>
                    </tr>
                @endforeach
            </table>
        </div>

        <h4>Thống kê code lỗi upload</h4>
        <i>(200: thành công, 100 : Không có title, 400: lỗi upload, 444 : Trùng tài liệu )</i>
        <div class="table-responsive">
            <table class="table table-bordered table-report table-hover">
                <tr>
                    <th>#</th>
                    <th>Nội dung</th>
                    <th>Số lượng</th>
                </tr>
                @foreach($upload_err_codes as $upload_err_code)
                    <tr>
                        <th scope="row"></th>
                        <td align="center">{{$upload_err_code->upload_err_code}}</td>
                        <td align="center">{{$upload_err_code->count}}</td>
                    </tr>
                @endforeach
            </table>
        </div>
    </div>

@endsection
@section('scripts')
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