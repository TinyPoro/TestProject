<div class="col-sm-12">
  <table class="table table-striped table-hover">
    <tr>
      <th>
        Content
      </th>
      <th>
        {!! \App\Colombo\PageHelper::sortLink($notifications, "created_at", 'Created at') !!}
      </th>
    </tr>
    @foreach($notifications as $notification)
      <tr>
        <td class="">
          @if($is_public || auth()->user()->isSuperAdmin() || $notification->notifiable->id == auth()->id())
          <a href="{{route('notifications.detail', ['id' => $notification->id])}}" class="{!! \App\TextFormatter::textClass($notification)  !!}">
          {!! \App\TextFormatter::renderNotificationContent($notification, 100)  !!}
          </a>
            @else
            {!! \App\TextFormatter::renderNotificationContent($notification, 100)  !!}
          @endif
            <i class="fa fa-check-circle-o text-{{$notification->read() ? "success" : "muted"}}"></i>
            @if(auth()->user()->isSuperAdmin())
              <p class="small">to {{$notification->notifiable->email}}</p>
            @endif
        </td>
        <td width="200" class="small">
      @if($is_public && auth()->user()->isSuperAdmin())
          <a href="{{route('notifications.compose', ['id' => $notification->id])}}">Edit</a> / <a href="">Delete</a><br/>
        @endif
        {{$notification->created_at->diffForHumans()}}
          <br>By {!! \App\TextFormatter::getActor($notification)  !!}
        </td>
      </tr>
    @endforeach
  </table>
  <div class="pagination">
    {!! $notifications->appends(['sort' => Request::query('sort')])->render('') !!}
  </div>
</div>