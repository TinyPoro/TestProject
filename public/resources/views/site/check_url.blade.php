@extends('layouts.master')

@section('content')
  <div class="container">
    <h3>URL checker</h3>
    <div class="col-sm-12">
      {!! Former::open(route('site.check_url'))->method('get')->id('site_form') !!}
      {!! Former::input('url') !!}
      <button class="btn btn-sm btn-primary" name="submit" value="edit_rules" type="submit">Check</button>
      {!! Former::close() !!}
    </div>
    @if($download_link)
      <h4>Download link</h4>
      <p>Page title {{$download_link->page_title}}</p>
      <p>Page link {{$download_link->page_link}}</p>
      <p>Link text {{$download_link->link_text}}</p>
      <p>Href {{$download_link->href}}</p>
      <p>Created at {{$download_link->created_at}}</p>
    @endif
    @if($download_links && $download_links->count())
      <h4>Links from this page</h4>
      @foreach($download_links as $dl)
        <p><a class="font-weight-bold">Href <a href="{{$dl->href}}">{{$dl->href}}</a></p>
        <p>Link text {{$dl->link_text}}</p>
        <p>Type {{$dl->filtered}}</p>
        <p>Page title {{$dl->page_title}}</p>
        <p>Page link {{$dl->page_link}}</p>
        <p>Created at {{$dl->created_at}}</p>
      @endforeach
    @endif
    @if($stack_link)
      <h4>Stack</h4>
      <p>URL {{$stack_link->url}}</p>
      <p>SITE KEY {{$stack_link->site_key}}</p>
      <p>PATH RUN {{$stack_link->path_run}}</p>
      <p>STATE {{$stack_link->state}}</p>
      <p>PARENT {{$stack_link->parent}}</p>
      <p>CREATED_AT {{$stack_link->created_at}}</p>
      @if($stack_link_children)
        <p>STACK CHILDREN</p>
        <ul>
          @foreach($stack_link_children as $child)
            <li>[{{$child->id}}] <a href="{{route('site.check_url', ['url' => $child->url])}}">{{$child->url}}</a></li>
          @endforeach
        </ul>
      @endif
      @endif

  </div>
@endsection