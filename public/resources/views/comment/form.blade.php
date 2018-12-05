{!! Former::open(route('site.comment.store'))->method('post') !!}
{!! Former::hidden('id')->value($site->id) !!}
<div class="media">
  <div class="media-body">
    {!! Former::textarea("content")->label('Comment') !!}
    {{--<textarea name="content" class="form-control"></textarea>--}}
  </div>
  <div class="d-flex ml-3 align-self-center">
    <button class="btn btn-primary">Save</button>
  </div>
</div>
{!! Former::close() !!}