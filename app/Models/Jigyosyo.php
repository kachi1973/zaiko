<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;

use App\Helper;

class Jigyosyo extends LocalBase
{
    public static function all2()
    {
        $items = Jigyosyo::whereRaw('id in (select jigyosyo_id from bumons where enable1)')->orderBy('order')->get();
        return $items;
    }
    public static function all3()
    {
        $items = Jigyosyo::whereRaw('id in (select jigyosyo_id from bumons where enable1)')->orderBy('order')->get();
        foreach ($items as $item) {
            $item->setAppends(['shukko_cnt', 'shukko00_cnt', 'shukko10_cnt', 'shukko20_cnt', 'shukko30_cnt', 'shukko40_cnt', 'shukko50_cnt', 'shukko60_cnt', 'shukko70_cnt', 'shukko99_cnt', 'shukko_date1_cnt', 'shukko_date2_cnt']);
        }
    return $items;
    }
    public function getShukko00CntAttribute(){
        return Shukko::where('jigyosyo_id', $this->id)->where('status', 0)->count();
    }
    public function getShukko10CntAttribute(){
        return Shukko::where('jigyosyo_id', $this->id)->where('status', 10)->count();
    }
    public function getShukko20CntAttribute(){
        return Shukko::where('jigyosyo_id', $this->id)->where('status', 20)->count();
    }
    public function getShukko30CntAttribute(){
        return Shukko::where('jigyosyo_id', $this->id)->where('status', 30)->count();
    }
    public function getShukko40CntAttribute(){
        return Shukko::where('jigyosyo_id', $this->id)->where('status', 40)->count();
    }
    public function getShukko50CntAttribute(){
        return Shukko::where('jigyosyo_id', $this->id)->where('status', 50)->count();
    }
    public function getShukko60CntAttribute(){
        return Shukko::where('jigyosyo_id', $this->id)->where('status', 60)->count();
    }
    public function getShukko70CntAttribute(){
        return Shukko::where('jigyosyo_id', $this->id)->where('status', 70)->count();
    }
    public function getShukko99CntAttribute(){
        return Shukko::where('jigyosyo_id', $this->id)->where('status', 99)->count();
    }
    public function getShukkoCntAttribute(){
        return Shukko::where('jigyosyo_id', $this->id)->count();
    }
    public function getShukkoDate1CntAttribute(){
        $stime = Carbon::now()->setTime(0, 0, 0);
        $etime = $stime->copy()->addDay(1);
        return Shukko
            ::where('jigyosyo_id', $this->id)
            ->where('status', 10)
            ->where('shukko_date', '>=', $stime)
            ->where('shukko_date', '<', $etime)
            ->count();
    }
    public function getShukkoDate2CntAttribute(){
        $etime = Carbon::now()->setTime(0, 0, 0);
        return Shukko
            ::where('jigyosyo_id', $this->id)
            ->where('status', 10)
            ->where('shukko_date', '<', $etime)
            ->count();
    }
    public function getZaikowareCntAttribute(){
        return Zaiko::whereColumn('zaiko_tekisei', '>', 'zaiko_suu')->count();
    }
}
