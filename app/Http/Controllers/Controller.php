<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Foundation\Bus\DispatchesJobs;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Routing\Controller as BaseController;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Auth;

use App\Helper;
use App\Models\Jigyosyo;
use App\Models\Bumon;
use App\Models\Zaiko;

class Controller extends BaseController
{
    use AuthorizesRequests, DispatchesJobs, ValidatesRequests;
    protected $FILE_PATH = '';
    public function AjaxInit(Request $req){
        return response()->json(
            [
                'user' => Auth::user(),
                'jigyosyos' => session('jigyosyos'),
                'bumons' => session('bumons'),
                'tanas' => session('tanas'),
                'kkamokus' => session('kkamokus'),
            ],
            200,
            [],
            JSON_UNESCAPED_UNICODE
        );
    }
    public function AjaxPutFile(Request $req){
        $file = $req->file('file');
        $name = $file->getClientOriginalName();
        // ディレクトリ作成
        $path = Helper::get_path($this->FILE_PATH, $req->id);
        if(!Storage::disk('public')->exists($path)){
            Storage::disk('public')->makeDirectory($path, 0775, true, true);
        }
        // 保存
        $file->storeAs(
            $path,
            $name,
            'public'
        );
        return response()->json(
            [
                'items' => Helper::get_filelist($this->FILE_PATH, $req->id),
            ],
            200,
            [],
            JSON_UNESCAPED_UNICODE
        );
    }
    public function AjaxDeleteFile(Request $req){
        $filename = Helper::get_path($this->FILE_PATH, $req->id)."/". $req->name;
        Storage::disk('public')->delete($filename);
        return response()->json(
            [
                'items' => Helper::get_filelist($this->FILE_PATH, $req->id),
            ],
            200,
            [],
            JSON_UNESCAPED_UNICODE
        );
    }
}
