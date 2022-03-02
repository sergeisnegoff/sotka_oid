@extends('voyager::bread.edit-add')
@section('submit-buttons')
    <button type="submit" class="btn btn-primary save">{{ __('voyager::generic.save') }}</button>
    <input type="hidden" name="__back" value="{{request()->server('HTTP_REFERER')}}">
@endsection
