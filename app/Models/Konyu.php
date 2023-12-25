<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;

use App\Helper;

class Konyu extends LocalBase{
    protected $dates = [
        'nouki_date',
        'hachu_date',
        'status10_date',
        'status20_date',
        'status30_date',
        'status40_date',
        'status50_date',
        'status60_date',
        'status70_date',
        'updated_at',
        'created_at',
    ];
    protected $appends = [
        'status_str',
        'bumon_name',
        'status10_user_name',
        'status20_user_name',
        'status30_user_name',
        'status40_user_name',
        'status50_user_name',
        'status60_user_name',
        'status70_user_name',
        'kkamoku'
    ];
    protected $hidden = ['xitems', 'xkkamoku'];
    public function __construct()
    {
        $this->FILE_PATH = Helper::KONYU_PATH;
    }
    public function xitems(){
        return $this->hasMany(KonyuItem::class)->orderBy('id');
    }
    public function getItemsAttribute(){
        return $this->xitems;
    }
    public function getBumonNameAttribute(){
        $user = $this->xstatus10user;
        if(isset($user)){
            return $user->bumon->name;
        }
        return null;
    }
    public function xkkamoku()
    {
        return $this->hasOne(Kkamoku::class, 'id', 'kkamoku_id');
    }
    public function getKkamokuAttribute(){
        return $this->xkkamoku;
    }
    public function getStatusStrAttribute(){
        switch($this->status){
            case 10:
                return '課長承認待';
            case 20:
                return '部長承認待';
            case 30:
                return '受取り待';
            case 40:
                return '倉庫移動待';
            case 50:
            case 60:
            case 70:
                return '完了';
            case 99:
                return 'キャンセル';
            default:
                return '申請待';
        }
    }
}
