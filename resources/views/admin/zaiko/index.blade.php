@extends('adminlte::page')

@section('title', config('adminlte.title'))

@section('content-header')
@stop

@section('content')
<div id="app" class="card">
	<div class="card-header">在庫マスタ管理</div>
    <div class="card-body">
        <form name="searchForm" action="{{ route('zaiko.index') }}" method="get" class="form-inline" @submit="onSearch">
            @csrf {{-- CSRF保護 --}}
            @method('GET') {{-- 疑似フォームメソッド --}}
            <div class="input-group input-group-sm">
                <div class="input-group-prepend">
                    <div class="input-group-text">ID</div>
                </div>
                <input type="text" class="form-control" placeholder="ID" name="search_id" id="search_id" value="{{$req->search_id}}" v-model="search.id">
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
                    <div class="input-group-text">在庫区分</div>
                </div>
                <select name="search_scaw_flg" id="search_scaw_flg" v-model="search.scaw_flg" class="form-control">
                    <option value="-1" @if($req->search_scaw_flg === '-1') selected @endif>全て</option>
                    <option value="0"  @if($req->search_scaw_flg === '0') selected @endif>貯蔵品</option>
                    <option value="1"  @if($req->search_scaw_flg === '1') selected @endif>SCAW品</option>
                </select>
            </div>
            <div class="input-group input-group-sm">
                <div class="input-group-prepend">
                    <div class="input-group-text">貯蔵品コード</div>
                </div>
                <input type="text" class="form-control" placeholder="貯蔵品コード" name="search_hinmoku_id" id="search_hinmoku_id" value="{{$req->search_hinmoku_id}}" v-model="search.hinmoku_id">
            </div>
            <div class="input-group input-group-sm">
                <div class="input-group-prepend">
                    <div class="input-group-text">型式</div>
                </div>
                <input type="text" class="form-control" placeholder="型式" name="search_model" id="search_model" value="{{$req->search_model}}" v-model="search.model">
            </div>
            <div class="input-group input-group-sm">
                <div class="input-group-prepend">
                    <div class="input-group-text">名称</div>
                </div>
                <input type="text" class="form-control" placeholder="名称" name="search_name" id="search_name" value="{{$req->search_name}}" v-model="search.name">
            </div>
            <div class="input-group input-group-sm">
                <div class="input-group-prepend">
                    <div class="input-group-text">メーカ</div>
                </div>
                <input type="text" class="form-control" placeholder="メーカ" name="search_maker" id="search_maker" value="{{$req->search_maker}}" v-model="search.maker">
            </div>
            <div class="input-group input-group-sm">
                <div class="input-group-prepend">
                    <div class="input-group-text">備考</div>
                </div>
                <input type="text" class="form-control" placeholder="備考" name="search_biko" id="search_biko" value="{{$req->search_biko}}" v-model="search.biko">
            </div>
            <div class="input-group input-group-sm">
                <div class="input-group-prepend">
                    <div class="input-group-text">特殊条件</div>
                </div>
                <select name="search_type" id="search_type" v-model="search.type" class="form-control">
                    <option value="0"  @if($req->search_type === '0') selected @endif>無し</option>
                    <option value="1"  @if($req->search_type === '1') selected @endif>SCAW品(在庫数に差異)</option>
                    <option value="2"  @if($req->search_type === '2') selected @endif>SCAW品(SCAW側無し)</option>
                    <option value="3"  @if($req->search_type === '3') selected @endif>個別在庫未対応</option>
                    <option value="4"  @if($req->search_type === '4') selected @endif>個別在庫数不一致</option>
                </select>
            </div>
            <div class="input-group input-group-sm">
                <div class="input-group-prepend">
                    <div class="input-group-text">貸出中表示</div>
                </div>
                <div class="custom-control custom-switch border">
                    <input type="checkbox" class="custom-control-input" id="search_checked01" name="search_checked01" value="1" {{$req->search_checked01=="1" ? "checked" : ""}} v-model="search.checked01">
                    <label class="custom-control-label m-1" for="search_checked01"></label>
                </div>
            </div>
            <div class="input-group input-group-sm">
                <div class="input-group-prepend">
                    <div class="input-group-text">ソート</div>
                </div>
                <select name="search_sort" id="search_sort" class="form-control" v-model="search.sort">
@foreach($sorts as $idx => $name)
                    <option value="{{$idx}}" @if($req->search_sort == $idx) selected @endif>{{$name}}</option>
@endforeach
                </select>
            </div>
            <div class="input-group input-group-sm" v-show="100<=search.sort">
                <div class="input-group-prepend">
                    <div class="input-group-text">期間</div>
                </div>
                <input type="date" class="form-control" id="search_date1" name="search_date1" value="{{Helper::YMD2($req->search_date1)}}"  v-model="search.date1">
                ～
                <input type="date" class="form-control" id="search_date2" name="search_date2" value="{{Helper::YMD2($req->search_date2)}}"  v-model="search.date2">
            </div>
            <div class="form-inline">
                <div class="btn-group btn-group-sm" role="group">
                    <button type="submit" name="submit1" value="1" class="btn btn-primary">検索</button>
                    <button type="button" class="btn btn-primary" v-on:click="clear" >クリア</button>
@if(Gate::allows('Shukko'))
                    <a href="{{route('zaiko.create', ['ReturnURL' => url()->full()])}}" class="btn btn-danger btn-sm d-none d-md-inline">新規</a>
@endif
                    <a href="{{route('zaiko.rireki', ['id' => 0, 'ReturnURL' => url()->full()])}}" class="btn btn-primary">全ての履歴</a>
@if(Gate::allows('Master'))
                    <button type="submit" name="submit2" value="2" class="btn btn-primary d-none d-md-inline" onclick="return window.confirm('SCAWから在庫数を取得しますか？')">SCAW数取得</button>
@endif
                    <button type="submit" name="submit4" value="4" class="btn btn-primary d-none d-md-inline" onclick="return window.confirm('CSVファイルをダウンロードしますか？')"
                        @if(!(Gate::allows('Shukko'))) disabled @endif
                    >CSV</button>
@if(Gate::allows('Shukko') || Gate::allows('Master'))
                    <button type="submit" name="submit3" value="3" class="btn btn-primary d-none d-md-inline" onclick="return window.confirm('全ての在庫をエクセル形式でエクスポートしますか？')">ｴｸｽﾎﾟｰﾄ</button>
@endif
@if(Gate::allows('Master'))
                    <input v-show="false" id="FileInput" type="file" v-on:change="fileSelected">
                    <button type="button" class="btn btn-primary d-none d-md-inline" v-on:click="upload()">ｲﾝﾎﾟｰﾄ</button>
@endif
                </div>
            </div>
        </form>
    </div>
@if(isset($items))
    {{ $items->links('pagination.bootstrap-4') }}
    <div style="overflow-x:auto;">
        <table class="table table-sm table-bordered table-hover table-min">
            <thead>
                <tr class="table-primary">
                    <th scope="col" rowspan="2">操作</th>
                    <th scope="col" rowspan="2">ID</th>
                    <th scope="col" rowspan="2">在庫区分</th>
                    <th scope="col" rowspan="2">事業所</th>
                    <th scope="col" colspan="3">場所</th>
                    <th scope="col" rowspan="2">貯蔵品コード</th>
                    <th class="d-none d-md-table-cell" scope="col" rowspan="2">単価</th>
                    <th class="d-none d-md-table-cell" scope="col" rowspan="2">販売価格</th>
                    <th scope="col" colspan="3">型式</th>
                    <th class="d-none d-md-table-cell" scope="col" rowspan="2">備考</th>
                    <th scope="col" rowspan="2">名称</th>
                    <th class="d-none d-md-table-cell" scope="col" rowspan="2">メーカ</th>
                    <th class="d-none d-md-table-cell" scope="col" rowspan="2">製造年月</th>
                    <th scope="col" colspan="5">ｿﾘｭｰｼｮﾝ</th>
                    <th scope="col" colspan="3">SCAW</th>
@if(100<=$req->search_sort)
                    <th scope="col" colspan="3" class="bg-success">実績</th>
@endif
                </tr>
                <tr class="table-primary">
                    <th scope="col">場所名</th>
                    <th scope="col">場所No.</th>
                    <th scope="col">棚No.</th>
                    <th scope="col">型式</th>
                    <th scope="col">Ver</th>
                    <th scope="col">種別</th>
                    <th scope="col">適正在庫</th>
                    <th scope="col">在庫数</th>
                    <th scope="col">個別在庫数</th>
                    <th scope="col">申請数</th>
                    <th scope="col">貸出数</th>
                    <th scope="col">在庫数</th>
                    <th scope="col">在庫CD</th>
                    <th scope="col">保管CD</th>
@if(100<=$req->search_sort)
                    <th scope="col" class="bg-success">使用実績</th>
                    <th scope="col" class="bg-success">貸出実績</th>
                    <th scope="col" class="bg-success">入庫実績</th>
@endif
                </tr>
            </thead>
            <tbody>
@foreach($items as $item)
                <tr class="{{$item->status==1 ? "table-dark" : ""}}">
                    <td>
                        <div class="btn-group btn-group-sm ml-2">
                                <a href="{{route('zaiko.edit', ['id' => $item->id, 'ReturnURL' => url()->full()])}}" class="btn btn-primary @if(!Gate::allows('Shukko')) disabled @endif" title="編集">編</a>
                            <a href="{{route('zaiko.rireki', ['id' => $item->id, 'ReturnURL' => url()->full()])}}" class="btn btn-primary" title="履歴">
                                歴
                            </a>
                                <a href="{{route('zaiko.scaw', ['id' => $item->id, 'ReturnURL' => url()->full()])}}" class="btn btn-primary d-none d-md-inline" title="SCAW"
                                @if(!($item->scaw_flg == 1)) disabled @endif
                                >S</a>
@if(false)
                                <a href="{{route('zaiko.move', ['id' => $item->id, 'ReturnURL' => url()->full()])}}" class="btn btn-primary d-none d-md-inline" title="倉庫移動"
                                @if(Gate::allows('Shukko')) disabled @endif
                                >移</a>
@endif
                                <button type="button" class="btn btn-primary d-none d-md-inline" title="個別在庫作成" v-on:click="onKcreate('{{route('zaiko.kcreate', ['id' => $item->id, 'ReturnURL' => url()->full()])}}')"
                                @if(!(Gate::allows('Shukko') && $item->can_make_kobetu)) disabled @endif
                                >作</button>
                        </div>
                    </td>
                    <td>{{$item->id}}</td>
                    <td>{{$item->scaw_flg_str}}</td>
                    <td>{{$item->jigyosyo_name}}</td>
                    <td>{{$item->basho}}</td>
                    <td>{{$item->basho_no}}</td>
                    <td>{{$item->basho_tana}}</td>
                    <td>{{$item->hinmoku_id}}</td>
                    <td class="d-none d-md-table-cell">
                        <div  class="text-right">
                            {{$item->tanka}}
                        </div>
                    </td>
                    <td class="d-none d-md-table-cell">
                        <div class="text-right">
                            {{$item->kakaku}}
                        </div>
                    </td>
                    <td>{{isset($item->hinmoku) ? $item->hinmoku->model :  ''}}</td>
                    <td>{{$item->model_v}}</td>
                    <td>{{$item->model_kind}}</td>
                    <td class="d-none d-md-table-cell">{{$item->biko}}</td>
                    <td>{{isset($item->hinmoku) ? $item->hinmoku->name :  ''}}</td>
                    <td class="d-none d-md-table-cell">{{isset($item->hinmoku) ? $item->hinmoku->maker :  ''}}</td>
                    <td class="d-none d-md-table-cell">{{$item->seizo_date}}</td>
                    <td class="text-right {{($item->zaiko_suu < $item->zaiko_tekisei) ? 'bg-warning' : ''}}">{{$item->zaiko_tekisei}}</td>
                    <td class="text-right">{{$item->zaiko_suu}}</td>
@if(0<$item->kobetu_flg)
                    <td class="text-right {{($item->zaiko_suu != $item->kzaiko_suu) ? 'bg-warning' : ''}}">
                        <a href="{{route('kzaiko.index', ['search_zaiko_id' => $item->id])}}" target="_blank" >{{$item->kzaiko_suu}}</a>
@else
                    <td class="text-right">
                        無効
@endif
                    </td>
                    <td class="text-right {{($item->sinsei_suu) ? 'bg-warning' : ''}}">
                        {{$item->sinsei_suu}}
@if($req->search_checked01=="1")
@foreach($item->sinseis as $s)
@if($s->type == 0)
                        <br><a href="{{route('shukko.desc', ['id' => $s->id])}}" target="_blank">出庫:{{$s->id}}[{{$s->suu}}]</a>
@elseif($s->type == 1)
                        <br><a href="{{route('zido.edit', ['id' => $s->id])}}" target="_blank">移動:{{$s->id}}[{{$s->suu}}]</a>
@endif
@endforeach
@endif
                    </td>
                    <td class="text-right {{($item->kashi_suu) ? 'bg-warning' : ''}}">
                        {{$item->kashi_suu}}
@if($req->search_checked01=="1")
@foreach($item->kashis as $s)
@if($s->type == 0)
                        <br><a href="{{route('shukko.desc', ['id' => $s->id])}}" target="_blank">出庫:{{$s->id}}[{{$s->suu}}]</a>
@elseif($s->type == 1)
                        <br><a href="{{route('zido.edit', ['id' => $s->id])}}" target="_blank">移動:{{$s->id}}[{{$s->suu}}]</a>
@endif
@endforeach
@endif
                    </td>
@if ($item->scaw_flg == 1)
                    <td class="text-right {{($item->zaiko_suu != $item->scaw_fzsu) || $item->scaw_fzsu == null ? 'bg-warning' : ''}}">{{$item->scaw_fzsu}}</td>
@else
                    <td class="bg-light text-right"></td>
@endif
                    <td>{{$item->scaw_fzcd}}</td>
                    <td>{{$item->scaw_fhokancd}}</td>
@if(100<=$req->search_sort)
                    <td class="text-right">{{$item->used_suu}}</td>
                    <td class="text-right">{{$item->out_suu}}</td>
                    <td class="text-right">{{$item->nyuko_suu}}</td>
@endif
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
        min-width: 1200px !important;
    }
</style>
@stop

@section('js')
<script>
var root_path = "{{parse_url(asset('/'), PHP_URL_PATH)}}";
var user_id = "{{Auth::user()->id}}";
var searched = {{$searched ? 'true' : 'false'}};
</script>
<script src="{{ asset('js/admin/zaiko.js') }}"></script>
@stop
