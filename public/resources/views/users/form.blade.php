{!! Former::open(route('user.store'))->method('post') !!}
{!! Former::token() !!}
{!! Former::populate($user) !!}
{!! $user->id ? Former::hidden('id') : '' !!}
{!! Former::text('name') !!}
{!! Former::email('email') !!}
{!! Former::password('password')->forceValue('') !!}
@can('change_group', $user)
  {!! Former::select('group')->options($groups)->placeholder('Group') !!}
  @endcan
{!! Former::primary_button('Save')->type('submit') !!}
{!! Former::primary_link('Back')->href(back()->getTargetUrl()) !!}
{!! Former::close() !!}

