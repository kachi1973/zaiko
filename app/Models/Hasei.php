<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;

use App\Helper;

class Hasei extends LocalBase{
    protected $dates = [
        'status10_date',
        'status20_date',
        'status30_date',
        'status40_date',
        'status50_date',
        'status60_date',
        'status70_date',
    ];
    protected $appends = [
        'status10_user_name',
        'status20_user_name',
        'status30_user_name',
        'status40_user_name',
        'status50_user_name',
        'status60_user_name',
        'status70_user_name',
    ];
    protected $hidden = ['xitems'];
    public function __construct()
    {
        $this->FILE_PATH = Helper::HASEI_PATH;
    }
    public function xitems(){
        return $this->hasMany(HaseiItem::class)->orderBy('id');
    }
    public function getItemsAttribute(){
        return $this->xitems;
    }
    public function getStatusStrAttribute(){
        switch($this->status){
            case 10:
                return '課長承認待ち';
            case 20:
                return '部長承認待ち';
            case 30:
            case 40:
            case 50:
            case 60:
            case 70:
                return '完了';
            case 99:
                return 'キャンセル';
            default:
                return '出庫申請前';
        }
    }
}
