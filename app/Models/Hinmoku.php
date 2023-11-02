<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;

use App\Helper;

class Hinmoku extends LocalBase{
    protected $fillable = [
        'id', 'name', 'model', 'model_kind', 'maker', 'original',
    ];
    protected $hidden = [
        'original',
    ];
    public $incrementing = false;
}
