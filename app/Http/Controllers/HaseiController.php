<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Response;
use Intervention\Image\Facades\Image;
use Illuminate\Support\Facades\Storage;

use App\Helper;

use App\Models\User;
use App\Models\Bumon;
use App\Models\Hinmoku;
use App\Models\Hasei;
use App\Models\HaseiItem;

class HaseiController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('auth');
        $this->FILE_PATH = Helper::HASEI_PATH;
    }
    public function main(Request $request)
    {
        return view('hasei.main', ['user' => Auth::user()]);
    }
    public function AjaxGetHaseis(Request $req){
        $perPage = 30;
        $pageNum = $req->pageNum;
        $q = Hasei::with(['xitems.xzaiko.xhinmoku', 'xitems.xzaiko.xjigyosyo', 'xstatus10user', 'xstatus20user', 'xstatus30user', 'xstatus40user', 'xstatus50user', 'xstatus60user', 'xstatus70user'])->select("haseis.*")
            ->leftJoin('users', 'haseis.status10_user_id', '=', 'users.id')
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
                    case 2: // 予定工場完 昇順
                        $q->orderBy('kojyo_end')->orderBy('id');
                        break;
                    case 3: // 予定工場完 降順
                        $q->orderBy('kojyo_end', 'desc')->orderBy('id', 'desc');
                        break;
                    case 4: // 状態 昇順
                        $q->orderBy('status')->orderBy('id');
                        break;
                    case 5: // 状態 降順
                        $q->orderBy('status', 'desc')->orderBy('id', 'desc');
                        break;
                    }
            }
            if (isset($req->search['user_id']) && 0 < $req->search['user_id']) {
                // 担当者
                $q->where('status10_user_id', $req->search['user_id']);
            } else if (isset($req->search['bumon_id']) && 0 < $req->search['bumon_id']) {
                // 所属
                $user_ids = collect(User::where('bumon_id', $req->search['bumon_id'])->get())->map(function ($c) {
                    return $c->id;
                });
                $q->whereIn('status10_user_id', $user_ids);
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
        if($req->type == 'excel'){
            $items = $q->get();
            foreach ($items as $item) {
                $item->append('items');
            }
            $json = json_encode($items, 0);
            if(config('app.json_debug')){
                file_put_contents(base_path("bin\\haseis.json"), $json);
            }
            [$ret, $stdout, $stderr] = Helper::RunCmd(base_path("bin\\zaikocgi.exe 13"), $json);
            $headers = [
                'Content-Type' => 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
                'Content-Disposition' => 'attachment; filename="haseis.xlsx"'
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
    public function get_hasei($id){
        $item = Hasei::find($id);
        if(isset($item)){
            $item->append('items');
            $item->append('file_urls');
        }
        return $item;
    }
    public function AjaxGetHasei(Request $req){
        return response()->json(
            [
                'item' => $this->get_hasei($req->id),
            ],
            200,
            [],
            JSON_UNESCAPED_UNICODE
        );
    }
    public function descprint(Request $req)
    {
        $filename = "hasei.pdf";
        $proc = proc_open(base_path("bin\\zaikocgi.exe 7"), array(
            0 => array('pipe', 'r'),
            1 => array('pipe', 'w'),
            //2 => array('pipe', 'w')
        ), $pipes);
        stream_set_write_buffer($pipes[0], 0);
        stream_set_blocking($pipes[1], 0);
        $json = json_encode($this->get_hasei($req->id), 0);
        if(config('app.json_debug')){
            file_put_contents(base_path("bin\\hasei.json"), $json);
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
    public function set_status($rs, Hasei $s, $user, $command)
    {
        switch ($command) {
            case 1: // 進む
                switch ($s->status) {
                    case 10: // 課長承認待ち
                        $s->status = 20;
                        break;
                    case 20: // 部長承認待ち
                    case 30:
                    case 40:
                    case 50:
                    case 60:
                        $s->status = 70;
                        break;
                    case 70: // 完了
                        break;
                    default: // 申請待ち
                        $s->status = 10;
                        break;
                }
            break;
            case 2: // 戻る
                switch ($s->status) {
                    case 10: // 課長承認待ち
                        $s->status = 0;
                        break;
                    case 20: // 部長承認待ち
                        $s->status = 10;
                        break;
                    case 30:
                    case 40:
                    case 50:
                    case 60:
                    case 70: // 完了
                        $s->status = 20;
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
            case 10: // 申請済、課長承認待ち
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
                if($command==1){
                    $s->status10_date = Carbon::now();
                    $s->status10_user_id = $user->id;
                }
                break;
            case 20: // 課長承済、部長承認待ち
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
                if($command==1){
                    // 課長承認完了で->部長承認->完了
                    $s->status20_date = Carbon::now();
                    $s->status20_user_id = $user->id;
                    $s->status70_date = Carbon::now();
                    $s->status70_user_id = config('app.bucho_id');
                    $s->status = 70;
                    foreach ($s->items as $item){
                        $item->zaiko->add($item->suu, $s->id);
                    }
                }
                break;
            case 30:
            case 40:
            case 50:
            case 60:
            case 70: // 完了
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
    }
    public function AjaxPutHasei(Request $req){
        $user = Auth::user();
        $id = 0;
        if(isset($req['item'])){
            $rs = $req['item'];
            $s = null;
            if (isset($rs['id']) && 0< $rs['id']) {
                $s = Hasei::find($rs['id']);
            }
            if(!isset($s)){
                $s = new Hasei();
            }
            if(!isset($s->status10_user_id)){
                $s->status10_user_id = $user->id;
            }
            if (isset($rs['command'])) {
                $this->set_status($rs, $s, $user, $rs['command']);
            }
            if($rs['editable']){
                if (isset($rs['biko'])) {
                    $s->biko = $rs['biko'];
                }
            }
            $s->save();
            $id = $s['id'];
            if ($rs['editable']) {
                if (isset($rs['items'])) {
                    HaseiItem::where('hasei_id', $id)->delete();
                    foreach ($rs['items'] as $idx => $hin){
                        $ji = new HaseiItem();
                        $ji->id = $id * 100 + $idx;
                        $ji->hasei_id = $id;
                        if (isset($hin['zaiko_id'])) {
                            $ji->zaiko_id = $hin['zaiko_id'];
                        }
                        if (isset($hin['suu'])) {
                            $ji->suu = $hin['suu'];
                        }
                        $ji->save();
                    }
                }
            }
        }
        return response()->json(
            [
                'item' => $this->get_hasei($id),
            ],
            200,
            [],
            JSON_UNESCAPED_UNICODE
        );
    }
}
