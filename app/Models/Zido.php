<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;

use App\Helper;

class Zido extends LocalBase{
    protected $dates = [
        'inout_date',
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
        'bumon_name',
        'status10_user_name',
        'status20_user_name',
        'status30_user_name',
        'status40_user_name',
        'status50_user_name',
        'status60_user_name',
        'status70_user_name',
        'parent_str',
        'parent_url',
        'from_str',
        'to_str',
    ];
    public function __construct()
    {
        $this->FILE_PATH = Helper::ZIDO_PATH;
    }
    public function xitems(){
        return $this->hasMany(ZidoItem::class)->orderBy('id');
    }
    public function getItemsAttribute(){
        $items = $this->xitems;
        foreach ($items as $item) {
            $item->append('items');
        }
        return $items;
    }
    public function User(){
        return $this->hasOne(User::class, 'id', 'user_id');
    }
    public function bumon(){
        return $this->hasOne(Bumon::class, 'id', 'bumon_id');
    }
    public function getBumonNameAttribute(){
        if(isset($this->bumon_id)){
            return $this->get_name($this->bumon);
        }
        return null;
    }
    public function getStatusStrAttribute(){
        switch($this->status){
            case 10:
                return '課長承認待';
            case 20:
                return '出庫待';
            case 30:
                return '入庫待';
            case 40:
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
    public function getParentStrAttribute(){
        if(isset($this->parent_kind) && isset($this->parent_id)){
            switch($this->parent_kind){
                case 1:
                    return "情報連絡票[{$this->parent_id}]";
                case 2:
                    return "部品購入依頼[{$this->parent_id}]";
                }
        }
        return null;
    }
    public function getParentUrlAttribute(){
        if(isset($this->parent_kind) && isset($this->parent_id)){
            switch($this->parent_kind){
                case 1:
                    return route('joren.edit', ['id' => $this->parent_id]);
                case 2:
                    return route('konyu.edit', ['id' => $this->parent_id]);
            }
        }
        return null;
    }
    public function getFromStrAttribute(){
        switch($this->from_type){
            case 0:
                return '美和(第１工場 １階)';
            case 1:
                return '美和(第１工場 ２階)';
            case 2:
                return '美和(00901 1)';
            case 3:
                return '東京(02103 A-S1-4)';
            case 4:
                return '大阪(03101 3-3-1)';
            case 5:
                return '広島(04101 5-1)';
            case 6:
                return '福岡(05101 C-1)';
            case 7:
                return '仙台(06101 A-1)';
            case 8:
                return '札幌(07101 F)';
            case 9:
                return '四国(08101 2-1)';
            case 10:
                return '新潟(09101 A-4)';
            case 11:
                return '南九州(10101 MB-1)';
            case 99:
                return "ソリューション棚";
                /*
                $from_jigyosyo_name = Jigyosyo::find($this->from_jigyosyo_id)->name;
                return "ソリューション棚 [ {$from_jigyosyo_name} - {$this->from_basho} - {$this->from_basho_no} - {$this->from_basho_tana} ]";
                */
            default:
                return "";
        }
    }
    public function getToStrAttribute(){
        switch($this->to_type){
            case 0:
                return '美和(00901 1)';
            case 1:
                return '東京(02103 A-S1-4)';
            case 2:
                return '大阪(03101 3-3-1)';
            case 3:
                return '広島(04101 5-1)';
            case 4:
                return '福岡(05101 C-1)';
            case 5:
                return '仙台(06101 A-1)';
            case 6:
                return '札幌(07101 F)';
            case 7:
                return '四国(08101 2-1)';
            case 8:
                return '新潟(09101 A-4)';
            case 9:
                return '南九州(10101 MB-1)';
            case 99:
                return "ソリューション棚";
                /*
                $to_jigyosyo_name = Jigyosyo::find($this->to_jigyosyo_id)->name;
                return "ソリューション棚 [ {$to_jigyosyo_name} - {$this->to_basho} - {$this->to_basho_no} - {$this->to_basho_tana} ]";
                */
            default:
                return $this->tana;
        }
    }
}
