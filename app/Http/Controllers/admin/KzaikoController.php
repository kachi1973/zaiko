<?php

namespace App\Http\Controllers\Admin;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Http\Response;
use Carbon\Carbon;

use App\Helper;
use App\Models\Kzaiko;
use App\Models\KzaikoRireki;
use App\Models\Jigyosyo;
use App\Models\Hinmoku;

class KzaikoController extends Controller
{
    public function __construct(){
        $this->middleware('auth');
    }
    public function index(Request $req){
        //dump($zaiko_id);
        $sorts = array(
            0 => "ID 昇順",
            1 => "ID 降順",
            2 => "作成日 昇順",
            3 => "作成日 降順",
            4 => "棚(在庫ID) 昇順",
            5 => "棚(在庫ID) 降順",
        );
        if(!isset($req->search_status_flg)){
            $req->search_status_flg = 0;
        }
        $items = null;
        $searched = false;
        if(isset($req->search_id) || isset($req->search_zaiko_id) || $req->query->has('search_id') || $req->query->has('page')){
            $q = Kzaiko::make_query([
                'sort' => $req->search_sort,
                'id' => $req->search_id,
                'jigyosyo_id' => $req->search_jigyosyo_id,
                'basho' => $req->search_basho,
                'basho_no' => $req->search_basho_no,
                'basho_tana' => $req->search_basho_tana,
                'zaiko_id' => $req->search_zaiko_id,
                'status_flg' => $req->search_status_flg,
                'create_date1' => $req->search_create_date1,
                'create_date2' => $req->search_create_date2,
                'jigyosyo_id' => $req->search_jigyosyo_id,
                'basho' => $req->search_basho,
                'basho_no' => $req->search_basho_no,
                'basho_tana' => $req->search_basho_tana,
            ]);
            $searched = true;
            if(isset($req->submit2)){
                $now = Carbon::now();
                $filename = "qrdata.{$now->format('YmdHi')}.json.kzqr";
                return response()->json(
                    $q->get(),
                    200,
                    array(
                        'Content-Type' => 'application/json',
                        'Content-Disposition' =>  'attachment; filename*=UTF-8\'\'' . urlencode($filename),
                    ),
                    JSON_UNESCAPED_UNICODE,
                );
            }else if(isset($req->submit3)){
                $now = Carbon::now();
                $filename = "個別在庫.{$now->format('YmdHi')}.csv";
                return response()->streamDownload(function () use($q) {
                    $handle = fopen('php://output', 'w');
                    $items = $q->get();
                    $cols = [
                        'ID',
                        '在庫ID',
                        '作成日',
                        '貯蔵品コード',
                        '型式',
                        'Ver',
                        '種別',
                        '備考',
                        '名称',
                        'メーカ',
                        '製造年月',
                        '在庫製番',
                        '状態',
                    ];
                    mb_convert_variables('SJIS', 'UTF-8', $cols);
                    fputcsv($handle, $cols);
                    foreach ($items as $item) {
                        $cols = [
                            $item->id,
                            $item->zaiko->short_name(),
                            Helper::YMD($item->created_at),
                            $item->hinmoku_id,
                            isset($item->hinmoku) ? $item->hinmoku->model :  '',
                            $item->model_v,
                            $item->model_kind,
                            $item->biko,
                            isset($item->hinmoku) ? $item->hinmoku->name :  '',
                            isset($item->hinmoku) ? $item->hinmoku->maker :  '',
                            $item->seizo_date,
                            $item->seiban,
                            $item->status_str,
                        ];
                        mb_convert_variables('SJIS', 'UTF-8', $cols);
                        fputcsv($handle, $cols);
                    }
                    fclose($handle);
                }, $filename);
            }else{
                $items = $q->paginate(50);
                $items->appends($req->query());
            }
        }
        return view('admin.kzaiko.index', [
            'req' => $req,
            'user' => Auth::user(),
            'items' => $items,
            'jigyosyos' => Jigyosyo::all2(),
            'sorts' => $sorts,
            'searched' => $searched,
        ]);
    }
    public function edit($id=0){
        $item = new Kzaiko();
        if(0< $id){
            $item = Kzaiko::find($id);
        }
        return view('admin.kzaiko.edit', [
            'user' => Auth::user(),
            'item' => $item,
            'jigyosyos' => Jigyosyo::all2(),
        ]);
    }
    public function store(Request $req){
        if(isset($req->edit)){
            DB::transaction(function () use ($req) {
                $item = new Kzaiko();
                if (0 < $req->id) {
                    $item = Kzaiko::find($req->id);
                }
                $item->zaiko_id = $req->zaiko_id;
                $item->hinmoku_id = $req->hinmoku_id;
                $item->model_v = $req->model_v;
                $item->model_kind = $req->model_kind;
                $item->biko = $req->biko;
                $item->seizo_date = $req->seizo_date;
                $item->seiban = $req->seiban;
                $status_old = $item->status;
                $item->status = $req->status;
                $item->created_at = $req->created_at;
                $item->save();
                if($item->status != $status_old){
                    KzaikoRireki::add(KzaikoRireki::TYPE_MAINT, $item, $status_old);
                }
            });
        }else if(isset($req->delete)){
            $item = Kzaiko::find($req->id);
            $item->delete_all();
        }
        return redirect($req->ReturnURL);
    }
    public function rireki(Request $req, $id = 0){
        $sorts = array(
            0 => "ID 昇順",
            1 => "ID 降順",
        );
        if(!isset($req->search_sort)){
            $req->search_sort = 1;
        }
        $q = KzaikoRireki::query();
        KzaikoRireki::make_query($q, [
            'kzaiko_id' => $id,
            'sort' => $req->search_sort,
        ]);
        $items = $q->paginate(50);
        $items->appends($req->query());
        return view('admin.kzaiko.rireki', [
            'kzaiko_id' => $id,
            'req' => $req,
            'user' => Auth::user(),
            'items' => $items,
            'sorts' => $sorts,
        ]);
    }
}
