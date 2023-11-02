<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;

use App\Helper;

class ItemKzaikoBase extends LocalBase{
    protected $primaryKey = ['item_id', 'kzaiko_id'];
    public $incrementing = false;
    protected $appends = ['kzaiko'];
    protected $hidden = ['xkzaiko'];
    public function xkzaiko(){
        return $this->hasOne(Kzaiko::class, 'id', 'kzaiko_id');
    }
    public function getKzaikoAttribute(){
        return $this->xkzaiko;
    }
    public static function find2($item_id, $kzaiko_id = null){
        $sikz = self::where('item_id', $item_id);
        if(isset($kzaiko_id)){
            $sikz->where('kzaiko_id', $kzaiko_id);
        }
        return $sikz;
    }
}
