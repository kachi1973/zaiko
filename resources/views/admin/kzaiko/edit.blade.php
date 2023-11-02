<!-- adminlte::pageを継承 -->
@extends('adminlte::page')

<!-- ページの見出しを入力 -->
@section('content_header')
    <h1>コード管理</h1>
@stop

<!-- ページの内容を入力 -->
@section('content')

<div id="app" class="container m-1">
    <div class="col-md-6">
        <div class="card">
            <div class="card-header">個別在庫マスタ管理
            </div>
            <div class="card-body">
                <form action="{{ route('kzaiko.store') }}" method="post">
                    @csrf {{-- CSRF保護 --}}
                    @method('POST') {{-- 疑似フォームメソッド --}}
                    <input name="ReturnURL" type="hidden" class="form-control" readonly value="{{request()->ReturnURL}}">
                    <div class="form-group">
                        <label for="id">ID</label>
                        <input type="number" class="form-control" {{$MobileDisabled}} id="id" name="id" readonly value="{{$item->id}}">
                    </div>
                    <div class="form-group">
                        <label for="id">在庫ID</label>
                        <input type="number" class="form-control" {{$MobileDisabled}} id="zaiko_id" name="zaiko_id" readonly value="{{$item->zaiko_id}}">
                    </div>
                    <div class="form-group">
                        <label for="hinmoku_id">貯蔵品コード</label>
                        <input type="text" class="form-control" {{$MobileDisabled}} id="hinmoku_id" name="hinmoku_id" placeholder="貯蔵品コード" value="{{$item->hinmoku_id}}">
                    </div>
                    <div class="form-group">
                        <label for="model_v">型式:Ver</label>
                        <input type="text" class="form-control" {{$MobileDisabled}} id="model_v" name="model_v" placeholder="Ver" value="{{$item->model_v}}">
                    </div>
                    <div class="form-group">
                        <label for="model_v">型式:種別</label>
                        <input type="text" class="form-control" {{$MobileDisabled}} id="model_kind" name="model_kind" placeholder="種別" value="{{$item->model_kind}}">
                    </div>
                    <div class="form-group">
                        <label for="biko">備考</label>
                        <input type="text" class="form-control" {{$MobileDisabled}} id="biko" name="biko" placeholder="備考" value="{{$item->biko}}">
                    </div>
                    <div class="form-group">
                        <label for="seizo_date">製造年月</label>
                        <input type="text" class="form-control" {{$MobileDisabled}} id="seizo_date" name="seizo_date" placeholder="製造年月" value="{{$item->seizo_date}}">
                    </div>
                    <div class="form-group">
                        <label for="created_at">作成日</label>
                        <input type="date" class="form-control" {{$MobileDisabled}} id="created_at" name="created_at" value="{{Helper::YMD2($item->created_at)}}">
                    </div>
                    <div class="form-group">
                        <label for="seiban">在庫製番</label>
                        <input type="text" class="form-control" {{$MobileDisabled}} id="seiban" name="seiban" placeholder="在庫製番" value="{{$item->seiban}}">
                    </div>
                    <div class="form-group">
                        <label for="status">状態</label>
                        <select id="status" name="status" class="form-control" {{$MobileDisabled}} >
                            <option value="0" @if($item->status == 0)       selected @endif>保管中(0)</option>
                            <option value="1" @if($item->status == 1)       selected @endif>出庫中(1)</option>
                            <option value="9999" @if($item->status == 9999) selected @endif>使用済(9999)</option>
                        </select>
                    </div>
                    <div class="btn-group btn-group-ms" role="group">
                        <button type="submit" name="edit"  {{$MobileDisabled}} value="1" class="btn btn-primary">登録</button>
                        <button type="submit" name="delete" value="2" class="btn btn-primary" {{$item->can_delete ? '' : 'disabled'}} {{$MobileDisabled}} onclick="return window.confirm('削除しますか？')">削除</button>
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
