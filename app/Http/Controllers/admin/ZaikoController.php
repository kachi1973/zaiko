<?php

namespace App\Http\Controllers\Admin;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Http\Response;
use Carbon\Carbon;

use App\Helper;
use App\Models\Zaiko;
use App\Models\ZaikoRireki;
use App\Models\Jigyosyo;
use App\Models\Hinmoku;
use App\Models\Kzaiko;
use App\Models\KzaikoRireki;

class ZaikoController extends Controller
{
    public function __construct(){
        $this->middleware('auth');
    }
    public function index(Request $req){
        $sorts = array(
            0 => "ID 昇順",
            1 => "ID 降順",
            2 => "在庫区分 昇順",
            3 => "在庫区分 降順",
            4 => "場所 昇順",
            5 => "場所 降順",
            6 => "品目CD 昇順",
            7 => "品目CD 降順",
            //8 => "型式 昇順",
            //9 => "型式 降順",
            //10 => "名称 昇順",
            //11 => "名称 降順",
            //12 => "メーカ 昇順",
            //13 => "メーカ 降順",
            14 => "製造年月 昇順",
            15 => "製造年月 降順",
            16 => "適正割れ 降順",
            17 => "申請中 降順",
            18 => "貸出中 降順",
            19 => "申請貸出中 降順",
            100 => "使用実績 降順",
            101 => "貸出実績 降順",
            102 => "入庫実績 降順",
        );
        if(isset($req->submit2)){
            // SCAW在庫数取得
            foreach (Zaiko::where('scaw_flg', 1)->get() as $z) {
                $z->fzsu_update();
            }
            return redirect(url()->current());
        }else if(isset($req->submit3)){
            // エクセル出力
            $now = Carbon::now();
            $filename = "zaikolist.{$now->format('YmdHi')}.xlsx";
            ob_start();
            passthru(base_path("bin\\zaikocgi.exe 5"));
            $output = ob_get_contents();
            ob_end_clean();
            return new Response($output, 200, array(
                'Content-Type' => 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
                'Content-Disposition' =>  'attachment; filename*=UTF-8\'\'' . urlencode($filename),
            ));
        }
        $items = null;
        $searched = false;
        if(isset($req->search_sort) || isset($req->search_id) || $req->query->has('search_id') || $req->query->has('page')){
            $q = Zaiko::make_query([
                'sort' => $req->search_sort,
                'scaw_flg' => $req->search_scaw_flg,
                'jigyosyo_id' => $req->search_jigyosyo_id,
                'basho' => $req->search_basho,
                'basho_no' => $req->search_basho_no,
                'basho_tana' => $req->search_basho_tana,
                'hinmoku_id' => $req->search_hinmoku_id,
                'model' => $req->search_model,
                'name' => $req->search_name,
                'maker' => $req->search_maker,
                'id' => $req->search_id,
                'type' => $req->search_type,
                'date1' => $req->search_date1,
                'date2' => $req->search_date2,
                'biko' => $req->search_biko,
            ]);
            if(isset($req->submit4)){
                // CSV出力
                $now = Carbon::now();
                $filename = "在庫.{$now->format('YmdHi')}.csv";
                return response()->streamDownload(function () use($q, $req) {
                    $handle = fopen('php://output', 'w');
                    $items = $q->get();
                    $cols = [
                        'ID',
                        '在庫区分',
                        '事業所',
                        '場所名',
                        '場所No.',
                        '棚No.',
                        '貯蔵品コード',
                        '単価',
                        '販売価格',
                        '型式',
                        'Ver',
                        '種別',
                        '備考',
                        '名称',
                        'メーカ',
                        '製造年月',
                        '適正在庫',
                        '在庫数',
                        '個別在庫数',
                        '申請数',
                        '貸出数',
                        'SCAW在庫数',
                        'SCAW在庫CD',
                        'SCAW保管CD',
                        '状態',
                    ];
                    if(100<=$req->search_sort){
                        $cols[] = '使用実績';
                        $cols[] = '貸出実績';
                        $cols[] = '入庫実績';
                    }
                    mb_convert_variables('SJIS', 'UTF-8', $cols);
                    fputcsv($handle, $cols);
                    foreach ($items as $item) {
                        $cols = [
                            $item->id,
                            $item->scaw_flg_str,
                            $item->jigyosyo_name,
                            $item->basho,
                            $item->basho_no,
                            $item->basho_tana,
                            $item->hinmoku_id,
                            $item->tanka,
                            $item->kakaku,
                            isset($item->hinmoku) ? $item->hinmoku->model :  '',
                            $item->model_v,
                            $item->model_kind,
                            $item->biko,
                            isset($item->hinmoku) ? $item->hinmoku->name :  '',
                            isset($item->hinmoku) ? $item->hinmoku->maker :  '',
                            $item->seizo_date,
                            $item->zaiko_tekisei,
                            $item->zaiko_suu,
                            0<$item->kobetu_flg ? $item->kzaiko_suu : '無効',
                            $item->sinsei_suu,
                            $item->kashi_suu,
                            $item->scaw_fzsu,
                            $item->scaw_fzcd,
                            $item->scaw_fhokancd,
                            $item->status_str,
                        ];
                        if(100<=$req->search_sort){
                            $cols[] = $item->used_suu;
                            $cols[] = $item->out_suu;
                            $cols[] = $item->nyuko_suu;
                        }
                            mb_convert_variables('SJIS', 'UTF-8', $cols);
                        fputcsv($handle, $cols);
                    }
                    fclose($handle);
                }, $filename);
            }else{
                $items = $q->paginate(50);
                $items->appends($req->query());
                $searched = true;
            }
        }
        return view('admin.zaiko.index', [
            'req' => $req,
            'user' => Auth::user(),
            'items' => $items,
            'jigyosyos' => Jigyosyo::all2(),
            'sorts' => $sorts,
            'searched' => $searched,
        ]);
    }
    public function edit($id=0){
        $item = new Zaiko();
        if(0< $id){
            $item = Zaiko::find($id);
        }
        return view('admin.zaiko.edit', [
            'user' => Auth::user(),
            'item' => $item,
            'jigyosyos' => Jigyosyo::all2(),
        ]);
    }
    public function store(Request $req){
        $item = new Zaiko();
        if (0 < $req->id) {
            $item = Zaiko::find($req->id);
        }
        $item->basho = $req->basho;
        $item->basho_no = $req->basho_no;
        $item->basho_tana = $req->basho_tana;
        $item->hinmoku_id = $req->hinmoku_id;
        $item->tanka = $req->tanka;
        $item->kakaku = $req->kakaku;
        $item->shuko = $req->shuko;
        $item->nyuko = $req->nyuko;
        $item->model_v = $req->model_v;
        $item->model_kind = $req->model_kind;
        $item->dengen_in = $req->dengen_in;
        $item->dengen_out = $req->dengen_out;
        $item->biko = $req->biko;
        $item->seizo_date = $req->seizo_date;
        $item->zaiko_tekisei = $req->zaiko_tekisei;
        $rireki_suu = $item->zaiko_suu;
        $item->zaiko_suu = $req->zaiko_suu;
        $item->scaw_flg = $req->scaw_flg;
        $item->jigyosyo_id = $req->jigyosyo_id;
        $item->scaw_fzcd = $req->scaw_fzcd;
        $item->scaw_fhokancd = $req->scaw_fhokancd;
        $item->status = $req->status;
        $item->kobetu_flg = $req->kobetu_flg;
        $item->save();
        if ($rireki_suu != $req->zaiko_suu || isset($req->zaiko_suu_biko)) {
            ZaikoRireki::add($item->id, 2, $rireki_suu ?: 0, $req->zaiko_suu, $req->zaiko_suu_biko,null, null, null);
        }
        return redirect($req->ReturnURL);
    }
    public function rireki(Request $req, $id = 0){
        if (isset($req->cancel_id)) {
            /*
            $r = ZaikoRireki::find($req->cancel_id);
            $z = Zaiko::find($r->zaiko_id);
            ZaikoRireki::add($r->zaiko_id, 4, $z->zaiko_suu, $r->zaiko_suu_old, "[履歴ID:{$req->cancel_id}]を取り消しました。",null, null, null);
            $z->zaiko_suu = $r->zaiko_suu_old;
            $z->save();
            */
        }
        $sorts = array(
            0 => "ID 昇順",
            1 => "ID 降順",
            2 => "在庫ID 昇順",
            3 => "在庫ID 降順",
        );
        if(!isset($req->search_sort)){
            $req->search_sort = 1;
        }
        $q = ZaikoRireki::query();
        ZaikoRireki::make_query($q, [
            'zaiko_id' => $id,
            'sort' => $req->search_sort,
        ]);
        $items = $q->paginate(50);
        $items->appends($req->query());
        return view('admin.zaiko.rireki', [
            'zaiko_id' => $id,
            'req' => $req,
            'user' => Auth::user(),
            'items' => $items,
            'sorts' => $sorts,
        ]);
    }
    public function scaw($id = 0){
        $item = Zaiko::find($id);
        return view('admin.zaiko.scaw', [
            'user' => Auth::user(),
            'item' => $item,
        ]);
    }
    public function scawupdate(Request $req){
        $item = Zaiko::find($req->id);
        $item->fzsu_copy($req->zaiko_suu_biko);
        return redirect($req->ReturnURL);
    }
    public function move(Request $req, $id){
        $mess = "";
        $status = 1;
        $src = Zaiko::find($id);
        $dst = null;
        if(isset($req->submit)){
            switch($req->submit){
                case 1:
                    $ret = $src->move_check($req->jigyosyo_id, $req->basho, $req->basho_no, $req->basho_tana, $req->zaiko_suu);
                    $dst = $ret['dst'];
                    if(!$ret['err']){
                        $status = 2;
                    }
                    $mess = $ret['message'];
                    break;
                case 2:
                    $dst = $src->move($req->jigyosyo_id, $req->basho, $req->basho_no, $req->basho_tana, $req->zaiko_suu, null);
                    return redirect($req->ReturnURL);
            }
        }else{
            $mess = "移動元の倉庫から移動先の倉庫へ在庫数を移動します。\n移動先が存在しない場合は新規に作成されます。";
            $dst = $src->replicate();
            $dst->zaiko_suu = null;
        }
        $suggests = Zaiko::select('jigyosyo_id', 'basho', 'basho_no', 'basho_tana')->groupBy('jigyosyo_id', 'basho', 'basho_no', 'basho_tana')->orderBy('jigyosyo_id')->orderBy('basho')->orderBy('basho_no')->orderBy('basho_tana')->get();
        return view('admin.zaiko.move', [
            'status' => $status,
            'user' => Auth::user(),
            'message' => $mess,
            'src' => $src,
            'dst' => $dst,
            'zaiko_suu' => $req->zaiko_suu,
            'jigyosyos' => Jigyosyo::all2(),
            'previousUrl' => app('url')->previous(),
            'suggests' => json_encode($suggests, 0),
        ]);
    }
    public function AjaxPutFile(Request $req){
        $file = $req->file('file');
        $path = $file->getPathname();
        $filename = "zaikolist.xlsx";
        ob_start();
        passthru(base_path("bin\\zaikocgi.exe 6 {$path}"));
        $output = ob_get_contents();
        ob_end_clean();
        return response()->json(
            json_decode($output),
            200,
            [],
            JSON_UNESCAPED_UNICODE
        );
    }
    public function kcreate(Request $req, $id=0){
        $src = Zaiko::find($id);
        if(isset($src)){
            DB::transaction(function () use ($src, $id) {
                $src->kobetu_flg = 1;
                $cnt = Kzaiko::where('status', '<', 9999)->where('zaiko_id', $id)->count();
                for($idx=$cnt; $idx<$src->zaiko_suu; ++$idx){
                    $item = new Kzaiko();
                    $item->zaiko_id = $src->id;
                    $item->hinmoku_id = $src->hinmoku_id;
                    $item->model_v = $src->model_v;
                    $item->model_kind = $src->model_kind;
                    $item->biko = $src->biko;
                    $item->seizo_date = $src->seizo_date;
                    $item->status = 0;
                    $item->save();
                    KzaikoRireki::add(KzaikoRireki::TYPE_NEW, $item);
                }
                $src->save();
            });
        }
        return redirect($req->ReturnURL);
        //return redirect()->route('kzaiko.index', ['search_zaiko_id' => $id]);
    }
}
