<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;

use App\Helper;

class ZaikoRireki extends LocalBase{
    protected $appends = [
        /*
        'scaw_flg_str',
        'scaw_flg_str_s',
        'jigyosyo_name',
        'zaiko_rireki_type',
        'zaiko',
        */
    ];
    public function zaiko_rireki_type()
    {
        return $this->hasOne(ZaikoRirekiType::class, 'id', 'type');
    }
    public function getZaikoRirekiTypeAttribute()
    {
        return $this->zaiko_rireki_type()->first();
    }
    public function zaiko()
    {
        return $this->hasOne(Zaiko::class, 'id', 'zaiko_id');
    }
    public function getZaikoAttribute()
    {
        return $this->zaiko()->first();
    }
    public function User()
    {
        return $this->hasOne(User::class, 'id', 'user_id')->withDefault();
    }
    public static function make_query($q, $keys)
    {
        if (isset($keys['zaiko_id']) && 0<$keys['zaiko_id']) {
            $q->Where('zaiko_id', $keys['zaiko_id']);
        }
        if (isset($keys['sort'])) {
            ZaikoRireki::orderByIdx($q, $keys['sort']);
        }else{
            $q->orderBy('id');
        }
    }
    public static function orderByIdx($q, $idx)
    {
        switch ($idx) {
            case 0: // ID 昇順
                $q->orderBy('id');
                break;
            case 1: // ID 降順
                $q->orderBy('id', 'desc');
                break;
            case 2: // 在庫ID 昇順
                $q->orderBy('zaiko_id')->orderBy('id', 'desc');
                break;
            case 3: // 在庫ID 降順
                $q->orderBy('zaiko_id', 'desc')->orderBy('id', 'desc');
                break;
        }
    }
    public static function add($zaiko_id, $type, $zaiko_suu_old,$zaiko_suu_new, $biko, $shukko_id, $rel_zaiko_id, $zido_id, $out_suu=null, $used_suu=null, $hasei_id=null){
        $user = Auth::user();
        if($zaiko_suu_old != $zaiko_suu_new || isset($biko) || isset($out_suu) || isset($used_suu)){
            $r = new ZaikoRireki();
            $r->zaiko_id = $zaiko_id;
            $r->type = $type;
            $r->shukko_id = $shukko_id;
            $r->zaiko_suu_old = $zaiko_suu_old;
            $r->zaiko_suu_new = $zaiko_suu_new;
            $r->biko = $biko;
            $r->user_id = $user->id;
            $r->rel_zaiko_id = $rel_zaiko_id;
            $r->zido_id = $zido_id;
            $r->out_suu = $out_suu;
            $r->used_suu = $used_suu;
            $r->hasei_id = $hasei_id;
            $r->save();
        }
    }
}
