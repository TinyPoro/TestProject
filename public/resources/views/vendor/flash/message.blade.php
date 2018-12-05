@foreach ((array) session('flash_notification') as $message)
    @if ($message[0]['overlay'])
        @include('flash::modal', [
            'modalClass' => 'flash-modal',
            'title'      => $message[0]['title'],
            'body'       => $message[0]['message']
        ])
    @else
        <div class="alert
                    alert-{{ $message[0]['level'] }}
                    {{ $message[0]['important'] ? 'alert-important' : '' }}"
        >
            @if ($message[0]['important'])
                <button type="button"
                        class="close"
                        data-dismiss="alert"
                        aria-hidden="true"
                >&times;</button>
            @endif

            {!! $message[0]['message'] !!}
        </div>
    @endif
@endforeach

{{ session()->forget('flash_notification') }}
