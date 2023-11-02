<!-- adminlte::pageを継承 -->
@extends('adminlte::page')

<!-- ページの見出しを入力 -->
@section('content_header')
    <h1>コード管理</h1>
@stop

<!-- ページの内容を入力 -->
@section('content')

<div id="app" class="card">
	<div class="card-header">在庫マスタ管理</div>
    <div class="card-body">
        <form action="{{ route('zaiko.scawupdate') }}" method="post">
            @csrf {{-- CSRF保護 --}}
            @method('POST') {{-- 疑似フォームメソッド --}}
            <input type="hidden" name="ReturnURL" value="{{request()->ReturnURL}}">
            <input type="hidden" name="id" value="{{$item->id}}">
            <table class="table table-sm table-bordered table-hover">
                <tdata>
                    <tr>
                        <td style="width: 1%" colspan="2" class="table-primary text-right">ID</td>
                        <td style="width: 2%">{{$item->id}}</td>
                    </tr>
                    <tr>
                        <td colspan="2" class="table-primary text-right">在庫区分</td>
                        <td>{{$item->scaw_flg_str}}</td>
                    </tr>
                    <tr>
                        <td colspan="2" class="table-primary text-right">事業所</td>
                        <td>{{$item->jigyosyo_name}}</td>
                    </tr>
                    <tr>
                        <td rowspan="3" class="table-primary text-right">場所</td>
				        <td class="table-primary text-right">場所名</td>
                        <td>{{$item->basho}}</td>
                    </tr>
                    <tr>
				        <td class="table-primary text-right">場所No.</td>
                        <td>{{$item->basho_no}}</td>
                    </tr>
                    <tr>
				        <td class="table-primary text-right">棚No.</td>
                        <td>{{$item->basho_tana}}</td>
                    </tr>
                    <tr>
                        <td colspan="2" class="table-primary text-right">貯蔵品コード</td>
                        <td>{{$item->hinmoku_id}}</td>
                    </tr>
                    <tr>
                        <td rowspan="3" class="table-primary text-right">型式</td>
				        <td class="table-primary text-right">型式</td>
        				<td>{{isset($item->hinmoku) ? $item->hinmoku->model :  ''}}</td>
                    </tr>
                    <tr>
				        <td class="table-primary text-right">V</td>
				        <td>{{$item->model_v}}</td>
                    </tr>
                    <tr>
				        <td class="table-primary text-right">種別</td>
				        <td>{{$item->model_kind}}</td>
                    </tr>
                    <tr>
				        <td colspan="2" class="table-primary text-right">名称</td>
                        <td>{{isset($item->hinmoku) ? $item->hinmoku->name :  ''}}</td>
                    </tr>
                    <tr>
				        <td colspan="2" class="table-primary text-right">メーカ</td>
				        <td>{{isset($item->hinmoku) ? $item->hinmoku->maker :  ''}}</td>
                    </tr>
                    <tr>
				        <td colspan="2" class="table-primary text-right">SCAW在庫数</td>
                        <td class="bg-warning">{{$item->scaw_fzsu}}</td>
                    </tr>
                    <tr>
				        <td colspan="2" class="table-primary text-right">ｿﾘｭｰｼｮﾝ在庫数</td>
                        <td class="bg-warning">{{$item->zaiko_suu}}</td>
                    </tr>
                    <div class="form-group">
				        <td colspan="2" class="table-primary text-right">理由</td>
                        <td class="bg-warning">
                            <input type="text" class="form-control" {{$MobileDisabled}} id="zaiko_suu_biko" name="zaiko_suu_biko" placeholder="変更理由">
                        </td>
                    </div>
                </tdata>
            </table>
        	<div class="btn-group btn-group-ms mt-1" role="group">
@if(Gate::allows('Master'))
                <button type="submit" {{$MobileDisabled}}  name="scawupdate" class="btn btn-primary">SCAW在庫数でｿﾘｭｰｼｮﾝ在庫数を更新</button>
@endif
            	<a href="{{request()->ReturnURL}}" class="btn btn-primary">戻る</a>
        	</div>
        </form>
    </div>
</div>

@stop

<!-- 読み込ませるCSSを入力 -->
@section('css')
@stop

<!-- 読み込ませるJSを入力 -->
@section('js')
@stop
