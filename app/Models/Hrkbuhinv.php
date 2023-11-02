<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

use App\Helper;

class Hrkbuhinv extends ScawBase
{
    protected $table = 'hrkbuhinv';
    protected $primaryKey = 'fhincd';
    public $incrementing = false;
    protected $appends = [
        'id',
        'name',
        'model',
        'maker',
    ];
    public function getIdAttribute(){
        return $this->fhincd;
    }
    public function getNameAttribute(){
        return $this->fhinrmei;
    }
    public function getModelAttribute(){
        return $this->fmekerhincd;
    }
    public function getMakerAttribute(){
        return $this->fmekermei;
    }
}
