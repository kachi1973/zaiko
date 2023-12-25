<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Carbon\Carbon;

use App\Helper;

use App\Models\User;
use App\Models\Bumon;
use App\Models\Jigyosyo;
use App\Models\Zaiko;
use App\Models\ZaikoRireki;
use App\Models\Shukko;
use App\Models\ShukkoItem;
use App\Models\Hinmoku;
use App\Models\Hrkbuhinv;
use App\Models\Kzaiko;
use App\Models\ShukkoItemKzaiko;
use App\Models\KzaikoRireki;
use App\Models\System;

class ShukkoController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('auth');
        $this->FILE_PATH = Helper::SHUKKO_PATH;
    }
    public function main(Request $request)
    {
        //dd(Zaiko::get_tanas());
        //dd(json_encode(Zaiko::get_tanas()));
        return view('shukko.main', ['user' => Auth::user()]);
    }
    public function AjaxGetZaikos(Request $req)
    {
        $perPage = 30;
        $pageNum = $req->pageNum;
        $q = Zaiko::query();
        if (isset($req->search)) {
            $s = $req->search;
            $tana = $s['tana'];
            if(isset($s['perPage'])){
                $perPage = $s['perPage'];
            }
            $q = Zaiko::make_query([
                'sort' => isset($s['sort']) ? $s['sort'] : null,
                'scaw_flg' => isset($s['scaw_flg']) ? $s['scaw_flg'] : null,
                'jigyosyo_id' => (0<count($tana) ? $tana[0] : null),
                'basho' => (1<count($tana) ? $tana[1] : null),
                'basho_no' => (2<count($tana) ? $tana[2] : null),
                'basho_tana' => (3<count($tana) ? $tana[3] : null),
                'hinmoku_id' => isset($s['hinmoku_id']) ? $s['hinmoku_id'] : null,
                'model' => isset($s['model']) ? $s['model'] : null,
                'name' => isset($s['name']) ? $s['name'] : null,
                'maker' => isset($s['maker']) ? $s['maker'] : null,
                'biko' => isset($s['biko']) ? $s['biko'] : null,
            ]);
        }
        $q->where('status', 0);
        $total_cnt = $q->count();
        $items = $q->skip(($pageNum - 1) * $perPage)->take($perPage)->get();
        foreach ($items as $item) {
            $item->append('kashi_suu');
            $item->append('sinsei_suu');
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
    public function AjaxGetSZaikos(Request $req)
    {
        $perPage = 30;
        $pageNum = $req->pageNum;
        $q = Hrkbuhinv::query();
        if (isset($req->search)) {
            $s = $req->search;
            if(isset($s['perPage'])){
                $perPage = $s['perPage'];
            }
            if (isset($s['sort'])) {
                switch ($s['sort']) {
                    case 0: // 品目コード 昇順
                        $q->orderBy('fhincd');
                        break;
                    case 1: // 品目コード 降順
                        $q->orderBy('fhincd', 'desc');
                        break;
                    case 2: // 型式 昇順
                        $q->orderBy('fhinrmei')->orderBy('fmekerhincd');
                        break;
                    case 3: // 型式 降順
                        $q->orderBy('fhinrmei', 'desc')->orderBy('fmekerhincd');
                        break;
                    case 4: // 名称 昇順
                        $q->orderBy('fmekerhincd')->orderBy('fhinrmei');
                        break;
                    case 5: // 名称 降順
                        $q->orderBy('fmekerhincd', 'desc')->orderBy('fhinrmei');
                        break;
                    case 6: // メーカ 昇順
                        $q->orderBy('fmekermei')->orderBy('fmekerhincd');
                        break;
                    case 7: // メーカ 降順
                        $q->orderBy('fmekermei', 'desc')->orderBy('fmekerhincd');
                        break;
                }
            }else{
                $q->orderBy('fhincd');
            }
            if (isset($s['id'])) {
                $q->Where('fhincd', 'like', Helper::get_like_str(Helper::get_hankaku($s['id'])));
            }
            if (isset($s['model'])) {
                $name = $s['model'];
                $q->where(function($qq) use($name){
                    $han = Helper::get_hankaku($name);
                    $hanU = mb_strtoupper($han);
                    $hanL = mb_strtolower($han);
                    $zenU = Helper::get_zenkaku($hanU);
                    $zenL = Helper::get_zenkaku($hanL);
                    $qq->Where('fmekerhincd', 'like', Helper::get_like_str($name));
                    $qq->orWhere('fmekerhincd', 'like', Helper::get_like_str($hanU));
                    $qq->orWhere('fmekerhincd', 'like', Helper::get_like_str($hanL));
                    $qq->orWhere('fmekerhincd', 'like', Helper::get_like_str($zenU));
                    $qq->orWhere('fmekerhincd', 'like', Helper::get_like_str($zenL));
                });
            }
            if (isset($s['name'])) {
                $name = $s['name'];
                $q->where(function($qq) use($name){
                    $han = Helper::get_hankaku($name);
                    $hanU = mb_strtoupper($han);
                    $hanL = mb_strtolower($han);
                    $zenU = Helper::get_zenkaku($hanU);
                    $zenL = Helper::get_zenkaku($hanL);
                    $qq->Where('fhinrmei', 'like', Helper::get_like_str($name));
                    $qq->orWhere('fhinrmei', 'like', Helper::get_like_str($hanU));
                    $qq->orWhere('fhinrmei', 'like', Helper::get_like_str($hanL));
                    $qq->orWhere('fhinrmei', 'like', Helper::get_like_str($zenU));
                    $qq->orWhere('fhinrmei', 'like', Helper::get_like_str($zenL));
                });
            }
            if (isset($s['maker'])) {
                $name = $s['maker'];
                $q->where(function($qq) use($name){
                    $han = Helper::get_hankaku($name);
                    $hanU = mb_strtoupper($han);
                    $hanL = mb_strtolower($han);
                    $zenU = Helper::get_zenkaku($hanU);
                    $zenL = Helper::get_zenkaku($hanL);
                    $qq->Where('fmekermei', 'like', Helper::get_like_str($name));
                    $qq->orWhere('fmekermei', 'like', Helper::get_like_str($hanU));
                    $qq->orWhere('fmekermei', 'like', Helper::get_like_str($hanL));
                    $qq->orWhere('fmekermei', 'like', Helper::get_like_str($zenU));
                    $qq->orWhere('fmekermei', 'like', Helper::get_like_str($zenL));
                });
            }
        }
        $total_cnt = $q->count();
        $items = $q->skip(($pageNum - 1) * $perPage)->take($perPage)->get();
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
    public function AjaxGetShukkos(Request $req){
        System::add_search_cnt();
        $perPage = 30;
        $pageNum = $req->pageNum;
        $q = Shukko::with(['xjigyosyo', 'user', 'user.bumon', 'xstatus10user', 'xstatus20user', 'xstatus30user', 'xstatus40user', 'xstatus50user', 'xstatus60user', 'xstatus70user'])
            ->select("shukkos.*")
            ->join('users', 'shukkos.user_id', '=', 'users.id')
            ->join('bumons', 'users.bumon_id', '=', 'bumons.id');
        if (isset($req->search)) {
            if (isset($req->search['sort'])) {
                switch($req->search['sort']){
                    case 0: // ID 昇順
                        $q->orderBy('id');
                        break;
                    case 1: // ID 降順
                        $q->orderBy('id', 'desc');
                        break;
                    case 2: // 申請日,出庫希望日 昇順
                        $q->orderBy('status10_date')->orderBy('shukko_date');
                        break;
                    case 3: // 申請日,出庫希望日 降順
                        $q->orderBy('status10_date', 'desc')->orderBy('shukko_date', 'desc');
                        break;
                    case 4: // 希望日 昇順
                        $q->orderBy('shukko_date');
                        break;
                    case 5: // 希望日 降順
                        $q->orderBy('shukko_date', 'desc');
                        break;
                    case 6: // 出庫日 昇順
                        $q->orderBy('status20_date');
                        break;
                    case 7: // 出庫日 降順
                        $q->orderBy('status20_date', 'desc');
                        break;
                    case 8: // 返却日,出庫日 昇順
                        $q->orderBy('status30_date')->orderBy('status20_date');
                        break;
                    case 9: // 返却日,出庫日 降順
                        $q->orderBy('status30_date', 'desc')->orderBy('status20_date', 'desc');
                        break;
                    case 10: // 返却承認日,出庫日 昇順
                        $q->orderBy('status40_date')->orderBy('status20_date');
                        break;
                    case 11: // 返却承認日,出庫日 降順
                        $q->orderBy('status40_date', 'desc')->orderBy('status20_date', 'desc');
                        break;
                    case 12: // 完了日 昇順
                        $q->orderBy('status50_date');
                        break;
                    case 13: // 完了日 降順
                        $q->orderBy('status50_date', 'desc');
                        break;
                    case 14: // 課長承認日 昇順
                        $q->orderBy('status60_date');
                        break;
                    case 15: // 課長承認日 降順
                        $q->orderBy('status60_date', 'desc');
                        break;
                    case 16: // 受注番号 昇順
                        $q->orderBy('seiban');
                        break;
                    case 17: // 受注番号 降順
                        $q->orderBy('seiban', 'desc');
                        break;
                    case 18: // 所属 昇順
                        $q->orderBy('bumons.name');
                        break;
                    case 19: // 所属 降順
                        $q->orderBy('bumons.name', 'desc');
                        break;
                    case 20: // 担当者 昇順
                        $q->orderBy('users.name');
                        break;
                    case 21: // 担当者 降順
                        $q->orderBy('users.name', 'desc');
                        break;
                    case 22: // 状況 昇順
                        $q->orderBy('status');
                        break;
                    case 23: // 状況 降順
                        $q->orderBy('status', 'desc');
                        break;
                }
            }
            // id
            if (isset($req->search['id']) && 0 < $req->search['id']) {
                $q->where('shukkos.id', $req->search['id']);
            }else{
                // 事業所
                if (isset($req->search['jigyosyo_id']) && 0 < $req->search['jigyosyo_id']) {
                    $q->where('shukkos.jigyosyo_id', $req->search['jigyosyo_id']);
                }
                if (isset($req->search['user_id']) && 0 < $req->search['user_id']) {
                    // 担当者
                    $q->where('user_id', $req->search['user_id']);
                } else if (isset($req->search['bumon_id']) && 0 < $req->search['bumon_id']) {
                    // 所属
                    $user_ids = collect(User::where('bumon_id', $req->search['bumon_id'])->get())->map(function ($c) {
                        return $c->id;
                    });
                    $q->whereIn('user_id', $user_ids);
                }
                // 受注番号
                if (isset($req->search['seiban'])) {
                    $q->where('seiban', 'ilike', '%' . $req->search['seiban'] . '%');
                }
                // 日付
                if (isset($req->search['date_sel'])) {
                    $fieldname = (['shukko_date', 'status10_date', 'status20_date', 'status30_date', 'status40_date', 'status50_date', 'status60_date', 'status70_date'])[$req->search['date_sel']];
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
                // 状態
                $stss = [];
                if (isset($req->search['stschk00']) && $req->search['stschk00']) $stss[] = 0;
                if (isset($req->search['stschk10']) && $req->search['stschk10']) $stss[] = 10;
                if (isset($req->search['stschk20']) && $req->search['stschk20']) $stss[] = 20;
                if (isset($req->search['stschk30']) && $req->search['stschk30']) $stss[] = 30;
                if (isset($req->search['stschk40']) && $req->search['stschk40']) $stss[] = 40;
                if (isset($req->search['stschk50']) && $req->search['stschk50']) $stss[] = 50;
                if (isset($req->search['stschk60']) && $req->search['stschk60']) $stss[] = 60;
                if (isset($req->search['stschk70']) && $req->search['stschk70']) $stss[] = 70;
                if (isset($req->search['stschk99']) && $req->search['stschk99']) $stss[] = 99;
                if(count($stss) < 9){
                    $q->whereIn('status', $stss);
                }
            }
        }
        if($req->type == 'excel'){
            $json = json_encode($q->get(), 0);
            if(config('app.json_debug')){
                file_put_contents(base_path("bin\\shukkos.json"), $json);
            }
            [$ret, $stdout, $stderr] = Helper::RunCmd(base_path("bin\\zaikocgi.exe 10"), $json);
            $headers = [
                'Content-Type' => 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
                'Content-Disposition' => 'attachment; filename="shukkos.xlsx"'
            ];
            return response()->make($stdout, 200, $headers);
        }else{
            $total_cnt = $q->count();
            $items = $q->skip(($pageNum - 1) * $perPage)->take($perPage)->get();
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
    public function AjaxDownloadShukkos(Request $req){

    }
    public function get_shukko($id){
        $item = Shukko::find($id);
        $item->append('items');
        $item->append('file_urls');
        return $item;
    }
    public function AjaxGetShukko(Request $req){
        System::add_show_cnt(Auth::user());
        return response()->json(
            [
                'item' => $this->get_shukko($req->id),
            ],
            200,
            [],
            JSON_UNESCAPED_UNICODE
        );
    }
    public function descprint(Request $req){
        $filename = "shukko.pdf";
        $proc = proc_open(base_path("bin\\zaikocgi.exe 1"), array(
            0 => array('pipe', 'r'),
            1 => array('pipe', 'w'),
            //2 => array('pipe', 'w')
        ), $pipes);
        stream_set_write_buffer($pipes[0], 0);
        stream_set_blocking($pipes[1], 0);
        $json = json_encode($this->get_shukko($req->id), 0);
        if(config('app.json_debug')){
            file_put_contents(base_path("bin\\shukko.json"), $json);
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
            'Content-Disposition' =>  'inline; filename*=UTF-8\'\''.urlencode($filename),
        ));
    }
    public function set_status($rs, $s, $user, $command)
    {
        $all_use = false;
        $scaw_use = false;
        $scaw_seiban_err = false;
        $file_cnt = count($s->file_urls);
        if (isset($rs['items'])) {
            foreach ($rs['items'] as $idx => $z) {
                if (isset($z['used_suu'])) {
                    if (0 < $z['used_suu']) {
                        $all_use = true;
                        if ($z['zaiko']['scaw_flg'] == 1) {
                            $scaw_use = true;
                            if (!isset($z['seiban'])) {
                                $scaw_seiban_err = true;
                            }
                        }
                    }
                }
            }
        }
        switch ($command) {
            case 1: // 進む
                switch ($s->status) {
                    case 10: // 出庫待ち
                        $s->status = 20;
                        break;
                    case 20: // 返却待ち
                        $s->status = 30;
                        break;
                    case 30: // 返却承認待ち
                        $s->status = 40;
                        break;
                    case 40: // 製番入力待ち
                        $s->status = 50;
                        break;
                    case 50: // 課長承認待ち
                        $s->status = 60;
                        break;
                    case 60: // 完了待ち
                        $s->status = 70;
                        break;
                    case 70: // 完了
                    case 99: // キャンセル
                        break;
                    default: // 出庫申請待ち
                        $s->status = 10;
                    break;
            }
                break;
            case 2: // 戻る
                switch ($s->status) {
                    case 10: // 出庫待ち
                        $s->status = 0;
                        break;
                    case 20: // 返却待ち
                        $s->status = 10;
                        break;
                    case 30: // 返却承認待ち
                        $s->status = 20;
                        break;
                    case 40: // 製番入力待ち
                        break;
                    case 50: // 課長承認待ち
                        $s->status = 40;
                        break;
                    case 60: // 完了待ち
                        $s->status = 50;
                        break;
                    case 70: // 完了
                        if($scaw_use){
                            $s->status = 50;
                        }else{
                            $s->status = 40;
                        }
                        break;
                        case 99: // キャンセル
                            $s->status = 0;
                            break;
                        default: // 出庫申請待ち
                            $s->status = 99;
                        break;
                }
                break;
        }
        switch($s->status){
            case 10: // 出庫待ち
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
            case 20: // 返却待ち
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
                break;
            case 30: // 返却承認待ち
                if($command==1){
                    $s->status30_date = Carbon::now();
                    $s->status30_user_id = $user->id;
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
            case 40: // 製番入力待ち
                if($command==1){
                    $s->status40_date = Carbon::now();
                    $s->status40_user_id = $user->id;
                }
                $s->status50_date = null;
                $s->status50_user_id = null;
                $s->status60_date = null;
                $s->status60_user_id = null;
                $s->status70_date = null;
                $s->status70_user_id = null;
                switch ($command) {
                    case 1: // 進む
                        if(!$all_use){
                            $s->status70_date = Carbon::now();
                            $s->status70_user_id = $user->id;
                            $s->status = 70;
                        }else if (isset($rs['seiban'])) {
                            if($scaw_use){
                                if(!$scaw_seiban_err && 0<$file_cnt){
                                    // 受注製番入力済み＋SCAW品使用＋投入製番有り＋ファイル有り
                                    $s->status50_date = Carbon::now();
                                    $s->status50_user_id = $user->id;
                                    $s->status = 50;
                                }
                            }else{
                                $s->status50_date = Carbon::now();
                                $s->status50_user_id = $user->id;
                                $s->status70_date = Carbon::now();
                                $s->status70_user_id = $user->id;
                                $s->status = 70;
                            }
                        }
                        break;
                    case 2: // 戻る
                        break;
                }
                break;
            case 50: // 課長承認待ち
                if($command==1){
                    $s->status50_date = Carbon::now();
                    $s->status50_user_id = $user->id;
                }
                $s->status60_date = null;
                $s->status60_user_id = null;
                $s->status70_date = null;
                $s->status70_user_id = null;
                switch ($command) {
                    case 1: // 進む
                        if (!$scaw_use) {
                            $s->status70_date = Carbon::now();
                            $s->status70_user_id = $user->id;
                            $s->status = 70;
                        }
                        break;
                    case 2: // 戻る
                        break;
                }
                break;
            case 60: // 完了待ち
                if($command==1){
                    $s->status60_date = Carbon::now();
                    $s->status60_user_id = $user->id;
                    $s->status70_date = Carbon::now();
                    $s->status70_user_id = $user->id;
                }
                switch ($command) {
                    case 1: // 進む
                        $s->status = 70;
                        break;
                    case 2: // 戻る
                        break;
                }
                break;
            case 70: // 完了
                if($command==1){
                    $s->status70_date = Carbon::now();
                    $s->status70_user_id = $user->id;
                }
                break;
            default: // 出庫申請待ち
                $s->status10_date = null;
                $s->status10_user_id = null;
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
    }
    public function AjaxPutShukko(Request $req){
        $user = Auth::user();
        $id = 0;
        if(isset($req['shukko'])){
            $rs = $req['shukko'];
            $s = null;
            if (isset($rs['id']) && 0< $rs['id']) {
                $s = Shukko::find($rs['id']);
            }
            if(!isset($s)){
                $s = new Shukko();
            }
            if (isset($rs['seiban'])) {
                $s->seiban = $rs['seiban'];
            }
            if (isset($rs['status'])) {
                $s->status = $rs['status'];
            }
            if (isset($rs['user_id'])) {
                $s->user_id = $rs['user_id'];
            }else{
                $s->user_id = $user->id;
            }
            if (isset($rs['shukko_date'])) {
                $s->shukko_date = Helper::ISO8601toDATE($rs['shukko_date']);
            }
            if (isset($rs['jigyosyo_id'])) {
                $s->jigyosyo_id = $rs['jigyosyo_id'];
            }
            if (isset($rs['command'])) {
                $this->set_status($rs, $s, $user, $rs['command']);
            }
            $s->save();
            $id = $s['id'];
            if (isset($rs['items'])) {
                $item_ids = [];
                foreach ($rs['items'] as $idx => $z){
                    $item_id = $id * 100 + $idx;
                    $item_ids[] = $item_id;
                    $si = ShukkoItem::find($item_id);
                    if(!isset($si)){
                        $si = new ShukkoItem();
                        $si->id = $item_id;
                    }
                    $si->shukko_id = $id;
                    $si->zaiko_id = $z['zaiko']['id'];
                    $si->biko = $z['biko'];
                    $si->req_suu = $z['req_suu'];
                    //$si->used_suu = $z['used_suu'];
                    $si->save();
                }
                ShukkoItem::where('shukko_id', $id)->whereNotIn('id', $item_ids)->delete();
            }
        }
        return response()->json(
            [
                'item' => $this->get_shukko($id),
            ],
            200,
            [],
            JSON_UNESCAPED_UNICODE
        );
    }
    public function AjaxPutShukko2(Request $req)
    {
        $user = Auth::user();
        $id = 0;
        if (isset($req['shukko'])) {
            DB::transaction(function () use ($user, $req, &$id) {
                $req_s = $req['shukko'];
                if ((isset($req_s['id']) && 0 < $req_s['id']) && isset($req_s['status'])){
                    $s = Shukko::find($req_s['id']);
                    $s_status_old = $s->status;
                    if(array_key_exists('seiban', $req_s)){
                        $s->seiban = $req_s['seiban'];
                    }
                    $s->status = $req_s['status'];
                    if(isset($req_s['command'])){
                        $this->set_status($req_s, $s, $user, $req_s['command']);
                    }
                    $s->save();
                    $id = $s['id'];
                    if (isset($req_s['items'])) {
                        foreach ($req_s['items'] as $idx => $req_si) {
                            $si = ShukkoItem::find($req_si['id']);
                            $z = Zaiko::find($si->zaiko_id);
                            $si->out_suu = $req_si['out_suu'];
                            $si->used_suu = $req_si['used_suu'];
                            $si->seiban = $req_si['seiban'];
                            $si->biko = $req_si['biko'];
                            if($s->status < 99){
                                if($s_status_old < $s->status){
                                    // ステータス前進
                                    switch($s_status_old){
                                        case 10: // 出庫待ち -> 返却待ち
                                            ShukkoItemKzaiko::find2($si->id)->delete();
                                            if (isset($req_si['items'])) {
                                                foreach ($req_si['items'] as $idx => $req_kz) {
                                                    $ikz = new ShukkoItemKzaiko();
                                                    $ikz->item_id = $si->id;
                                                    $ikz->kzaiko_id = $req_kz['kzaiko_id'];
                                                    $ikz->status = $req_kz['status'];
                                                    $ikz->save();
                                                    $kz = Kzaiko::find($req_kz['kzaiko_id']);
                                                    if(isset($kz)){
                                                        $kz_status_old = $kz->status;
                                                        $kz->update(['status' => Kzaiko::STATUS_SHUKKO]);
                                                        KzaikoRireki::add(KzaikoRireki::TYPE_STATUS, $kz, $kz_status_old, null, null, $s->id, null);
                                                    }
                                                }
                                                ZaikoRireki::add($z->id, 1, $z->zaiko_suu, null, null, $id, null, null, $si->out_suu, null);
                                            }
                                            break;
                                        case 20: // 返却待ち -> 返却承認待ち
                                            if (isset($req_si['items'])) {
                                                foreach ($req_si['items'] as $idx => $req_kz) {
                                                    $status = $req_kz['status'];
                                                    switch($status){
                                                        case Kzaiko::STATUS_SHUKKO:
                                                            $status = Kzaiko::STATUS_USED;
                                                            break;
                                                    }
                                                    ShukkoItemKzaiko::find2($si->id, $req_kz['kzaiko_id'])->update(['status' => $status]);
                                                }
                                            }
                                            break;
                                        case 30: // 返却承認待ち -> 完了承認待ち
                                            if (isset($si->used_suu) && 0 < $si->used_suu) {
                                                $zaiko_suu_old = $z->zaiko_suu;
                                                $zaiko_suu_new = $z->zaiko_suu - $si->used_suu;
                                                $z->zaiko_suu = $zaiko_suu_new;
                                                $z->save();
                                                ZaikoRireki::add($z->id, 1, $zaiko_suu_old, $zaiko_suu_new, null, $id, null, null, null, $si->used_suu);
                                            }else{
                                                ZaikoRireki::add($z->id, 1, $z->zaiko_suu, null, null, $id, null, null, null, $si->used_suu);
                                            }
                                            if (isset($req_si['items'])) {
                                                foreach ($req_si['items'] as $idx => $req_kz) {
                                                    $status = $req_kz['status'];
                                                    $status2 = $status;
                                                    switch($status){
                                                        case Kzaiko::STATUS_RETURN:
                                                            $status2 = Kzaiko::STATUS_ZAIKO;
                                                            break;
                                                    }
                                                    ShukkoItemKzaiko::find2($si->id, $req_kz['kzaiko_id'])->update(['status' => $status]);
                                                    $kz = Kzaiko::find($req_kz['kzaiko_id']);
                                                    if(isset($kz)){
                                                        $kz_status_old = $kz->status;
                                                        $kz->update(['status' => $status2]);
                                                        KzaikoRireki::add(KzaikoRireki::TYPE_STATUS, $kz, $kz_status_old, null, null, $s->id, null);
                                                    }
                                                }
                                            }
                                            break;
                                    }
                                }else if($s->status < $s_status_old){
                                    // ステータス後退
                                    switch($s->status){
                                        case 10: // 出庫待ち
                                            $si->out_suu = null;
                                            $si->used_suu = null;
                                            foreach ($si->items as $sikz) {
                                                $status_old = $sikz->kzaiko->status;
                                                $sikz->kzaiko->update(['status' => Kzaiko::STATUS_ZAIKO]);
                                                KzaikoRireki::add(KzaikoRireki::TYPE_STATUS, $sikz->kzaiko, $status_old, null, "出庫取り消し", $s->id, null);
                                            }
                                            ZaikoRireki::add($si->zaiko_id, 1, null, null, "出庫取り消し", $id, null, null, null, null);
                                            ShukkoItemKzaiko::find2($si->id)->delete();
                                            break;
                                        case 20: // 返却待ち
                                            $si->used_suu = null;
                                            foreach ($si->items as $sikz) {
                                                ShukkoItemKzaiko::find2($sikz->item_id, $sikz->kzaiko_id)->update(['status' => Kzaiko::STATUS_SHUKKO]);
                                                $sikz->kzaiko->update(['status' => Kzaiko::STATUS_SHUKKO]);
                                            }
                                            break;
                                        case 30: // 返却承認待ち
                                            break;
                                        case 40: // 製番入力待ち
                                            break;
                                        case 50: // 課長承認待ち
                                            break;
                                        case 60: // 完了待ち
                                            break;
                                        case 70: // 完了
                                            break;
                                        default: // 出庫申請待ち
                                            $si->out_suu = null;
                                            $si->used_suu = null;
                                            break;
                                }
                                }else{
                                    // 現状保存
                                    if (isset($req_si['items'])) {
                                        foreach ($req_si['items'] as $idx => $req_kz) {
                                            $status = $req_kz['status'];
                                            ShukkoItemKzaiko::find2($si->id, $req_kz['kzaiko_id'])->update(['status' => $status]);
                                        }
                                    }
                                }
                            }
                            $si->save();
                        }
                    }
                }
            });
        }
        return response()->json(
            [
                'item' => $this->get_shukko($id),
            ],
            200,
            [],
            JSON_UNESCAPED_UNICODE
        );
    }
}
