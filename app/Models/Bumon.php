<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

use App\Helper;

class Bumon extends LocalBase
{
    public $incrementing = false;
    public function jigyosyo()
    {
        return $this->belongsTo(Jigyosyo::class)->withDefault();
    }
}
