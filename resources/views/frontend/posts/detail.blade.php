@extends('frontend.layouts.master')
@section('content')

<div>
    <div class="container">
        <div class="card-body">
            @if( $post->subject )
                <p><strong>Đề bài</strong></p>
                {!! $post->subject !!}
            @endif
            <p><strong>Hướng dẫn giải</strong></p>
            {!! $post->content !!}
        </div>
    </div>
</div>

    @endsection

@section('after-scripts')
    <script src='https://cdnjs.cloudflare.com/ajax/libs/mathjax/2.7.4/MathJax.js?config=TeX-MML-AM_CHTML' async></script>
    <script src="{{ url('frontend/js/detail.post.js') }}"></script>
    @endsection