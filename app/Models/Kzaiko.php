<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;

use App\Helper;

class Kzaiko extends LocalBase{
    public const STATUS_ZAIKO = 0;
    public const STATUS_SHUKKO = 1;
    public const STATUS_RETURN = 2;
    public const STATUS_USED = 9999;
    protected $guarded = ['id'];
    protected $appends = ['hinmoku'];
    protected $hidden = ['xhinmoku'];
    private $get_rel_rec_cache = [];
    public function xhinmoku(){
        return $this->hasOne(Hinmoku::class, 'id', 'hinmoku_id');
    }
    public function getHinmokuAttribute(){
        return $this->xhinmoku;
    }
    public function zaiko(){
        return $this->hasOne(Zaiko::class, 'id', 'zaiko_id');
    }
    public static function getStatusStr(?int $status) {
        if(isset($status)){
        switch($status){
                case 0:
                    return "保管中";
                case 1:
                    return "出庫中";
                case 9999:
                    return "使用済";
            }
        }
        return "なし";
    }
    public function getStatusStrAttribute(){
        return Kzaiko::getStatusStr($this->status);
    }
    public static function make_query($keys){
        $q = Kzaiko::query()->select('kzaikos.*', 'zaikos.basho')->with(['xhinmoku', 'zaiko.xjigyosyo']);
        $q->leftJoin('zaikos', 'zaikos.id', 'zaiko_id');
        if (isset($keys['sort'])) {
            Kzaiko::orderByIdx($q, $keys['sort']);
        }else{
            $q->orderBy('kzaikos.id');
        }
        if (isset($keys['id'])) {
            $q->Where('kzaikos.id', intval($keys['id']));
        }else{
            if (isset($keys['zaiko_id'])) {
                $q->Where('zaiko_id', intval($keys['zaiko_id']));
            }
        }
        if (isset($keys['create_date1'])) {
            $q->Where('kzaikos.created_at', '>=', Helper::get_date($keys['create_date1']));
        }
        if (isset($keys['create_date2'])) {
            $q->Where('kzaikos.created_at', '<', Helper::get_date($keys['create_date2'])->addDays(1));
        }
        if (isset($keys['jigyosyo_id']) && 0 < $keys['jigyosyo_id']) {
            $q->Where('jigyosyo_id', $keys['jigyosyo_id']);
        }
        if (isset($keys['basho'])) {
            $q->Where('basho', $keys['basho']);
        }
        if (isset($keys['basho_no'])) {
            $q->Where('basho_no', $keys['basho_no']);
        }
        if (isset($keys['basho_tana'])) {
            $q->Where('basho_tana', $keys['basho_tana']);
        }
        switch ($keys['status_flg']){
            case 1:
                break;
            default:
                $q->Where('kzaikos.status', '<', 9999);
                break;
        }
        return $q;
    }
    public static function orderByIdx($q, $idx){
        switch ($idx) {
            case 0: // ID 昇順
                $q->orderBy('id');
                break;
            case 1: // ID 降順
                $q->orderBy('id', 'desc');
                break;
            case 2: // 作成日 昇順
                $q->orderBy('created_at');
                break;
            case 3: // 作成日 降順
                $q->orderBy('created_at', 'desc');
                break;
            case 4: // 棚 昇順
                $q->orderBy('zaiko_id');
                break;
            case 5: // 棚 降順
                $q->orderBy('zaiko_id', 'desc');
                break;
            }
        switch ($idx) {
            case 0: // ID 昇順
            case 1: // ID 降順
                break;
            default:
                $q->orderBy('id');
                break;
        }
    }
    /*
    public function get_rel_rec($shukkos, $zidos) {
        $ret1 = DB::table('kzaikos')->select(DB::raw("0 as type"), 'shukkos.id')
            ->leftJoin('shukko_item_kzaikos', 'shukko_item_kzaikos.kzaiko_id', '=', 'kzaikos.id')
            ->leftJoin('shukko_items', 'shukko_items.id', '=', 'shukko_item_kzaikos.item_id')
            ->leftJoin('shukkos', 'shukkos.id', '=', 'shukko_items.shukko_id')
            ->where('kzaikos.id', $this->id)
            ->whereIn('shukkos.status', $shukkos)
            ->orderBy('id')
            ->get()->toArray();
        $ret2 = DB::table('kzaikos')->select(DB::raw("1 as type"), 'zidos.id')
            ->leftJoin('zido_item_kzaikos', 'zido_item_kzaikos.kzaiko_id', '=', 'kzaikos.id')
            ->leftJoin('zido_items', 'zido_items.id', '=', 'zido_item_kzaikos.item_id')
            ->leftJoin('zidos', 'zidos.id', '=', 'zido_items.zido_id')
            ->where('kzaikos.id', $this->id)
            ->whereIn('zidos.status', $zidos)
            ->orderBy('id')
            ->get()->toArray();
        return array_merge($ret1, $ret2);
    }
    public function getUsesAttribute(){
        return $this->get_rel_rec([10, 20,30], [10,20,30]);
    }
    */
    public function get_rel_rec($shukkos) {
        $name = "1-".serialize($shukkos);
        $ret1 = [];
        if(array_key_exists($name, $this->get_rel_rec_cache)){
            $ret1 = $this->get_rel_rec_cache[$name];
        }else{
            $ret1 = DB::table('kzaikos')->select(DB::raw("0 as type"), 'shukkos.id')
                ->leftJoin('shukko_item_kzaikos', 'shukko_item_kzaikos.kzaiko_id', '=', 'kzaikos.id')
                ->leftJoin('shukko_items', 'shukko_items.id', '=', 'shukko_item_kzaikos.item_id')
                ->leftJoin('shukkos', 'shukkos.id', '=', 'shukko_items.shukko_id')
                ->where('kzaikos.id', $this->id)
                ->whereIn('shukko_item_kzaikos.status', $shukkos)
                ->orderBy('id')
                ->get()->toArray();
            $this->get_rel_rec_cache[$name] = $ret1;
        }
        $name = "2-".serialize($shukkos);
        $ret2 = [];
        if(array_key_exists($name, $this->get_rel_rec_cache)){
            $ret2 = $this->get_rel_rec_cache[$name];
        }else{
            $ret2 = DB::table('kzaikos')->select(DB::raw("1 as type"), 'zidos.id')
                ->leftJoin('zido_item_kzaikos', 'zido_item_kzaikos.kzaiko_id', '=', 'kzaikos.id')
                ->leftJoin('zido_items', 'zido_items.id', '=', 'zido_item_kzaikos.item_id')
                ->leftJoin('zidos', 'zidos.id', '=', 'zido_items.zido_id')
                ->where('kzaikos.id', $this->id)
                ->whereIn('zido_item_kzaikos.status', $shukkos)
                ->orderBy('id')
                ->get()->toArray();
            $this->get_rel_rec_cache[$name] = $ret2;
        }
        return array_merge($ret1, $ret2);
    }
    public function getShukkosAttribute(){
        return $this->get_rel_rec([Kzaiko::STATUS_SHUKKO]);
    }
    public function getUsedsAttribute(){
        return $this->get_rel_rec([Kzaiko::STATUS_USED]);
    }
    public function getRelationsAttribute(){
        return $this->get_rel_rec([Kzaiko::STATUS_SHUKKO, Kzaiko::STATUS_USED]);
    }
    public function getCanDeleteAttribute(){
        return !(KzaikoRireki::Where('kzaiko_id', $this->id)->Where('type', "<>", KzaikoRirekiType::TYPE_NEW)->exists());
    }
    public function delete_all(){
        if($this->can_delete){
            DB::transaction(function () {
                KzaikoRireki::Where('kzaiko_id', $this->id)->delete();
                $this->delete();
            });
        }
    }
}
