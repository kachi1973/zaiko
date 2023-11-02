<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;

use App\Helper;

class ShukkoItem extends LocalBase{
    protected $appends = [
    ];
    public function zaiko(){
        return $this->hasOne(Zaiko::class, 'id', 'zaiko_id');
    }
    public function getZaikoAttribute(){
        $item = Zaiko::make_query(['id' => $this->zaiko_id])->first();
        $item->append('kashi_suu');
        $item->append('sinsei_suu');
        return $item;
    }
    public function items(){
        return $this->hasMany(ShukkoItemKzaiko::class, 'item_id', 'id')->orderBy('kzaiko_id', 'asc');
    }
    public function getItemsAttribute(){
        return $this->items()->get();
    }
}
