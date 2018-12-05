@extends('layouts.master')



@section('content')
  <div class="container-fluid">
    @includeWhen($site, 'site._navigation', ['site' => $site])
    <div class="page-header">
      @if($site)
        <?php $site_id = $site->id; ?>
      <h3 class="page-header">Site <a href="{{$site->start_url}}">{{$site->start_url}}</a></h3>
        @else
		    <?php $site_id = 'all'; ?>
        <h3>All download links</h3>
      @endif
      <div class="d-flex">
        <div class="">
          <a href="{{ route('report.index') }}" class="btn btn-sm btn-info">Show report</a>
        </div>
          @if($site && auth()->user()->can('verify', $site))
          <div class="pl-2">
            {!! Former::inline_open(route('site.verify'), 'POST')->addClass('form-sm') !!}
            {!! Former::hidden('id')->value($site->id) !!}
            {!! Former::submit("Verify")
            ->type('Submit')
            ->name('verified')
            ->value('Verify')
            ->addClass("btn btn-danger")
            ->onclick("return confirm('Really want to verify this site ?');")
            !!}
            <a id="un_verify_button" class="btn-sm btn-danger text-bold text-white" href="javascript:void(0);" data-id="{{$site->id}}">UnVerify</a>
            {!! Former::close() !!}
          </div>
          {!! $site->verifyStatusText('2x') !!}
          @endif
      </div>
      @if($site && $site->verified == -1 && $site->reasons && $site->reasons->count() > 0)
        <div class="d-flex">
          <ul class="list-unstyled">
            <li class="text-danger"> {{ $site->reasons->implode("content", "/ ") }} </li>
          </ul>
        </div>
        @endif
    </div>
    <div class="text-center">

      <a href="{{route('download_link.index', ['site' => $site_id, 'type' => 'all'])}}"
         class="{{$type == "all" ? "text-danger" : ""}}">All</a> |
      <a href="{{route('download_link.index', ['site' => $site_id, 'type' => 'downloadable'])}}"
         class="{{$type == "downloadable" ? "text-danger" : ""}}">Downloadable</a> |
      <a href="{{route('download_link.index', ['site' => $site_id, 'type' => 'common'])}}"
         class="{{$type == "common" ? "text-danger" : ""}}">Common</a>
    </div>
    <div>
      {!! Former::inline_open()->method('get')->addClass('form-sm') !!}
      {!! Former::text('site_search')->placeholder('ID...') !!}
      {!! Former::select('country_id')->options(['' => 'All country'] + $countries)->id('country_id') !!}
      {{--    {!! Former::text('daterange')->class('form-control date-range-picker') !!}--}}
      {!! Former::select('filter_filtered')->options(['' => 'All filtered'] + $filter_texts) !!}
      {!! Former::select('filter_download')->options(['' => 'Download status'] + $filter_data['download_status']) !!}
      {!! Former::text('daterange_download')->class('form-control date-range-picker')->placeholder('Date range')
       ->setAttributes('disabled')!!}
      {!! Former::select('filter_upload')->options(['' => 'Upload status'] + $filter_data['upload_status']) !!}
      {!! Former::text('daterange_upload')->class('form-control date-range-picker')
      ->placeholder('Date range')->setAttributes('disabled')!!}
      {!! Former::text('attempts_range')->placeholder('attempts range') !!}
      {!! Former::button('Filter')->type('Submit') !!}
      {!! Former::close() !!}
    </div>
    <div>
      <span>Number: {{ $number }}</span>
    </div>
    <div class="table-responsive">
    <table class="table table-bordered table-report table-hover">
      <tr>
        <th>
          {!! \App\Colombo\PageHelper::sortLink($dls, 'download_links.id', 'ID', ['filtered' => $filtered]) !!}
        </th>
        <th>
          {!! \App\Colombo\PageHelper::sortLink($dls, 'download_links.filtered', 'Filter', ['filtered' => $filtered]) !!}
        </th>
        <th>
          Source
        </th>
        <th>
          Link
        </th>
        <th>
          {!! \App\Colombo\PageHelper::sortLink($dls, 'download_links.name', 'File name', ['filtered' => $filtered]) !!}
        </th>
        <th style="min-width: 70px;">
          {!! \App\Colombo\PageHelper::sortLink($dls, 'download_links.size', 'Size', ['filtered' => $filtered]) !!}
        </th>
        <th>
          {!! \App\Colombo\PageHelper::sortLink($dls, 'download_links.disk', 'Disk', ['filtered' => $filtered]) !!}
        </th>
        <th>
          {!! \App\Colombo\PageHelper::sortLink($dls, 'download_links.status', 'Downloaded', ['filtered' => $filtered]) !!}
        </th>
        <th>
          {!! \App\Colombo\PageHelper::sortLink($dls, 'download_links.uploaded_at', 'Uploaded', ['filtered' => $filtered]) !!}
        </th>
        <th>
          Document
        </th>
        <th>
          {!! \App\Colombo\PageHelper::sortLink($dls, 'download_links.updated_at', 'Updated', ['filtered' => $filtered]) !!}
        </th>
        <th>
          {!! \App\Colombo\PageHelper::sortLink($dls, 'download_links.attempts', 'Attempts', ['filtered' => $filtered]) !!}
        </th>
      </tr>
      @foreach($dls as $dl)
        <tr>
          <td>
            {{strtoupper($dl->site ? $dl->site->country : 'Deleted') . $dl->id}}
          </td>
          <td>
            {{$dl->filtered}}
          </td>
          <td>
            <a href="{{$dl->page_link}}" target="_blank">{{$dl->short_source_title}}</a>
          </td>
          <td>
            @php
              $link = $dl->href;
              if(strpos($link, "//") === false && $dl->page_link){
                $link = \App\Helpers\PhpUri::parse($dl->page_link)->join($link);
              }
            @endphp
            <a href="{{$link}}" target="_blank">{{$dl->link_title}}</a>
          </td>
          <td>
              {{$dl->name}}
            <br/><i class="text-muted">{{$dl->title_resolved}}</i>
              @if(isset($dl->title))
                @if($dl->title == 'null')
                      <br/><b class="text-muted">NULL</b>
                  @else
                      <br/><b class="text-muted">{{$dl->title}}</b>
                @endif
              @endif
          </td>
          <td style="text-align: right">
            {{\App\Helpers\Utils::formatBytes($dl->size)}}
          </td>
          <td>
            @if($dl->disk)
              <a href="{{route('download_link.download', ['id' => $dl->id])}}">
                <span title="{{$dl->path}}" class="badge badge-info">{{$dl->disk}}</span>
              </a>
              @else
              --
            @endif
          </td>
          <td>
            @if($dl->status == 1)
              {{ $dl->downloaded_at }}
              @else
              {{$dl->status_text}}
              @endif

          </td>
          <td>
            @if($dl->upload_status == 1)
              {{$dl->uploaded_at}}
              @else
              {{$dl->upload_status}}
            @endif
          </td>
          <td>
            @if($dl->upload_status == 1 || $dl->upload_status == -2)
            <a href="{{$dl->uploaded_link}}" target="_blank">{{str_limit($dl->uploaded_document_title, 60)}}</a>
            @endif
          </td>
          <td>
            {{$dl->updated_at}}
          </td>
          <td>
            {{$dl->attempts}}
          </td>
        </tr>
        @endforeach
    </table>
  </div>
    <div>
      {{$dls->appends($filtered+\App\Colombo\PageHelper::getSort())->render()}}
    </div>
  </div>

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
          if ($('#filter_download').val() != ''){
              $('#daterange_download').removeAttr('disabled');
          }
          $('#filter_download').change(function () {
              $('#daterange_download').removeAttr('disabled');
          });
          if ($('#filter_upload').val() != ''){
              $('#daterange_upload').removeAttr('disabled');
          }
          $('#filter_upload').change(function () {
              $('#daterange_upload').removeAttr('disabled');
          });
          $('#un_verify_button').click(function (e) {
              var id = $(this).data('id');
              open_un_verify_box(id)
          });
      });
  </script>
  @endsection