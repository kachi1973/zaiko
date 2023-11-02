<?php

namespace App\Http\Controllers;

use Illuminate\Http\Response;
use Illuminate\Http\Request;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;

use App\Models\User;
use App\Models\Bumon;
use App\Models\Hinmoku;
use App\Models\Hrkbuhinv;
use App\Models\Kzaiko;
use App\Models\Jigyosyo;

class AjaxController extends Controller
{
    public function AjaxGetBumons(Request $req){
        $items = Bumon::where('enable1', true)->orderBy('id')->get();
        return response()->json(
            [
                'items' => $items,
            ],
            200,
            [],
            JSON_UNESCAPED_UNICODE
        );
    }
    public function AjaxGetUsers(Request $req){
        $items = User::query()->where('bumon_id', $req->input('bumon_id'))->where('kengen06', true)->orderBy('id')->get();
        return response()->json(
            [
                'items' => $items,
            ],
            200,
            [],
            JSON_UNESCAPED_UNICODE
        );
    }
    public function AjaxGetUserName(Request $req){
        $item = User::find(intval($req->input('id')));
        return response()->json(
            [
                'item' => $item,
            ],
            200,
            [],
            JSON_UNESCAPED_UNICODE
        );
    }
    public function GetHanko(Request $req){
        $filename = "hanko.png";
        $cmd = null;
        if(isset($req->date)){
            $cmd = base_path("bin\\hanko.exe -n {$req->name} -d {$req->date}");
        }else{
            $cmd = base_path("bin\\hanko.exe -n {$req->name}");
        }
        $proc = proc_open($cmd, array(
            0 => array('pipe', 'r'),
            1 => array('pipe', 'w'),
            //2 => array('pipe', 'w')
        ), $pipes);
        stream_set_write_buffer($pipes[0], 0);
        stream_set_blocking($pipes[1], 0);
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
        return new Response($stdout, 200, array(
            'Content-Type' => 'image/jpg',
            'Content-Disposition' =>  "inline; filename={$filename}",
        ));
    }
    public function AjaxGetHinmoku(Request $req){
        $item = null;
        if(isset($req['type']) && isset($req['id'])){
            if($req['type']==2){
                $item = Hinmoku::find(intval($req['id']));
            }else{
                $scaw = Hrkbuhinv::find(intval($req['id']));
                if(isset($scaw)){
                    $item = new Hinmoku();
                    $item->name = $scaw->fhinrmei;
                    $item->model = $scaw->fmekerhincd;
                    $item->maker = $scaw->fmekermei;
                }
            }
        }
        return response()->json(
            [
                'item' => $item,
            ],
            200,
            [],
            JSON_UNESCAPED_UNICODE
        );
    }
    public function AjaxGetScawHinmoku(Request $req){
        $item = null;
        if(isset($req['id'])){
            $item = Hrkbuhinv::find($req['id']);
        }
        return response()->json(
            [
                'item' => $item,
            ],
            200,
            [],
            JSON_UNESCAPED_UNICODE
        );
    }
    public function AjaxGetKzaiko(Request $req){
        $user = Auth::user();
        $id = 0;
        $kzaiko = null;
        if(isset($req['id'])){
            $id = $req['id'];
            $kzaiko = Kzaiko::find($id);
        }
        return response()->json(
            [
                'item' => $kzaiko,
            ],
            200,
            [],
            JSON_UNESCAPED_UNICODE
        );
    }
    public function AjaxJigyosyos(Request $req){
        return response()->json(
            [
                'items' => Jigyosyo::all3(),
            ],
            200,
            [],
            JSON_UNESCAPED_UNICODE
        );
    }
}
