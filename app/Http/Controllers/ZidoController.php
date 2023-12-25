<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Response;
use Intervention\Image\Facades\Image;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\DB;

use App\Helper;

use App\Models\User;
use App\Models\Bumon;
use App\Models\Hinmoku;
use App\Models\Zido;
use App\Models\ZidoItem;
use App\Models\ZidoItemKzaiko;
use App\Models\Zaiko;
use App\Models\Kzaiko;
use App\Models\KzaikoRireki;

class ZidoController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('auth');
    }
    public function main(Request $request)
    {
        return view('zido.main', ['user' => Auth::user()]);
    }
    public function AjaxGetZidos(Request $req){
        $perPage = 10;
        $pageNum = $req->pageNum;
        //$q = Zido::with('bumon', 'xitems.tojigyosyo', 'xitems.xhinmoku', 'xitems.xzaiko.xhinmoku', 'xitems.xzaiko.xjigyosyo')->select("zidos.*")
        // xitemsはHinmokuとHrkbuhinvを切り替えているのでキャッシュしてはいけない。
        $q = Zido::with(['bumon', 'xstatus10user', 'xstatus20user', 'xstatus30user', 'xstatus40user', 'xstatus50user', 'xstatus60user', 'xstatus70user'])->select("zidos.*")
            ->leftJoin('users', 'zidos.status10_user_id', '=', 'users.id')
            ->leftJoin('bumons', 'users.bumon_id', '=', 'bumons.id');
        if (isset($req->search)) {
            if (isset($req->search['sort'])) {
                switch($req->search['sort']){
                    case 0: // ID 昇順
                        $q->orderBy('id');
                        break;
                    case 1: // ID 降順
                        $q->orderBy('id', 'desc');
                        break;
                    case 2: // 入庫・出庫日 昇順
                        $q->orderBy('inout_date')->orderBy('id');
                        break;
                    case 3: // 入庫・出庫日 降順
                        $q->orderBy('inout_date', 'desc')->orderBy('id', 'desc');
                        break;
                    case 4: // 状態 昇順
                        $q->orderBy('status')->orderBy('id');
                        break;
                    case 5: // 状態 降順
                        $q->orderBy('status', 'desc')->orderBy('id', 'desc');
                        break;
                    case 6: // 申請日 昇順
                        $q->orderBy('status10_date')->orderBy('id');
                        break;
                    case 7: // 申請日 降順
                        $q->orderBy('status10_date', 'desc')->orderBy('id', 'desc');
                        break;
                }
            }
            if (isset($req->search['id']) && 0 < $req->search['id']) {
                // IDの場合はIDのみで検索
                $q->where('zidos.id', $req->search['id']);
            } else if (0 < $req->search['parent_kind']) {
                // 関連検索の場合は関連検索のみで検索
                $q->where('parent_kind', $req->search['parent_kind']);
                if (isset($req->search['parent_id'])) {
                    $q->where('parent_id', $req->search['parent_id']);
                }
            }else{
                if (isset($req->search['user_id']) && 0 < $req->search['user_id']) {
                    // 担当者を指定した場合は所属を無視する。
                    $q->where('status10_user_id', $req->search['user_id']);
                } else if (isset($req->search['bumon_id']) && 0 < $req->search['bumon_id']) {
                    // 所属を指定した場合は所属内の全担当者で検索。
                    $user_ids = collect(User::where('bumon_id', $req->search['bumon_id'])->get())->map(function ($c) {
                        return $c->id;
                    });
                    $q->whereIn('status10_user_id', $user_ids);
                }
                // 日付
                if (isset($req->search['date_sel'])) {
                    $fieldname = (['status10_date', 'inout_date'])[$req->search['date_sel']];
                    if (isset($req->search['date1_str'])) {
                        $date = Carbon::parse($req->search['date1_str']);
                        $date->setTime(0, 0, 0);
                        $q->where($fieldname, '>=', $date);
                }
                    if (isset($req->search['date2_str'])) {
                        $date = Carbon::parse($req->search['date2_str']);
                        $date->setTime(0, 0, 0)->addDay();
                        $q->where($fieldname, '<', $date);
                    }
                }
                // 備考
                if (isset($req->search['biko'])) {
                    $q->Where('biko', 'ilike', Helper::get_like_str($req->search['biko']));
                }
                // 状態
                $stss = [];
                if ($req->search['stschk00']) $stss[] = 0;
                if ($req->search['stschk10']) $stss[] = 10;
                if ($req->search['stschk20']) $stss[] = 20;
                if ($req->search['stschk30']) $stss[] = 30;
                if ($req->search['stschk40']) $stss[] = 40;
                if ($req->search['stschk50']) $stss[] = 50;
                if ($req->search['stschk60']) $stss[] = 60;
                if ($req->search['stschk70']) $stss[] = 70;
                if ($req->search['stschk99']) $stss[] = 99;
                if(count($stss) < 9){
                    $q->whereIn('status', $stss);
                }
            }
        }
        if($req->type == 'excel'){
            $items = $q->get();
            foreach ($items as $item) {
                $item->append('items');
            }
            $json = json_encode($items, 0);
            if(config('app.json_debug')){
                file_put_contents(base_path("bin\\zidos.json"), $json);
            }
            [$ret, $stdout, $stderr] = Helper::RunCmd(base_path("bin\\zaikocgi.exe 14"), $json);
            $headers = [
                'Content-Type' => 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
                'Content-Disposition' => 'attachment; filename="zidos.xlsx"'
            ];
            return response()->make($stdout, 200, $headers);
        }else{
            $total_cnt = $q->count();
            $items = $q->skip(($pageNum - 1) * $perPage)->take($perPage)->get();
            foreach ($items as $item) {
                $item->append('items');
            }
            return response()->json(
                [
                    'total_cnt' => $total_cnt,
                    'page_cnt' => ceil($total_cnt / $perPage),
                    'page_num' => $pageNum,
                    'items' => $items,
                ],
                200,
                [],
                JSON_UNESCAPED_UNICODE
            );
        }
    }
    public function get_zido($id){
        $item = Zido::find($id);
        $item->append('items');
        return $item;
    }
    public function AjaxGetZido(Request $req){
        return response()->json(
            [
                'item' => $this->get_zido($req->id),
            ],
            200,
            [],
            JSON_UNESCAPED_UNICODE
        );
    }
    public function descprint(Request $req)
    {
        $filename = "zido.pdf";
        $proc = proc_open(base_path("bin\\zaikocgi.exe 3"), array(
            0 => array('pipe', 'r'),
            1 => array('pipe', 'w'),
            //2 => array('pipe', 'w')
        ), $pipes);
        stream_set_write_buffer($pipes[0], 0);
        stream_set_blocking($pipes[1], 0);
        $json = json_encode($this->get_zido($req->id), 0);
        if(config('app.json_debug')){
            file_put_contents(base_path("bin\\zido.json"), $json);
        }
        $len = strlen($json);
        for ($written = 0; $written < $len;) {
            $written += fwrite($pipes[0], substr($json, $written, 4096));
        }
        fclose($pipes[0]);
        $stdout = $stderr = '';
        while (feof($pipes[1]) === false) {
            $read = array($pipes[1]);
            $write  = NULL;
            $except = NULL;
            $ret = stream_select($read, $write, $except, 1);
            if ($ret === false) {
                // error
                break;
            } else if ($ret === 0) {
                // timeout
                continue;
            } else {
                foreach ($read as $sock) {
                    if ($sock === $pipes[1]) {
                        $stdout .= fread($sock, 4096);
                    }
                }
            }
        }
        fclose($pipes[1]);
        proc_close($proc);
        //file_put_contents(base_path("bin\\" . $filename), $stdout);
        return new Response($stdout, 200, array(
            'Content-Type' => 'application/pdf',
            'Content-Disposition' =>  'inline; filename*=UTF-8\'\'' . urlencode($filename),
        ));
    }
    public function set_status($rs, $s, $user, $command, &$mess)
    {
        switch ($command) {
            case 1: // 進む
                switch ($s->status) {
                    case 10: // 所属長認待ち
                        $s->status = 20;
                        break;
                    case 20: // 出庫待ち
                        $s->status = 30;
                        break;
                    case 30: // 入庫待ち
                    case 40: //
                    case 50: //
                    case 60: //
                        $s->status = 70;
                        break;
                    case 70: // 完了
                        break;
                    default: // 申請待ち
                        if ($command == 1) { // 進む
                            // 移動元棚チェック
                            $items = $rs['items'];
                            foreach ($items as $idx => $item) {
                                $mess_pre = "(在庫 " . ($idx + 1) . " 列目)";
                                $mess = null;
                                switch ($rs['from_type']) {
                                    case 0: // 第１工場 １階
                                        break;
                                    case 1: // 第１工場 ２階
                                        break;
                                    case 99: // ソリューション棚
                                        $src = Zaiko::find($item['zaiko_id']);
                                        if (isset($src)) {
                                            $ret = $src->move_check(@$item['to_zaiko_id'], @$item['to_jigyosyo_id'], @$item['to_basho'], @$item['to_basho_no'], @$item['to_basho_tana'], @$item['suu']);
                                            if ($ret["err"]) {
                                                $mess = $ret["message"] . $mess_pre;
                                                return false;
                                            }
                                        } else {
                                            $mess = "ソリューション在庫に存在しない在庫です。" . $mess_pre;
                                            return false;
                                        }
                                        break;
                                }
                            }
                            // 移動先棚チェック
                            switch ($rs['to_type']) {
                                case 99: // ソリューション棚
                                    break;
                            }
                        }
                        $s->status = 10;
                        break;
                }
            break;
            case 2: // 戻る
                switch ($s->status) {
                    case 10: // 課長承認待ち
                        $s->status = 0;
                        break;
                    case 20: // 出庫待ち
                        $s->status = 10;
                        break;
                    case 30: // 入庫待ち
                        $s->status = 20;
                        break;
                    case 40: //
                    case 50: //
                    case 60: //
                        $s->status = 0;
                        break;
                    case 70: // 完了
                        $s->status = 30;
                        break;
                    case 99: // キャンセル
                        $s->status = 0;
                        break;
                    default: // 申請待ち
                        $s->status = 99;
                        break;
                }
                break;
        }
        switch($s->status){
            case 10: // 課長承認待ち
                if($command==1){
                    $s->status10_date = Carbon::now();
                    $s->status10_user_id = $user->id;
                }
                $s->status20_date = null;
                $s->status20_user_id = null;
                $s->status30_date = null;
                $s->status30_user_id = null;
                $s->status40_date = null;
                $s->status40_user_id = null;
                $s->status50_date = null;
                $s->status50_user_id = null;
                $s->status60_date = null;
                $s->status60_user_id = null;
                $s->status70_date = null;
                $s->status70_user_id = null;
                break;
            case 20: // 出庫待ち
                if($command==1){
                    $s->status20_date = Carbon::now();
                    $s->status20_user_id = $user->id;
                }
                $s->status30_date = null;
                $s->status30_user_id = null;
                $s->status40_date = null;
                $s->status40_user_id = null;
                $s->status50_date = null;
                $s->status50_user_id = null;
                $s->status60_date = null;
                $s->status60_user_id = null;
                $s->status70_date = null;
                $s->status70_user_id = null;
                // キャンセルで出庫待ちになった場合は個別在庫を削除する
                foreach(ZidoItem::where('zido_id', $s->id)->get() as $zi){
                    foreach ($zi->items as $zikz) {
                        $status_old = $zikz->kzaiko->status;
                        $zikz->kzaiko->update(['status' => Kzaiko::STATUS_ZAIKO]);
                        KzaikoRireki::add(KzaikoRireki::TYPE_STATUS, $zikz->kzaiko, $status_old, null, "倉庫移動取り消し", null, $s->id);
                    }
                    ZidoItemKzaiko::find2($zi->id)->delete();
                }
                break;
            case 30: // 入庫待ち
                if ($command == 1) {
                    $s->status30_date = Carbon::now();
                    $s->status30_user_id = $user->id;
                    $items = $rs['items'];
                    // 個別在庫の登録
                    foreach ($items as $idx => $req_zi) {
                        $zi = ZidoItem::find($req_zi['id']);
                        if(isset($zi)){
                            ZidoItemKzaiko::find2($zi->id)->delete();
                            if (isset($req_zi['items'])) {
                                foreach ($req_zi['items'] as $idx => $req_kz) {
                                    $ikz = new ZidoItemKzaiko();
                                    $ikz->item_id = $zi->id;
                                    $ikz->kzaiko_id = $req_kz['kzaiko_id'];
                                    $ikz->status = $req_kz['status'];
                                    $ikz->save();
                                    $kz = Kzaiko::find($req_kz['kzaiko_id']);
                                    if(isset($kz)){
                                        $kz_status_old = $kz->status;
                                        $kz->update(['status' => Kzaiko::STATUS_SHUKKO]);
                                        KzaikoRireki::add(KzaikoRireki::TYPE_STATUS, $kz, $kz_status_old, null, null, null, $s->id);
                                    }
                                }
                            }
                        }
                    }
                }
                $s->status40_date = null;
                $s->status40_user_id = null;
                $s->status50_date = null;
                $s->status50_user_id = null;
                $s->status60_date = null;
                $s->status60_user_id = null;
                $s->status70_date = null;
                $s->status70_user_id = null;
                break;
            case 70: // 完了
                if ($command == 1) {
                    $s->status70_date = Carbon::now();
                    $s->status70_user_id = $user->id;
                    $items = $rs['items'];
                    foreach ($items as $idx => $req_zi) {
                        $zi = ZidoItem::find($req_zi['id']);
                        switch ($rs['from_type']) {
                            case 0: // 第１工場 １階
                            case 1: // 第１工場 ２階
                                Zaiko::move_from_scaw($zi->hinmoku_id, $zi->to_zaiko_id, $zi->to_jigyosyo_id, $zi->to_basho, $zi->to_basho_no, $zi->to_basho_tana, $zi->suu, $s->id);
                                break;
                            case 99: // ソリューション棚
                                $src = Zaiko::find($zi->zaiko_id);
                                if (isset($src)) {
                                    $zaiko_dst = $src->move($zi->to_zaiko_id, $zi->to_jigyosyo_id, $zi->to_basho, $zi->to_basho_no, $zi->to_basho_tana, $zi->suu, $s->id);
                                    if (isset($req_zi['items'])) {
                                        foreach ($req_zi['items'] as $idx => $req_kz) {
                                            ZidoItemKzaiko::find2($zi->id, $req_kz['kzaiko_id'])->update(['status' => Kzaiko::STATUS_ZAIKO]);
                                            $kz = Kzaiko::find($req_kz['kzaiko_id']);
                                            if(isset($kz)){
                                                $kz_status_old = $kz->status;
                                                $kz->update(['status' => Kzaiko::STATUS_ZAIKO, 'zaiko_id' => $zaiko_dst->id]);
                                                KzaikoRireki::add(KzaikoRireki::TYPE_ZIDO, $kz, $kz_status_old, $src->id, null, null, $s->id);
                                            }
                                        }
                                    }
                                }
                                break;
                        }
                    }
                }
                if($command==1){
                    $s->status70_date = Carbon::now();
                    $s->status70_user_id = $user->id;
                }
                break;
            default: // 申請待ち
                $s->status10_date = null;
                if($command==1){
                    $s->status10_user_id = $user->id;
                }
                $s->status20_date = null;
                $s->status20_user_id = null;
                $s->status30_date = null;
                $s->status30_user_id = null;
                $s->status40_date = null;
                $s->status40_user_id = null;
                $s->status50_date = null;
                $s->status50_user_id = null;
                $s->status60_date = null;
                $s->status60_user_id = null;
                $s->status70_date = null;
                $s->status70_user_id = null;
                break;
        }
        return true;
    }
    public function AjaxPutZido(Request $req){
        $mess = "";
        $ret = true;
        $id = 0;
        DB::transaction(function () use ($req, &$mess, &$ret, &$id) {
            $user = Auth::user();
            if(isset($req['item'])){
                $rs = $req['item'];
                $s = null;
                if (isset($rs['id']) && 0< $rs['id']) {
                    $s = Zido::find($rs['id']);
                }
                if(!isset($s)){
                    $s = new Zido();
                }
                if(!isset($s->status10_user_id)){
                    $s->status10_user_id = $user->id;
                }
                if (isset($rs['command'])) {
                    $ret = $this->set_status($rs, $s, $user, $rs['command'], $mess);
                }
                if($ret){
                    if($rs['editable']){
                        if (isset($rs['inout_date'])) {
                            $s->inout_date = Helper::ISO8601toDATE($rs['inout_date']);
                        }
                        if (isset($rs['from_type'])) {
                            $s->from_type = $rs['from_type'];
                            switch($s->from_type){
                                case 0:
                                    $s->souko = '美和(第１工場 １階)';
                                    break;
                                case 1:
                                    $s->souko = '美和(第１工場 ２階)';
                                    break;
                                case 2:
                                    $s->souko = '美和(00901 1)';
                                    break;
                                case 3:
                                    $s->souko = '東京(02103 A-S1-4)';
                                    break;
                                case 4:
                                    $s->souko = '大阪(03101 3-3-1)';
                                    break;
                                case 5:
                                    $s->souko = '広島(04101 5-1)';
                                    break;
                                case 6:
                                    $s->souko = '福岡(05101 C-1)';
                                    break;
                                case 7:
                                    $s->souko = '仙台(06101 A-1)';
                                    break;
                                case 8:
                                    $s->souko = '札幌(07101 F)';
                                    break;
                                case 9:
                                    $s->souko = '四国(08101 2-1)';
                                    break;
                                case 10:
                                    $s->souko = '新潟(09101 A-4)';
                                    break;
                                case 11:
                                    $s->souko = '南九州(10101 MB-1)';
                                    break;
                                case 99:
                                    $s->souko = 'ソリューション棚';
                                    break;
                                default:
                                $s->souko = null;
                                break;
                            }
                        }
                        if (isset($rs['to_type'])) {
                            $s->to_type = $rs['to_type'];
                            switch($s->to_type){
                                case 0:
                                    $s->tana = '美和(00901 1)';
                                    break;
                                case 1:
                                    $s->tana = '東京(02103 A-S1-4)';
                                    break;
                                case 2:
                                    $s->tana = '大阪(03101 3-3-1)';
                                    break;
                                case 3:
                                    $s->tana = '広島(04101 5-1)';
                                    break;
                                case 4:
                                    $s->tana = '福岡(05101 C-1)';
                                    break;
                                case 5:
                                    $s->tana = '仙台(06101 A-1)';
                                    break;
                                case 6:
                                    $s->tana = '札幌(07101 F)';
                                    break;
                                case 7:
                                    $s->tana = '四国(08101 2-1)';
                                    break;
                                case 8:
                                    $s->tana = '新潟(09101 A-4)';
                                    break;
                                case 9:
                                    $s->tana = '南九州(10101 MB-1)';
                                    break;
                                case 99:
                                    $s->tana = 'ソリューション棚';
                                    break;
                                default:
                                    $s->tana = null;
                                    break;
                            }
                        }
                        if (isset($rs['sizai'])) {
                            $s->sizai = $rs['sizai'];
                        }
                        if (isset($rs['juchu_id'])) {
                            $s->juchu_id = $rs['juchu_id'];
                        }
                        if (isset($rs['bumon_id'])) {
                            $s->bumon_id = $rs['bumon_id'];
                        }
                        if (isset($rs['in_riyuu'])) {
                            $s->in_riyuu = $rs['in_riyuu'];
                        }
                        if (isset($rs['out_riyuu'])) {
                            $s->out_riyuu = $rs['out_riyuu'];
                        }
                        if (isset($rs['kubun'])) {
                            $s->kubun = $rs['kubun'];
                        }
                        if (isset($rs['from_biko'])) {
                            $s->from_biko = $rs['from_biko'];
                        }
                        if (isset($rs['to_biko'])) {
                            $s->to_biko = $rs['to_biko'];
                        }
                    }
                    if (isset($rs['biko'])) {
                        $s->biko = $rs['biko'];
                    }
                    if (isset($rs['parent_kind'])) {
                        $s->parent_kind = $rs['parent_kind'];
                    }
                    if (isset($rs['parent_id'])) {
                        $s->parent_id = $rs['parent_id'];
                    }
                    $s->save();
                    $id = $s['id'];
                    if ($rs['editable']) {
                        if (isset($rs['items'])) {
                            ZidoItem::where('zido_id', $id)->delete();
                            foreach ($rs['items'] as $idx => $hin){
                                $zi = new ZidoItem();
                                $zi->id = $id * 100 + $idx;
                                $zi->zido_id = $id;
                                if (isset($hin['seiban'])) {
                                    $zi->seiban = $hin['seiban'];
                                }
                                if (isset($hin['hinmoku_id'])) {
                                    $zi->hinmoku_id = $hin['hinmoku_id'];
                                }
                                if (isset($hin['suu'])) {
                                    $zi->suu = $hin['suu'];
                                }
                                if (isset($hin['zaiko_id'])) {
                                    $zi->zaiko_id = $hin['zaiko_id'];
                                }
                                if (isset($hin['name'])) {
                                    $zi->name = $hin['name'];
                                }
                                if (isset($hin['model'])) {
                                    $zi->model = $hin['model'];
                                }
                                if (isset($hin['kubun'])) {
                                    $zi->kubun = $hin['kubun'];
                                }
                                if (isset($hin['to_zaiko_id'])) {
                                    $zi->to_zaiko_id = $hin['to_zaiko_id'];
                                }else{
                                    if (isset($hin['to_jigyosyo_id'])) {
                                        $zi->to_jigyosyo_id = $hin['to_jigyosyo_id'];
                                    }
                                    if (isset($hin['to_basho'])) {
                                        $zi->to_basho = $hin['to_basho'];
                                    }
                                    if (isset($hin['to_basho_no'])) {
                                        $zi->to_basho_no = $hin['to_basho_no'];
                                    }
                                    if (isset($hin['to_basho_tana'])) {
                                        $zi->to_basho_tana = $hin['to_basho_tana'];
                                    }
                                }
                                $zi->save();
                            }
                        }
                    }
                }
            }
        });
        return response()->json(
            [
                'ret' => $ret,
                'id' => $id,
                'message' => $mess,
            ],
            200,
            [],
            JSON_UNESCAPED_UNICODE
        );
    }
}
