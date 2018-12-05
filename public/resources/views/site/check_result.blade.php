<ul class="list-report">
  <li class="text-warning font-weight-bold">May be dupplicated with</li>
  @foreach($sites as $s)
    <li>
      <input type="radio" name="parent_id" value="{{$s->id}}" {{$s->id == $parent_id ? "checked" : ""}} />
      {{$s->start_url}} {{$s->id == $parent_id ? "<Parent site>" : ""}}
      {!! Html::linkRoute('site.edit', '[EDIT]', ['site' => $s->id]) !!}
      {!! Html::linkRoute('site.edit_selectors', '[RULES]', ['site' => $s->id]) !!}
    </li>
    @endforeach
</ul>