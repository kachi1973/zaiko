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
            <div class="card-header">在庫マスタ管理</div>
            <div class="card-body">
                <form action="{{ route('zaiko.store') }}" method="post">
                    @csrf {{-- CSRF保護 --}}
                    @method('POST') {{-- 疑似フォームメソッド --}}
                    <input name="ReturnURL" type="hidden" class="form-control" readonly value="{{request()->ReturnURL}}">
                    <div class="form-group">
                        <label for="id">ID</label>
                        <input type="number" class="form-control" {{$MobileDisabled}} id="id" name="id" readonly value="{{$item->id}}">
                    </div>
                    <div class="form-group">
                        <label for="zaiko_suu_biko">変更理由(※変更履歴に残ります)</label>
                        <input type="text" class="form-control" {{$MobileDisabled}} id="zaiko_suu_biko" name="zaiko_suu_biko" placeholder="変更理由">
                    </div>
                    <div class="form-group">
                        <label for="jigyosyo_id">事業所</label>
                        <select id="jigyosyo_id" name="jigyosyo_id" class="form-control" {{$MobileDisabled}}>
    @foreach($jigyosyos as $jigyosyo)
                            <option value="{{$jigyosyo->id}}" @if($item->jigyosyo_id == $jigyosyo->id) selected @endif>{{$jigyosyo->name}}</option>
    @endforeach
                        </select>
                    </div>
                    <div class="form-group">
                        <label for="basho">場所:場所名</label>
                        <input type="text" class="form-control" {{$MobileDisabled}} id="basho" name="basho" placeholder="場所名" value="{{$item->basho}}">
                    </div>
                    <div class="form-group">
                        <label for="basho_no">場所:場所No.</label>
                        <input type="text" class="form-control" {{$MobileDisabled}} id="basho_no" name="basho_no" placeholder="場所No." value="{{$item->basho_no}}">
                    </div>
                    <div class="form-group">
                        <label for="basho_tana">場所:棚No.</label>
                        <input type="text" class="form-control" {{$MobileDisabled}} id="basho_tana" name="basho_tana" placeholder="棚No." value="{{$item->basho_tana}}">
                    </div>
                    <div class="form-group">
                        <label for="hinmoku_id">貯蔵品コード</label>
                        <input type="text" class="form-control" {{$MobileDisabled}} id="hinmoku_id" name="hinmoku_id" placeholder="貯蔵品コード" value="{{$item->hinmoku_id}}">
                    </div>
                    <div class="form-group">
                        <label for="tanka">単価</label>
                        <input type="text" class="form-control" {{$MobileDisabled}} id="tanka" name="tanka" placeholder="単価" value="{{$item->tanka}}">
                    </div>
                    <div class="form-group">
                        <label for="kakaku">販売価格</label>
                        <input type="text" class="form-control" {{$MobileDisabled}} id="kakaku" name="kakaku" placeholder="販売価格" value="{{$item->kakaku}}">
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
                        <label for="zaiko_tekisei">在庫数:適正在庫</label>
                        <input type="number" class="form-control text-right" {{$MobileDisabled}} id="zaiko_tekisei" name="zaiko_tekisei" placeholder="適正在庫" value="{{$item->zaiko_tekisei}}">
                    </div>
                    <div class="form-group">
                        <label for="zaiko_suu">在庫数:在庫数</label>
                        <input type="number" class="form-control" {{$MobileDisabled}} id="zaiko_suu" name="zaiko_suu" placeholder="在庫数" value="{{$item->zaiko_suu}}">
                    </div>
                    <div class="form-group">
                        <label for="scaw_flg">在庫区分</label>
                        <select id="scaw_flg" name="scaw_flg" class="form-control" {{$MobileDisabled}}>
                            <option value="0" @if($item->scaw_flg == 0) selected @endif>0:貯蔵品</option>
                            <option value="1" @if($item->scaw_flg == 1) selected @endif>1:SCAW品</option>
                        </select>
                    </div>
                    <div class="form-group">
                        <label for="scaw_fzcd">在庫場所コード</label>
                        <input type="text" class="form-control" {{$MobileDisabled}} id="scaw_fzcd" name="scaw_fzcd" placeholder="在庫場所コード" value="{{$item->scaw_fzcd}}">
                    </div>
                    <div class="form-group">
                        <label for="scaw_fhokancd">保管場所コード</label>
                        <input type="text" class="form-control" {{$MobileDisabled}} id="scaw_fhokancd" name="scaw_fhokancd" placeholder="保管場所コード" value="{{$item->scaw_fhokancd}}">
                    </div>
                    <div class="form-group">
                        <label for="status">状態</label>
                        <select id="status" name="status" class="form-control" {{$MobileDisabled}}>
                            <option value="0" @if($item->status == 0) selected @endif>運用中</option>
                            <option value="1" @if($item->status == 1) selected @endif>休止</option>
                        </select>
                    </div>
                    <div class="form-group">
                        <label for="kobetu_flg">個別在庫</label>
                        <select id="kobetu_flg" name="kobetu_flg" class="form-control" {{$MobileDisabled}}>
                            <option value="0" @if($item->kobetu_flg == 0) selected @endif>無効</option>
                            <option value="1" @if($item->kobetu_flg == 1) selected @endif>有効</option>
                        </select>
                    </div>
                    <div class="btn-group btn-group-ms" role="group">
                        <button type="submit" name="edit" {{$MobileDisabled}} class="btn btn-primary">登録</button>
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
