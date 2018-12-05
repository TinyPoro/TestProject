@extends('layouts.master')

@section('css')
  <link rel="stylesheet" href="{{url('assets/plugins/vis/vis.min.css')}}">
  <link rel="stylesheet" href="{{url('assets/plugins/animate.css')}}">
  <link rel="stylesheet" href="{{url('assets/plugins/vis/vis-network.min.css')}}">
  {{--<link rel="stylesheet" href="{{url('assets/plugins/jquery_modal/jquery.modal.css')}}">--}}
  <link rel="stylesheet" href="{{url('css/bas.css')}}">
  <style type="text/css">
    /*body{font-size: 0.8em;}*/
  </style>
  @endsection

@section('scripts')
  <script type="text/javascript">
    @foreach($js as $k => $var)
      @if(is_array($var))
      var {{$k}} = {!! json_encode($var) !!};
      @else
      var {{$k}} = '{{$var}}';
    @endif
    @endforeach
  </script>

  <script src="{{url('assets/plugins/vis/vis.js')}}"></script>
  <script src="{{url('assets/plugins/bootbox.min.js')}}"></script>
  {{--<script src="{{url('assets/plugins/superselector.js')}}"></script>--}}
  <script src="{{url('assets/plugins/getDomPath.js')}}"></script>
  {{--<script src="{{url('assets/plugins/jquery_modal/jquery.modal.js')}}"></script>--}}
  <script>
      $.notifyDefaults({
          delay: 1000,
          timer: 500,
          animate : {
              exit : 'fadeOutUp'
          }
      });
  </script>
  <script src="{{url('assets/plugins/crawler_builder.js')}}"></script>
  <script>
    function go_position(position) {
        var iframe = $('#site_preview_iframe');
        var height;
        if (position === 'to_top'){
            height = 0;
        }
        else if (position === 'to_bottom'){
            height = $(document).height();
            var height_iframe_document = iframe[0].contentWindow.document.body.offsetHeight;
            console.log(height_iframe_document);
            iframe.contents().find('body').animate({ scrollTop: height_iframe_document }, 100);
        }
        else if (position === 'to_content'){
            height = $(document).height() - iframe.height();
            iframe.contents().find('body').animate({ scrollTop: 0 }, 100);
        }
        $('html,body').animate({
            scrollTop: height
        }, 800);
        return false;
    }
    
    function saveBrowserEngine(select) {
        var confirm = window.confirm('Do you want change ?');
        if (confirm){
            select.form.submit()
        }else{
            return false;
        }

    }
  </script>
  @endsection


@section('content')
  <div class="area-fixed" style="">
    <div class="img-to-position">
      <a href="#" id="to_top" onclick="go_position('to_top')">
        <i class="fa fa-angle-double-up" aria-hidden="true"></i>
      </a>
    </div>
    <div class="img-to-position" onclick="go_position('to_content')">
      <a href="#">
        <i class="fa fa-angle-up" aria-hidden="true"></i>
      </a>
    </div>
    <div class="img-to-position">
      <a href="#" onclick="go_position('to_bottom')">
        <i class="fa fa-angle-down" aria-hidden="true"></i>
      </a>
    </div>
  </div>
  <div class="container-fluid" id="crawler_builder">
    <div class="row">
      <div class="col-md-6 col-sm-12" id="">
        <h4>Site {{$site->start_url}}
          <a class="btn btn-sm btn-default" href="{{$site->start_url}}" target="_blank"><i class="fa fa-external-link"></i></a>
        </h4>
        <div class="pb-2">
          @can('manage', $site)
            <button class="btn btn-sm btn-primary" id="change_status_ready"><i class="fa fa-check"></i> Ready for task</button>
            <button class="btn btn-sm btn-primary" id="change_status_edit"><i class="fa fa-check"></i> Need edit</button>
          @endcan
          @can('edit_rules', $site)
            <button class="btn btn-sm btn-primary" id="change_status_check"><i class="fa fa-bullhorn"></i> Notify to admin</button>
          @endcan
            <a target="_blank" class="btn btn-sm btn-primary" href="{{route('site.comment', ['site' => $site->id])}}"><i class="fa fa-comment-o"></i></a>
        </div>
        <div id="graph_viewer_wrap">
          <div id="graph_viewer" style="height: 400px; border: 1px solid #ccc;">
          </div>
          <i class="fa fa-refresh" id="redraw_graph" title="redraw graph"></i>
        </div>
      </div>
      <div class="col-md-6 col-sm-12">
        <div class="" id="info-and-editor" data-id="" style="">
          <ul class="nav nav-tabs" role="tablist">
            <li class="nav-item">
              <a class="nav-link" data-toggle="tab" href="#form_site_info" role="tab">Site info</a>
            </li>
            <li class="nav-item">
              <a class="nav-link" data-toggle="tab" href="#node_viewer" role="tab">Node info</a>
            </li>
            <li class="nav-item">
              <a class="nav-link active" data-toggle="tab" href="#node_editor" role="tab">Node editor</a>
            </li>
            @include('site._drop_navigation', compact('site'))
          </ul>

          <!-- Tab panes -->
          <div class="tab-content">
            <div class="tab-pane pt-3" id="form_site_info" role="tabpanel">
              @include('crawler._site_info', compact('site'))
            </div>
            <div class="tab-pane pt-3" id="node_viewer" role="tabpanel"><div id="step_info">Select one node to see what will be happened</div></div>
            <div class="tab-pane active pt-3" id="node_editor" role="tabpanel">
              <form id="step_info_form" class="">
                @macro('bootstrap_input', $type, $field, $label = "", $opts = [])
                <div class="form-group">
                  @if($label)
                  <label for="step_id" class="control-label  font-weight-bold">{{$label}}</label>
                  @endif
                  {{--<input type="text" class="" id="step_id" name="step_id" value="" />--}}
                  @if($type == 'select')
                    {!! Form::select($field, array_get($opts, 'list', []), '', array_except($opts, 'list') + ['id' => $field, 'class' => 'form-control']) !!}
                  @elseif($type == 'checkbox')
                      {!! Form::checkbox($field, 'multiple', null,$opts + ['id' => $field, 'class' => '']) !!}
                  @else
                    {!! Form::$type($field, '', $opts + ['id' => $field, 'class' => 'form-control']) !!}
                  @endif
                </div>
                @endmacro
                <div class="row">
                  <div class="col-sm-12 col-md-6">
                    {!! Html::bootstrap_input('select',
                  'step_type',
                  'Type',
                  ['list' => config('crawler.types'),
                    'placeholder' => 'Type'
                    ])
                  !!}
                    {!! Html::bootstrap_input('checkbox', 'step_multiple', 'Multiple?') !!}
                    {!! Html::bootstrap_input('select', 'step_parent_selectors', 'Parents', ['list' => [],
                      'multiple' => true]) !!}
                    {!! Html::bootstrap_input('text', 'step_delay', 'Delay', ['placeholder' => 'Blank for using site default setting']) !!}
                  </div>
                  <div class="col-sm-12 col-md-6">
                    {!! Html::bootstrap_input('text', 'step_title', 'Title') !!}
                    {!! Html::bootstrap_input('text', 'step_id', null, ['readonly' => true, 'placeholder' => 'Auto generated field']) !!}
                    {{--{!! Html::bootstrap_input('text', 'step_test_url', 'Test url') !!}--}}
                    <div class="form-group">
                      <label for="step_test_url" class="control-label font-weight-bold">Test url</label>
                      <div class="input-group">
                        <input type="text" id="step_test_url" name="step_test_url" class="form-control" aria-label="Test url">
                        <div class="input-group-btn">
                          <button type="button" class="btn btn-sm btn-secondary" id="step_selector_inspector" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                            <i class="fa fa-magic"></i> Inspector
                          </button>
                        </div>
                      </div>
                    </div>
                    {!! Html::bootstrap_input('textarea', 'step_selector', 'Selector', ['rows' => 3]) !!}
                  </div>
                  <div class="col-sm-12 form-group pt-1">
                    <button class="btn btn-sm btn-primary" id="btn_form_save">
                      <i class="fa fa-save"></i> Save
                    </button>
                    <button class="btn btn-sm btn-default" id="step_selector_test">
                      <i class="fa fa-search"></i> Test selector
                    </button>
                    <label><input type="checkbox" checked id="disable_image" />Disable image</label>
                  </div>
                </div>
              </form>
            </div>
          </div>
        </div>
      </div>
    </div>

    <div class="" id="site_preview" style="min-height: 500px;">

    </div>
    {{--<iframe id="xxx" width="100%" height="1000px" src="{{url('/html?url=https%3A%2F%2Fonnyrudianto.wordpress.com')}}"></iframe>--}}
  </div>
  @endsection