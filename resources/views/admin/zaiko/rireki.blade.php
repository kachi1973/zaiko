@extends('adminlte::page')

@section('title', config('adminlte.title'))

@section('content-header')
@stop

@section('content')
<div id="app" class="card">
	<div class="card-header">在庫履歴</div>
    <div class="card-body">
        <form action="{{ route('zaiko.rireki', ['id' => $zaiko_id]) }}" method="get" class="form-inline">
            @csrf {{-- CSRF保護 --}}
            @method('GET') {{-- 疑似フォームメソッド --}}
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
                {{ $items->links('pagination.bootstrap-4') }}
                <div class="btn-group btn-group-sm ml-2" role="group">
                    <button type="submit" name="submit1" value="1" class="btn btn-primary">検索</button>
                </div>
            </div>
        </form>
    </div>
    <div style="overflow-x:auto;">
        <table class="table table-sm table-bordered table-hover table-min">
            <thead>
                <tr class="table-primary">
                    <th>操作</th>
                    <th>ID</th>
                    <th>在庫ID</th>
                    <th>関連在庫ID</th>
                    <th>日時</th>
                    <th>区分</th>
                    <th>在庫数変移</th>
                    <th>貸出数</th>
                    <th>使用数</th>
                    <th>出庫ID</th>
                    <th>移動ID</th>
                    <th>発生品ID</th>
                    <th>担当</th>
                    <th class="d-none d-md-table-cell">備考</th>
                </tr>
            </thead>
            <tbody>
@foreach($items as $item)
                <tr>
                    <td>
                        <div class="form-inline">
                            <a href="{{route('zaiko.rireki', ['id' => $item->zaiko_id, 'ReturnURL' => url()->full()])}}" class="btn btn-primary" title="通帳">
                                <i class="fas fa-list-alt"></i>
                            </a>

@if($user->id == '9999')
                            <div class="d-none d-md-inline">
                                <a onClick='cancel({{$item->id}}, "{{route('zaiko.rireki', ['id' => $item->zaiko_id, 'cancel_id' => $item->id])}}")' class="btn btn-primary" title="取り消し">
                                    <i class="fas fa-times-circle"></i>
                                </a>
                            </div>
                        </div>
@endif
                    </td>
                    <td>
                        {{$item->id}}
                    </td>
                    <td class="position-relative">
@isset($item->zaiko_id)
                        <a class="stretched-link" href="{{route('zaiko.index', ['search_id' => $item->zaiko_id])}}" target="_blank">
                            [{{$item->zaiko_id}}]
@isset($item->zaiko)
                            {{$item->zaiko->hinmoku_id}},
@isset($item->zaiko->hinmoku)
                            {{$item->zaiko->hinmoku->name}},
                            {{$item->zaiko->hinmoku->model}}
@endisset
@endisset
                        </a>
@endisset
                    </td>
                    <td class="position-relative">
                        @isset($item->rel_zaiko_id)
                        <a class="stretched-link" href="{{route('zaiko.index', ['search_id' => $item->rel_zaiko_id])}}" target="_blank">{{$item->rel_zaiko_id}}</a>
                        @endisset
                    </td>
                    <td>{{$item->updated_at}}</td>
                    <td>{{$item->zaiko_rireki_type->name}}</td>
                    <td>
                        @if(isset($item->zaiko_suu_old) && isset($item->zaiko_suu_new))
                        {{$item->zaiko_suu_old}}個 -> {{$item->zaiko_suu_new}}個
                        @elseif(isset($item->zaiko_suu_old))
                        {{$item->zaiko_suu_old}}個
                        @elseif(isset($item->zaiko_suu_new))
                        {{$item->zaiko_suu_new}}個
                        @endif

                    </td>
                    <td>
                        @if(isset($item->out_suu))
                        {{$item->out_suu}}個
                        @endif
                    </td>
                    <td>
                        @if(isset($item->used_suu))
                        {{$item->used_suu}}個
                        @endif
                    </td>
                    <td class="position-relative">
                        @isset($item->shukko_id)
                        <a class="stretched-link" href="{{route('shukko.desc', ['id' => $item->shukko_id])}}" target="_blank">{{$item->shukko_id}}</a>
                        @endisset
                    </td>
                    <td class="position-relative">
                        @isset($item->zido_id)
                        <a class="stretched-link" href="{{route('zido.edit', ['id' => $item->zido_id])}}" target="_blank">{{$item->zido_id}}</a>
                        @endisset
                    </td>
                    <td class="position-relative">
                        @isset($item->hasei_id)
                        <a class="stretched-link" href="{{route('hasei.edit', ['id' => $item->hasei_id])}}" target="_blank">{{$item->hasei_id}}</a>
                        @endisset
                    </td>
                    <td>{{$item->user->name}}</td>
                    <td class="d-none d-md-table-cell">{{$item->biko}}</td>
                </tr>
@endforeach
            </tbody>
        </table>
    </div>
	{{ $items->links('pagination.bootstrap-4') }}
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
function cancel(id, url){
    if (window.confirm(`[ID:${id}]をロールバックしますか?`)) {
        window.location.href = url;
    }
}
</script>
@stop
