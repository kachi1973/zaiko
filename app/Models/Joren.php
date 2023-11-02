<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Carbon\CarbonImmutable;

use App\Helper;

class Joren extends LocalBase{
    protected $dates = [
        'kouki_start',
        'kouki_end',
        'kojyo_end',
        'status10_date',
        'status20_date',
        'status30_date',
        'status40_date',
        'status50_date',
        'status60_date',
        'status70_date',
    ];
    protected $appends = [
        'no',
        'bumon_name',
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
        $this->FILE_PATH = Helper::JOREN_PATH;
    }
    public function xitems(){
        return $this->hasMany(JorenItem::class)->orderBy('id');
    }
    public function getItemsAttribute(){
        return $this->xitems;
    }
    public function User()
    {
        return $this->hasOne(User::class, 'id', 'user_id');
    }
    public function Bumon()
    {
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
                return '出庫確認待ち';
            case 20:
                return '部品返却待ち';
            case 30:
                return '返却確認待ち';
            case 40:
                return '完了登録待ち';
            case 50:
                return '課長承認待ち';
            case 60:
                return '完了';
            case 70:
                return '完了';
            case 99:
                return 'キャンセル';
            default:
                return '出庫申請前';
        }
    }
    public function getNoAttribute(){
        if(isset($this->status10_date) && isset($this->id)){
            $stime = CarbonImmutable::create($this->status10_date->subMonthsNoOverflow(3)->year, 4, 1);
			$count = Joren::where('status10_date', '>=', $stime)->where('id', '<=', $this->id)->count();
			return "INFO{$stime->year}-".sprintf('%03d', $count);
        }
        return "";
    }
}
