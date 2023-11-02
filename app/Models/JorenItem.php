<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;

use App\Helper;

class JorenItem extends LocalBase{
    protected $appends = [
        'hinmoku',
        'ido_cnt',
    ];
    protected $casts = [
        'suu' => 'integer',
        'tanka' => 'integer',
    ];
    protected $hidden = ['xhinmoku'];
    protected $ido_suu = null;
    public function getHinmokuAttribute()
    {
        return $this->xhinmoku;
    }
    public function xhinmoku(){
        return $this->hasOne(Hrkbuhinv::class, 'fhincd', 'hinmoku_id');
    }
    public function getIdoCntAttribute(){
        if(!isset($this->ido_suu)){
            $this->ido_suu= ZidoItem::with('xitems.xhinmoku')->leftJoin('zidos', 'zidos.id', '=', 'zido_id')
                ->where('zidos.status', 70)
                ->where('zidos.parent_kind', 1)
                ->where('zidos.parent_id', $this->joren_id)
                ->where('hinmoku_id', $this->hinmoku_id)
                ->sum('suu');
        }
        return $this->ido_suu;
    }
}
