@extends('adminlte::page')

@section('title', config('adminlte.title'))

@section('content-header')
@stop

@section('content')
<div id="app" class="card">
	<div class="card-header">在庫履歴</div>
    <div class="card-body">
        <form action="{{ route('kzaiko.rireki', ['id' => $kzaiko_id]) }}" method="get" class="form-inline">
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
        </form>
    </div>
    <div style="overflow-x:auto;">
        <table class="table table-sm table-bordered table-hover">
            <thead>
                <tr class="table-primary">
                    <th>操作</th>
                    <th>ID</th>
                    <th>個別在庫ID</th>
                    <th>日時</th>
                    <th>区分</th>
                    <th>状態</th>
                    <th>在庫ID</th>
                    <th>前回の在庫ID</th>
                    <th>出庫ID</th>
                    <th>倉庫移動ID</th>
                    <th>担当</th>
                    <th class="d-none d-md-table-cell">備考</th>
                </tr>
            </thead>
            <tbody>
@foreach($items as $item)
                <tr>
                    <td>
                        <div class="btn-group btn-group-sm ml-2" role="group">
                            <a href="{{route('kzaiko.rireki', ['id' => $item->kzaiko_id, 'ReturnURL' => url()->full()])}}" class="btn btn-primary" title="通帳">
                                <i class="fas fa-list-alt"></i>
                            </a>
                        </div>
                    </td>
                    <td>
                        {{$item->id}}
                    </td>
                    <td class="position-relative">
@isset($item->kzaiko_id)
                        <a class="stretched-link" href="{{route('kzaiko.index', ['search_id' => $item->kzaiko_id])}}" target="_blank">
                            [{{$item->kzaiko_id}}]
@isset($item->kzaiko)
@isset($item->kzaiko->hinmoku)
                            {{$item->kzaiko->hinmoku->name}},
                            {{$item->kzaiko->hinmoku->model}}
@endisset
@endisset
                        </a>
@endisset
                    </td>
                    <td>{{$item->updated_at}}</td>
                    <td>{{$item->kzaiko_rireki_type->name}}</td>
                    <td>
@if($item->status_old_str != $item->status_str)
                        {{$item->status_old_str}} ->
@endif
                        {{$item->status_str}}
                    </td>
                    <td class="position-relative">
@isset($item->zaiko_id)
                        <a class="stretched-link" href="{{route('zaiko.index', ['search_id' => $item->zaiko_id])}}" target="_blank">{{$item->zaiko_id}}</a>
@endisset
                    </td>
                    <td class="position-relative">
@isset($item->zaiko_id_old)
                        <a class="stretched-link" href="{{route('zaiko.index', ['search_id' => $item->zaiko_id_old])}}" target="_blank">{{$item->zaiko_id_old}}</a>
@endisset
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
