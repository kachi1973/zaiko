<?php

namespace App\Http\Controllers\Admin;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Response;
use Carbon\Carbon;

use App\Helper;
use App\Models\Hinmoku;

class HinmokuController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }
    public function index(Request $req)
    {
        $sorts = array(
            0 => "品目コード 昇順",
            1 => "品目コード 降順",
            2 => "型式 昇順",
            3 => "型式 降順",
            4 => "名称 昇順",
            5 => "名称 降順",
            6 => "メーカ 昇順",
            7 => "メーカ 降順",
        );
        $q = Hinmoku::query();
        switch ($req->search_sort) {
            case 1: // 品目コード 降順
                $q->orderBy('id', 'desc');
                break;
            case 2: // 型式 昇順
                $q->orderBy('model');
                break;
            case 3: // 型式 降順
                $q->orderBy('model', 'desc');
                break;
            case 4: // 名称 昇順
                $q->orderBy('name');
                break;
            case 5: // 名称 降順
                $q->orderBy('name', 'desc');
                break;
            case 6: // メーカ 昇順
                $q->orderBy('maker');
                break;
            case 7: // メーカ 降順
                $q->orderBy('maker', 'desc');
                break;
            default: // 品目コード 昇順
                $q->orderBy('id');
                break;
        }
        if(isset($req->submit2)){
            // エクセル出力
            $now = Carbon::now();
            $filename = "hinmokulist.{$now->format('YmdHi')}.xlsx";
            ob_start();
            passthru(base_path("bin\\zaikocgi.exe 8"));
            $output = ob_get_contents();
            ob_end_clean();
            return new Response($output, 200, array(
                'Content-Type' => 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
                'Content-Disposition' =>  'attachment; filename*=UTF-8\'\'' . urlencode($filename),
            ));
        }
        if (isset($req->search_hinmoku_id)) {
            $q->Where('id', 'ilike', Helper::get_like_str($req->search_hinmoku_id));
        }
        if (isset($req->search_model)) {
            $q->Where('model', 'ilike', Helper::get_like_str($req->search_model));
        }
        if (isset($req->search_name)) {
            $q->Where('name', 'ilike', Helper::get_like_str($req->search_name));
        }
        if (isset($req->search_maker)) {
            $q->Where('maker', 'ilike', Helper::get_like_str($req->search_maker));
        }
        $items = $q->paginate(50);
        $items->appends($req->query());
        return view('admin.hinmoku.index', [
            'req' => $req,
            'user' => Auth::user(),
            'items' => $items,
            'sorts' => $sorts,
        ]);
    }
    public function edit($id=null)
    {
        $item = new Hinmoku();
        if(isset($id)){
            $item = Hinmoku::find($id);
        }
        return view('admin.hinmoku.edit', [
            'user' => Auth::user(),
            'item' => $item,
        ]);
    }
    public function store(Request $req)
    {
        $item = Hinmoku::find($req->id);
        if (!isset($item)) {
            $item = new Hinmoku();
            $item->id = $req->id;
        }
        $item->name = $req->name;
        $item->model = $req->model;
        $item->maker = $req->maker;
        $item->save();
        return redirect($req->ReturnURL);
    }
    public function AjaxPutFile(Request $req){
        $file = $req->file('file');
        $path = $file->getPathname();
        ob_start();
        passthru(base_path("bin\\zaikocgi.exe 9 {$path}"));
        $output = ob_get_contents();
        ob_end_clean();
        return response()->json(
            json_decode($output),
            200,
            [],
            JSON_UNESCAPED_UNICODE
        );
    }
}
