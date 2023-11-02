<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ScawBase extends Model
{
    protected $connection = 'scaw';
    public $incrementing = false;
}
