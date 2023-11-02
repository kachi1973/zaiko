<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;

use App\Helper;

class KzaikoRirekiType extends LocalBase{
    public const TYPE_NEW = 1;
    public const TYPE_STS_CHG = 2;
    public const TYPE_MAINT = 3;
    public const TYPE_IDO = 4;
}
