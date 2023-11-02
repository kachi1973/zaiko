<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;

use App\Helper;

class Shukko extends LocalBase{
    protected $dates = [
        'shukko_date',
        'status10_date',
        'status20_date',
        'status30_date',
        'status40_date',
        'status50_date',
        'status60_date',
        'status70_date',
    ];
    protected $appends = [
        'status_str',
        'user_name',
        'bumon_name',
        'jigyosyo_name',
        'status10_user_name',
        'status20_user_name',
        'status30_user_name',
        'status40_user_name',
        'status50_user_name',
        'status60_user_name',
        'status70_user_name',
    ];
    protected $hidden = ['xitems', 'xjigyosyo'];
    public function __construct()
    {
        $this->FILE_PATH = Helper::SHUKKO_PATH;
    }
    public function xjigyosyo()
    {
        return $this->hasOne(Jigyosyo::class, 'id', 'jigyosyo_id');
    }
    public function getJigyosyoNameAttribute()
    {
        $item = $this->xjigyosyo;
        if (isset($item)) {
            return $item->name;
        } else {
            return null;
        }
    }
    public function xitems(){
        return $this->hasMany(ShukkoItem::class)->orderBy('id');
    }
    public function getItemsAttribute(){
        $items = $this->xitems;
        foreach ($items as $item) {
            $item->append('zaiko');
            $item->append('items');
        }
        return $items;
    }
    public function User()
    {
        return $this->hasOne(User::class, 'id', 'user_id');
    }
    public function getBumonNameAttribute(){
        if(isset($this->user_id)){
            $item = $this->user;
            if (isset($item)) {
                return $this->get_name($item->bumon);
            }
        }
        return null;
    }
    public function getUserNameAttribute(){
        if(isset($this->user_id)){
            return $this->get_name($this->user);
        }
        return null;
    }
    public function getStatusStrAttribute(){
        switch($this->status){
            case 10:
                return '出庫待';
            case 20:
                return '返却待';
            case 30:
                return '返却承認待';
            case 40:
                return '製番待';
            case 50:
                return '課長承認待';
            case 60:
                return '完了待';
            case 70:
                return '完了';
            case 99:
                return 'キャンセル';
            default:
                return '出庫申請待';
        }
    }
}
