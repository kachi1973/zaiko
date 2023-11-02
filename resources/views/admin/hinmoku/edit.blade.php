<!-- adminlte::pageを継承 -->
@extends('adminlte::page')

<!-- ページの見出しを入力 -->
@section('content_header')
    <h1>コード管理</h1>
@stop

<!-- ページの内容を入力 -->
@section('content')

<div id="app" class="container m-1">
    <div class="col-lg-6">
        <div class="card">
            <div class="card-header">品目マスタ管理</div>
            <div class="card-body">
                <form action="{{ route('hinmoku.store') }}" method="post">
                    @csrf {{-- CSRF保護 --}}
                    @method('POST') {{-- 疑似フォームメソッド --}}
                    <input name="ReturnURL" type="hidden" class="form-control" readonly value="{{request()->ReturnURL}}">
                    <div class="form-group">
                        <label for="id">品目コード</label>
                        <input type="text" class="form-control" {{$MobileDisabled}} id="id" name="id" value="{{$item->id}}">
                    </div>
                    <div class="form-group">
                        <label for="model">型式</label>
                        <input type="text" class="form-control" {{$MobileDisabled}} id="model" name="model" placeholder="型式" value="{{$item->model}}">
                    </div>
                    <div class="form-group">
                        <label for="name">名称</label>
                        <input type="text" class="form-control" {{$MobileDisabled}} id="name" name="name" placeholder="基板、ＬＥＤ、電源など" value="{{$item->name}}">
                    </div>
                    <div class="form-group">
                        <label for="maker">メーカ</label>
                        <input type="text" class="form-control" {{$MobileDisabled}} id="maker" name="maker" placeholder="メーカ" value="{{$item->maker}}">
                    </div>
                    <div class="btn-group btn-group-ms" role="group">
                        <button type="submit" name="edit"  {{$MobileDisabled}} class="btn btn-primary">登録</button>
                        <a href="{{request()->ReturnURL}}"  class="btn btn-primary">戻る</a>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

@stop

<!-- 読み込ませるCSSを入力 -->
@section('css')
@stop

<!-- 読み込ませるJSを入力 -->
@section('js')
@stop
