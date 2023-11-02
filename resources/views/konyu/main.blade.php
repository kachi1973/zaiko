@extends('adminlte::page')

@section('title', config('adminlte.title'))

@section('content-header')
@stop

@section('content')
<div id="app" >
<router-view v-bind:sys="sys"></router-view>
</div>
@stop

@section('css')
@stop

@section('js')
<script>
var root_path = "{{parse_url(asset('/'), PHP_URL_PATH)}}";
var user_id = "{{Auth::user()->id}}";
</script>
<script src="{{ asset('js/konyu.js') }}"></script>
@stop
