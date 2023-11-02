<!-- adminlte::pageを継承 -->
@extends('adminlte::page')

<!-- ページの見出しを入力 -->
@section('content_header')
    <h1>コード管理</h1>
@stop

<!-- ページの内容を入力 -->
@section('content')

<div id="app" class="card">
	<div class="card-header">在庫移動</div>
    <div class="card-body">
        <form action="{{ route('zaiko.move', ['id' => $src->id, 'ReturnURL' => url()->full()]) }}" method="post">
            @csrf {{-- CSRF保護 --}}
            @method('POST') {{-- 疑似フォームメソッド --}}
            <input type="hidden" name="ReturnURL" value="{{request()->ReturnURL}}">
            <div style="overflow-x:auto;">
                <table class="table table-sm table-bordered table-hover">
                    <thead>
                        <tr class="table-primary">
                            <th rowspan="2">移動</th>
                            <th rowspan="2">ID</th>
                            <th rowspan="2">在庫区分</th>
                            <th rowspan="2">事業所</th>
                            <th colspan="3">場所</th>
                            <th rowspan="2">貯蔵品コード</th>
                            <th colspan="3">型式</th>
                            <th rowspan="2">備考</th>
                            <th rowspan="2">名称</th>
                            <th rowspan="2">メーカ</th>
                            <th rowspan="2">製造年月</th>
                            <th colspan="1">ｿﾘｭｰｼｮﾝ</th>
                        </tr>
                        <tr class="table-primary">
                            <th>場所名</th>
                            <th>場所No.</th>
                            <th>棚No.</th>
                            <th>型式</th>
                            <th>Ver</th>
                            <th>種別</th>
                            <th>在庫数</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr class="{{$src->status==1 ? "table-dark" : ""}}">
                            <td>移動元</td>
                            <td>
                                <input type="hidden" name="src_id" value="{{$src->id}}">
                                {{$src->id}}
                            </td>
                            <td>{{$src->scaw_flg_str}}</td>
                            <td>{{$src->jigyosyo_name}}</td>
                            <td>{{$src->basho}}</td>
                            <td>{{$src->basho_no}}</td>
                            <td>{{$src->basho_tana}}</td>
                            <td>{{$src->hinmoku_id}}</td>
                            <td>{{isset($src->hinmoku) ? $src->hinmoku->model :  ''}}</td>
                            <td>{{$src->model_v}}</td>
                            <td>{{$src->model_kind}}</td>
                            <td>{{$src->biko}}</td>
                            <td>{{isset($src->hinmoku) ? $src->hinmoku->name :  ''}}</td>
                            <td>{{isset($src->hinmoku) ? $src->hinmoku->maker :  ''}}</td>
                            <td>{{$src->seizo_date}}</td>
                            <td class="text-right">
@if($status == 1)
                                {{$src->zaiko_suu}}
@else
                                {{$src->zaiko_suu}}個 -> {{$src->zaiko_suu - $zaiko_suu}}個
@endif
                            </td>
                        </tr>
                        <tr>
                            <td colspan="21" class="text-center">
                                <i class="fas fa-arrow-circle-down fa-2x"></i>
                                <i class="fas fa-arrow-circle-down fa-2x"></i>
                                <i class="fas fa-arrow-circle-down fa-2x"></i>
                            </td>
                        </tr>
                        <tr class="{{$dst->status==1 ? "table-dark" : ""}}">
                            <td>移動先</td>
                            <td>{{$dst->id}}</td>
                            <td>
                                {{$dst->scaw_flg_str}}
                                <input type="hidden" id="scaw_flg" name="scaw_flg" value="{{$dst->scaw_flg}}">
                            </td>
                            <td>
                                <select id="jigyosyo_id" name="jigyosyo_id" class="form-control" @if($status > 1) readonly @endif>
@foreach($jigyosyos as $jigyosyo)
                                    <option value="{{$jigyosyo->id}}" @if($dst->jigyosyo_id == $jigyosyo->id) selected @endif>{{$jigyosyo->name}}</option>
@endforeach
                                </select>
                            </td>
                            <td>
                                <input type="text" class="form-control" id="basho" name="basho" placeholder="場所名" value="{{$dst->basho}}" @if($status > 1) readonly @endif>
                            </td>
                            <td>
                                <input type="text" class="form-control" id="basho_no" name="basho_no" placeholder="場所No." value="{{$dst->basho_no}}" @if($status > 1) readonly @endif>
                            </td>
                            <td>
                                <input type="text" class="form-control" id="basho_tana" name="basho_tana" placeholder="棚No." value="{{$dst->basho_tana}}" @if($status > 1) readonly @endif>
                            </td>
                            <td>
                                {{$dst->hinmoku_id}}
                                <input type="hidden" id="hinmoku_id" name="hinmoku_id" value="{{$dst->hinmoku_id}}">
                            </td>
                            <td>{{isset($dst->hinmoku) ? $dst->hinmoku->model :  ''}}</td>
                            <td>
                                {{$dst->model_v}}
                                <input type="hidden" id="model_v" name="model_v" value="{{$dst->model_v}}">
                            </td>
                            <td>
                                {{$dst->model_kind}}
                                <input type="hidden" id="model_kind" name="model_kind" value="{{$dst->model_kind}}">
                            </td>
                            <td>{{$dst->biko}}</td>
                            <td>{{isset($dst->hinmoku) ? $dst->hinmoku->name :  ''}}</td>
                            <td>{{isset($dst->hinmoku) ? $dst->hinmoku->maker :  ''}}</td>
                            <td class="text-right">{{$dst->seizo_date}}</td>
                            <td class="text-right">
@if($status == 1)
                                <input type="number" class="form-control text-right" id="zaiko_suu" name="zaiko_suu" placeholder="移動したい在庫数" value="{{$zaiko_suu}}">
@else
                                {{$dst->zaiko_suu}}個 -> {{$dst->zaiko_suu + $zaiko_suu}}個
                                <input type="hidden" id="zaiko_suu" name="zaiko_suu" value="{{$zaiko_suu}}">
@endif
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>
            <div class="alert alert-danger mt-2" role="alert">
                {!! nl2br($message) !!}
            </div>
            <div class="btn-group btn-group-ms mt-1" role="group">
@if($status == 1)
                <button type="submit" name="submit" value="1" class="btn btn-primary">確認</button>
@else
                <button type="submit" name="submit" value="2" class="btn btn-danger">確定</button>
@endif
            	<button type="button" class="btn btn-primary" onClick="history.back()">戻る</button>
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
<script src="{{ asset('js/app.js') }}"></script>
<script type="text/javascript">
$(function() {
    var suggests = JSON.parse('{!!$suggests!!}');
    //$('#basho').autocomplete({source: bashos, autoFocus: true, delay: 500, minLength: 0});
    $('#basho').autocomplete({source: function( req, res ) {
        var items = [];
        suggests.forEach(s =>{
            var jigyosyo_id = $('#jigyosyo_id').val();
            if(s.jigyosyo_id == jigyosyo_id){
                if(!items.includes(s.basho)){
                    items.push(s.basho);
                }
            }
        });
        res(items);
    }, autoFocus: true, delay: 500, minLength: 0});
    $('#basho_no').autocomplete({source: function( req, res ) {
        var items = [];
        var basho = $('#basho').val();
        suggests.forEach(s =>{
            if(s.basho === basho){
                if(!items.includes(s.basho_no)){
                    items.push(s.basho_no);
                }
            }
        });
        res(items);
    }, autoFocus: true, delay: 500, minLength: 0});
    $('#basho_tana').autocomplete({source: function( req, res ) {
        var items = [];
        var basho = $('#basho').val();
        var basho_no = $('#basho_no').val();
        suggests.forEach(s =>{
            if(s.basho === basho && s.basho_no === basho_no){
                if(!items.includes(s.basho_tana)){
                    items.push(s.basho_tana);
                }
            }
        });
        res(items);
    }, autoFocus: true, delay: 500, minLength: 0});
    //$('#basho_no').autocomplete({source: basho_nos, autoFocus: true, delay: 500, minLength: 0});
    //$('#basho_tana').autocomplete({source: basho_tanas, autoFocus: true, delay: 500, minLength: 0});
});
</script>
@stop
