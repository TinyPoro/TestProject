<!DOCTYPE html>
<html>
<head>
  <meta charset="UTF-8">
  <title>InfyOm Generator</title>
  <meta content='width=device-width, initial-scale=1, maximum-scale=1, user-scalable=no' name='viewport'>

  <link rel="stylesheet" href="{{url('css/app.css')}}">
  <link rel="stylesheet" href="{{url('assets/plugins/fa/css/font-awesome.min.css')}}">
  <link rel="stylesheet" href="{{url('assets/plugins/daterangepicker/daterangepicker.css')}}">
  <meta name="csrf-token" content="{{ csrf_token() }}">
  @yield('css')
  <script>
    function sure(msg) {
        msg = msg || "Sure ?";
        var ok = confirm(msg);
        if(ok === false){
            return false;
        }
    }
  </script>
</head>
<body>
@include('partials.navigation')
@include('vendor.flash.message')
{{--<div class="{{$wrap_class or 'container'}}">--}}
  @yield('content')
{{--</div>--}}

<!-- jQuery 3.1.1 -->
<script src="{{url('assets/plugins/jquery.js')}}"></script>
<script src="{{url('assets/plugins/axios.js')}}"></script>
<script type="text/javascript">
  $.ajaxSetup({
    headers: {
      'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
    }
  });
  axios.defaults.headers.common['X-CSRF-TOKEN'] = $('meta[name="csrf-token"]').attr('content');
</script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/tether/1.4.0/js/tether.min.js"
        integrity="sha384-DztdAPBWPRXSA/3eYEEUWrWCy7G5KFbe8fFjk5JAIxUYHKkDx6Qin1DkWx51bBrb"
        crossorigin="anonymous"></script>
<script src="{{url('assets/plugins/bootstrap/js/bootstrap.min.js')}}"></script>
<script src="{{url('assets/plugins/notify/bootstrap-notify.min.js')}}"></script>
<script src="{{url('assets/plugins/bootbox.min.js')}}"></script>
<script src="{{url('assets/plugins/daterangepicker/moment.min.js')}}"></script>
<script src="{{url('assets/plugins/daterangepicker/daterangepicker.js')}}"></script>
@yield('scripts')

<script>
    $('#flash-overlay-modal').modal();
</script>

</body>
</html>