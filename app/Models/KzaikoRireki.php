<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;

use App\Helper;

class KzaikoRireki extends LocalBase{
    public const TYPE_NEW = 1;
    public const TYPE_STATUS = 2;
    public const TYPE_MAINT = 3;
    public const TYPE_ZIDO = 4;
    protected $appends = [
        /*
        'kzaiko_rireki_type',
        'kzaiko',
        */
    ];
    public function kzaiko_rireki_type(){
        return $this->hasOne(KzaikoRirekiType::class, 'id', 'type');
    }
    public function getKzaikoRirekiTypeAttribute(){
        return $this->kzaiko_rireki_type()->first();
    }
    public function kzaiko(){
        return $this->hasOne(Kzaiko::class, 'id', 'kzaiko_id');
    }
    public function getKzaikoAttribute(){
        return $this->kzaiko()->first();
    }
    public function getStatusStrAttribute(){
        return Kzaiko::getStatusStr($this->status);
    }
    public function getStatusOldStrAttribute(){
        return Kzaiko::getStatusStr($this->status_old);
    }
    public function User(){
        return $this->hasOne(User::class, 'id', 'user_id')->withDefault();
    }
    public static function make_query($q, $keys){
        if (isset($keys['kzaiko_id']) && 0<$keys['kzaiko_id']) {
            $q->Where('kzaiko_id', $keys['kzaiko_id']);
        }
        if (isset($keys['sort'])) {
            KzaikoRireki::orderByIdx($q, $keys['sort']);
        }else{
            $q->orderBy('id');
        }
    }
    public static function orderByIdx($q, $idx){
        switch ($idx) {
            case 0: // ID 昇順
                $q->orderBy('id');
                break;
            case 1: // ID 降順
                $q->orderBy('id', 'desc');
                break;
            case 2: // 在庫ID 昇順
                $q->orderBy('kzaiko_id')->orderBy('id', 'desc');
                break;
            case 3: // 在庫ID 降順
                $q->orderBy('kzaiko_id', 'desc')->orderBy('id', 'desc');
                break;
        }
    }
    public static function add(int $type, Kzaiko $kzaiko, int $status_old = null, int $zaiko_id_old = null, string $biko = null, int $shukko_id = null, int $zido_id = null){
        $user = Auth::user();
        $r = new KzaikoRireki();
        $r->kzaiko_id = $kzaiko->id;
        $r->type = $type;
        $r->status = $kzaiko->status;
        $r->status_old = $status_old;
        $r->zaiko_id = $kzaiko->zaiko_id;
        $r->zaiko_id_old = $zaiko_id_old;
        $r->biko = $biko;
        $r->user_id = $user->id;
        $r->shukko_id = $shukko_id;
        $r->zido_id = $zido_id;
        $r->save();
    }
}
