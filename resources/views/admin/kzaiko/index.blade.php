@extends('adminlte::page')

@section('title', config('adminlte.title'))

@section('content-header')
@stop

@section('content')
<div id="app" class="card">
	<div class="card-header">個別在庫マスタ管理</div>
    <div class="card-body">
        <form name="searchForm" action="{{ route('kzaiko.index') }}" method="get" class="form-inline" @submit="onSearch">
            @csrf {{-- CSRF保護 --}}
            @method('GET') {{-- 疑似フォームメソッド --}}
            <div class="input-group input-group-sm">
                <div class="input-group-prepend">
                    <div class="input-group-text">ID</div>
                </div>
                <input id="search_id" name="search_id" type="text" class="form-control" placeholder="ID" value="{{$req->search_id}}" v-model="search.id">
            </div>
            <tanasel
                :sys="sys"
                v-model="search.tana"
                name1="search_jigyosyo_id"
                name2="search_basho"
                name3="search_basho_no"
                name4="search_basho_tana"
                value1="{{$req->search_jigyosyo_id}}"
                value2="{{$req->search_basho}}"
                value3="{{$req->search_basho_no}}"
                value4="{{$req->search_basho_tana}}"
                tananame="棚"
                :ing="ing"
                ></tanasel>
            <div class="input-group input-group-sm">
                <div class="input-group-prepend">
                    <div class="input-group-text">在庫ID</div>
                </div>
                <input id="search_zaiko_id" name="search_zaiko_id" type="text" class="form-control" placeholder="ID" value="{{$req->search_zaiko_id}}" v-model="search.zaiko_id">
            </div>
            <div class="input-group input-group-sm">
                <div class="input-group-prepend">
                    <div class="input-group-text">状態</div>
                </div>
                <select id="search_status_flg" name="search_status_flg" class="form-control"  v-model="search.status_flg">
                    <option value="0" @if($req->search_status_flg == 0) selected @endif>使用済みを省く</option>
                    <option value="1" @if($req->search_status_flg == 1) selected @endif>全て</option>
                </select>
            </div>
            <div class="input-group input-group-sm">
                <div class="input-group-prepend">
                    <div class="input-group-text">作成日</div>
                </div>
                <datepicker id="search_create_date1" name="search_create_date1" style="display: inline-block;" v-model="search.create_date1"></datepicker>
                ～
                <datepicker id="search_create_date2" name="search_create_date2" style="display: inline-block;" v-model="search.create_date2"></datepicker>
            </div>
            <div class="input-group input-group-sm">
                <div class="input-group-prepend">
                    <div class="input-group-text">ソート</div>
                </div>
                <select name="search_sort" class="form-control">
@foreach($sorts as $idx => $name)
                    <option value="{{$idx}}" @if($req->search_sort == $idx) selected @endif>{{$name}}</option>
@endforeach
                </select>
            </div>
            <div class="btn-group btn-group-sm" role="group">
                <button type="submit" name="submit1" value="1" class="btn btn-primary">検索</button>
                <button type="button" class="btn btn-primary" v-on:click="clear" >クリア</button>
                <a href="{{route('kzaiko.rireki', ['id' => 0, 'ReturnURL' => url()->full()])}}" class="btn btn-primary">全ての履歴</a>
@if(Gate::allows('Shukko'))
                <button type="submit" name="submit2" value="2" class="btn btn-primary d-none d-md-inline" onclick="return window.confirm('QRコードファイルをダウンロードしますか？')">QRコード</button>
                <button type="submit" name="submit3" value="3" class="btn btn-primary d-none d-md-inline" onclick="return window.confirm('CSVファイルをダウンロードしますか？')">CSV</button>
            </div>
            <div class="d-none d-md-inline" >
                <a class="btn btn-primary btn-sm" href="{{Storage::disk('public')->url('installer/ToolsInstaller.msi')}}" download>印刷ツール</a>
            </div>
@endif
    </form>
    </div>
@if(isset($items))
    {{ $items->links('pagination.bootstrap-4') }}
    <div style="overflow-x:auto;">
        <table class="table table-sm table-bordered table-hover table-min">
            <thead>
                <tr class="table-primary">
                    <th rowspan="2">操作</th>
                    <th rowspan="2">ID</th>
                    <th rowspan="2">在庫ID</th>
                    <th class="d-none d-md-table-cell" rowspan="2">作成日</th>
                    <th rowspan="2">貯蔵品コード</th>
                    <th colspan="3">型式</th>
                    <th class="d-none d-md-table-cell" rowspan="2">備考</th>
                    <th rowspan="2">名称</th>
                    <th class="d-none d-md-table-cell" rowspan="2">メーカ</th>
                    <th class="d-none d-md-table-cell" rowspan="2">製造年月</th>
                    <th class="d-none d-md-table-cell" rowspan="2">在庫製番</th>
                    <th rowspan="2">状態</th>
                </tr>
                <tr class="table-primary">
                    <th>型式</th>
                    <th>Ver</th>
                    <th>種別</th>
                </tr>
            </thead>
            <tbody>
@foreach($items as $item)
                <tr class="{{0 < $item->status ? ($item->status<9999 ? "table-warning" : "table-dark") : ""}}">
                    <td>
                        <div class="d-none d-md-inline" >
                            <a href="{{route('kzaiko.edit', ['id' => $item->id, 'ReturnURL' => url()->full()])}}" class="btn btn-primary @if(!Gate::allows('Shukko')) disabled @endif" title="編集">編</a>
                        </div>
                        <a href="{{route('kzaiko.rireki', ['id' => $item->id, 'ReturnURL' => url()->full()])}}" class="btn btn-primary" title="履歴">歴</a>
                    </td>
                    <td>{{$item->id}}</td>
                    <td><a href="{{route('zaiko.index', ['search_id' => $item->zaiko->id, 'ReturnURL' => url()->full()])}}" title="編集">{{$item->zaiko->short_name()}}</a></td>
                    <td>{{Helper::YMD($item->created_at)}}</td>
                    <td>{{$item->hinmoku_id}}</td>
                    <td class="d-none d-md-table-cell">{{isset($item->hinmoku) ? $item->hinmoku->model :  ''}}</td>
                    <td>{{$item->model_v}}</td>
                    <td>{{$item->model_kind}}</td>
                    <td class="d-none d-md-table-cell">{{$item->biko}}</td>
                    <td>{{isset($item->hinmoku) ? $item->hinmoku->name :  ''}}</td>
                    <td class="d-none d-md-table-cell">{{isset($item->hinmoku) ? $item->hinmoku->maker :  ''}}</td>
                    <td class="d-none d-md-table-cell">{{$item->seizo_date}}</td>
                    <td class="d-none d-md-table-cell">{{$item->seiban}}</td>
                    <td>
                        {{$item->status_str}}
@foreach($item->relations as $s)
@if($s->type == 0)
                        <br><a href="{{route('shukko.desc', ['id' => $s->id])}}" target="_blank">出庫:{{$s->id}}</a>
@elseif($s->type == 1)
                        <br><a href="{{route('zido.edit', ['id' => $s->id])}}" target="_blank">移動:{{$s->id}}</a>
@endif
@endforeach
                    </td>
                </tr>
@endforeach
            </tbody>
        </table>
    </div>
	{{ $items->links('pagination.bootstrap-4') }}
@endif
</div>
@stop

@section('css')
<style>
    .table-min{
        min-width: 800px !important;
    }
</style>

@stop

@section('js')
<script>
var root_path = "{{parse_url(asset('/'), PHP_URL_PATH)}}";
var user_id = "{{Auth::user()->id}}";
var def_search_create_date1 = "{{$req->search_create_date1}}";
var def_search_create_date2 = "{{$req->search_create_date2}}";
var searched = {{$searched ? 'true' : 'false'}};
</script>
<script src="{{ asset('js/admin/kzaiko.js') }}"></script>
@stop
