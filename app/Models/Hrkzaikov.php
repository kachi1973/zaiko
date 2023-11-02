<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

use App\Helper;

class Hrkzaikov extends ScawBase
{
    protected $table = 'hrkzaikov';
    protected $primaryKey = ['fhincd', 'fseiban', 'fzcd', 'fhokancd'];
}
