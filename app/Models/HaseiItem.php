<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;

use App\Helper;

class HaseiItem extends LocalBase{
    protected $appends = [
        'zaiko',
    ];
    protected $casts = [
        'suu' => 'integer',
    ];
    protected $hidden = ['xzaiko'];
    public function xzaiko(){
        return $this->hasOne(Zaiko::class, 'id', 'zaiko_id');
    }
    public function getZaikoAttribute()
    {
        return $this->xzaiko;
    }
}
