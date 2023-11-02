@extends('adminlte::page')

@section('title', config('adminlte.title'))

@section('content-header')
@stop

@section('content')
<div id="app" class="card">
	<div class="card-header">品目マスタ管理</div>
    <div class="card-body">
        <form action="{{ route('hinmoku.index') }}" method="get" class="form-inline">
            @csrf {{-- CSRF保護 --}}
            @method('GET') {{-- 疑似フォームメソッド --}}
            <div class="input-group input-group-sm">
                <div class="input-group-prepend">
                    <div class="input-group-text">貯蔵品コード</div>
                </div>
                <input type="text" class="form-control" placeholder="貯蔵品コード" name="search_hinmoku_id" value="{{$req->search_hinmoku_id}}">
            </div>
            <div class="input-group input-group-sm">
                <div class="input-group-prepend">
                    <div class="input-group-text">型式</div>
                </div>
                <input type="text" class="form-control" placeholder="型式" name="search_model" value="{{$req->search_model}}">
            </div>
            <div class="input-group input-group-sm">
                <div class="input-group-prepend">
                    <div class="input-group-text">名称</div>
                </div>
                <input type="text" class="form-control" placeholder="名称" name="search_name" value="{{$req->search_name}}">
            </div>
            <div class="input-group input-group-sm">
                <div class="input-group-prepend">
                    <div class="input-group-text">メーカ</div>
                </div>
                <input type="text" class="form-control" placeholder="メーカ" name="search_maker" value="{{$req->search_maker}}">
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
                <button type="button" class="btn btn-primary" onClick="clear2()" >クリア</button>
@if(Gate::allows('Shukko'))
                <a href="{{route('hinmoku.create', ['ReturnURL' => url()->full()])}}" class="btn btn-primary btn-sm d-none d-md-inline">新規</a>
@endif
@if(Gate::allows('Shukko') || Gate::allows('Master'))
                    <button type="submit" name="submit2" value="2" class="btn btn-primary d-none d-md-inline" onclick="return window.confirm('全ての品目をエクセル形式でエクスポートしますか？')">ｴｸｽﾎﾟｰﾄ</button>
@endif
@if(Gate::allows('Master'))
                    <input v-show="false" id="FileInput" type="file" v-on:change="fileSelected">
                    <button type="button" class="btn btn-primary d-none d-md-inline" v-on:click="upload()">ｲﾝﾎﾟｰﾄ</button>
@endif
            </div>
        </form>
    </div>
    {{ $items->links('pagination.bootstrap-4') }}
    <div style="overflow-x:auto;">
        <table class="table table-sm table-bordered table-hover table-min">
            <thead>
                <tr class="table-primary">
                    <th class="d-none d-md-table-cell">操作</th>
                    <th>品目コード</th>
                    <th>型式</th>
                    <th>名称</th>
                    <th>メーカ</th>
                </tr>
            </thead>
            <tbody>
@foreach($items as $item)
                <tr>
                    <td class="d-none d-md-table-cell">
@if(Gate::allows('Shukko'))
                        <a href="{{route('hinmoku.edit', ['id' => $item->id, 'ReturnURL' => url()->full()])}}" class="btn btn-primary btn-sm d-none d-md-inline">
                            <i class="fas fa-edit"></i>
                        </a>
@endif
                    </td>
                    <td>{{$item->id}}</td>
                    <td>{{$item->model}}</td>
                    <td>{{$item->name}}</td>
                    <td>{{$item->maker}}</td>
                </tr>
@endforeach
            </tbody>
        </table>
    </div>
	{{ $items->links('pagination.bootstrap-4') }}
</div>
@stop

@section('css')

@stop

@section('js')
<script src="{{ asset('vendor/jquery/jquery.min.js') }}"></script>
<script>
var root_path = "{{parse_url(asset('/'), PHP_URL_PATH)}}";
function clear2(){
    $('input[name="search_hinmoku_id"]').val('');
    $('input[name="search_model"]').val('');
    $('input[name="search_name"]').val('');
    $('input[name="search_maker"]').val('');
}
</script>
<script src="{{ asset('js/admin/hinmoku.js') }}"></script>
@stop
