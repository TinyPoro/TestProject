@extends('layouts.master')
@section('content')
    <div class="col-sm-12">
        <h3>{{ $user->name }}</h3>
        {!! Former::inline_open()->method('get')->addClass('form-sm') !!}
        {!! Former::text('daterange')->class('form-control date-range-picker')->placeholder('ID') !!}

        {!! Former::button('Filter')->type('Submit') !!}
        {!! Former::close() !!}
        <div style="padding-top: 10px;">
            <div class="table-responsive">
                <table class="table table-bordered table-report table-hover">
                    <tr>
                        <th>Time</th>
                        <th>Sites</th>
                        <th>Sites ready</th>
                        <th>Links</th>
                    </tr>
                    @foreach($sites as $site)
                        <tr>
                            <td>
                                {{ date('Y-m-d', strtotime($site->day)) }}
                            </td>
                            <td>
                                {{ $site->sites_number }}
                            </td>
                            <td>
                                {{ $site->sites_ready_number }}
                            </td>
                            <td>
                                {{ $site->links_number }}
                            </td>

                        </tr>
                    @endforeach
                </table>
            </div>
        </div>

        {{ $sites->links() }}
    </div>
@endsection
@section('scripts')
    <script type="text/javascript">
        $(function() {
            $('input[name="daterange"]').daterangepicker({
                locale: {
                    format: 'YYYY-MM-DD'
                }
            });
        });
    </script>
@endsection