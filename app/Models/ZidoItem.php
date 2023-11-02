<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;

use App\Helper;

class ZidoItem extends LocalBase{
    protected $appends = [
        'hinmoku',
        'zaiko',
        'to_zaiko',
        'from_str',
        'to_str',
    ];
    protected $casts = [
        'suu' => 'integer',
        'tanka' => 'integer',
    ];
    protected $hidden = ['xhinmoku', 'xzaiko', 'xto_zaiko', 'xitems'];
    public function xhinmoku(){
        if(isset($this->zaiko_id)){
            return $this->hasOne(Hinmoku::class, 'id', 'hinmoku_id');
        }else{
            return $this->hasOne(Hrkbuhinv::class, 'fhincd', 'hinmoku_id');
        }
    }
    public function getHinmokuAttribute(){
        return $this->xhinmoku;
    }
    public function xzaiko(){
        return $this->hasOne(Zaiko::class, 'id', 'zaiko_id');
    }
    public function getZaikoAttribute(){
        return $this->xzaiko;
    }
    public function xto_zaiko(){
        return $this->hasOne(Zaiko::class, 'id', 'to_zaiko_id');
    }
    public function getToZaikoAttribute(){
        return $this->xto_zaiko;
    }
    public function getFromStrAttribute(){
        $zaiko = $this->xzaiko;
        if(isset($zaiko)){
            $ji = $zaiko->xjigyosyo;
            if(isset($ji)){
                return "{$ji->name}({$ji->tana_id}):{$zaiko->basho}:{$zaiko->basho_no}:{$zaiko->basho_tana}";
            }
        }
        return null;
    }
    public function tojigyosyo(){
        return $this->hasOne(Jigyosyo::class, 'id', 'to_jigyosyo_id');
    }
    public function getToStrAttribute(){
        if(isset($this->to_zaiko_id)){
            $z = $this->xto_zaiko;
            return "{$z->jigyosyo_name}({$z->jigyosyo_tana_id}):{$z->basho}:{$z->basho_no}:{$z->basho_tana}";
        }else if(isset($this->to_jigyosyo_id)){
            $ji = $this->tojigyosyo;
            if(isset($ji)){
                return "{$ji->name}({$ji->tana_id}):{$this->to_basho}:{$this->to_basho_no}:{$this->to_basho_tana}";
            }
        }
        return null;
    }
    public function xitems(){
        return $this->hasMany(ZidoItemKzaiko::class, 'item_id', 'id')->orderBy('kzaiko_id', 'asc');
    }
    public function getItemsAttribute(){
        return $this->xitems;
    }
}
