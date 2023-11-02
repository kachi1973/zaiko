<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;

use App\Helper;

class Zaiko extends LocalBase{
    protected $guarded = [
        'id',
    ];
    protected $appends = [
        'scaw_flg_str',
        'scaw_flg_str_s',
        'jigyosyo_name',
        'jigyosyo_tana_id',
        'hinmoku',
        'tana_str',
    ];
    protected $hidden = ['original', 'xjigyosyo', 'xhinmoku'];
    private $get_rel_rec_cache = [];
    public function xjigyosyo(){
        return $this->hasOne(Jigyosyo::class, 'id', 'jigyosyo_id');
    }
    public function getJigyosyoNameAttribute(){
        $item = $this->xjigyosyo;
        if(isset($item)){
            return $item->name;
        }else{
            return null;
        }
    }
    public function getJigyosyoTanaIdAttribute(){
        $item = $this->xjigyosyo;
        if(isset($item)){
            return $item->tana_id;
        }else{
            return null;
        }
    }
    public function getStatusStrAttribute(){
        switch($this->status){
        case 0: return "運用中";
        default: return "休止";
        }
    }
    public function xhinmoku(){
        return $this->hasOne(Hinmoku::class, 'id', 'hinmoku_id');
    }
    public function getHinmokuAttribute(){
        return $this->xhinmoku;
    }
    public function fzsu_update(){
        $this->scaw_fzsu = null;
        $q = Hrkzaikov::where('fhincd', $this->hinmoku_id)->where('fzcd', $this->scaw_fzcd)->where('fhokancd', $this->scaw_fhokancd);
        if(0<$q->count()){
            $this->scaw_fzsu = (int)Hrkzaikov::where('fhincd', $this->hinmoku_id)->where('fzcd', $this->scaw_fzcd)->where('fhokancd', $this->scaw_fhokancd)->sum('fzsu');
        }
        $this->save();
    }
    public function fzsu_copy($zaiko_suu_biko){
        if(isset($this->scaw_fzsu)){
            ZaikoRireki::add($this->id, 3, $this->zaiko_suu, $this->scaw_fzsu, $zaiko_suu_biko, null, null, null);
            $this->zaiko_suu = $this->scaw_fzsu;
            $this->save();
        }
    }
    public function getScawFlgStrAttribute(): string{
        switch($this->scaw_flg){
            case 1:
                return 'SCAW品';
            default:
                return '貯蔵品';
        }
    }
    public function getScawFlgStrSAttribute(): string{
        switch ($this->scaw_flg) {
            case 1:
                return 'S';
            default:
                return '貯';
        }
    }
    public function get_rel_rec($shukkos, $shukko_col, $zidos) {
        $name = "1-".serialize($shukkos)."-".$shukko_col."-".serialize($zidos);
        $ret1 = [];
        if(array_key_exists($name, $this->get_rel_rec_cache)){
            $ret1 = $this->get_rel_rec_cache[$name];
        }else{
            $ret1 = DB::table('zaikos')->select(DB::raw("0 as type"), 'shukkos.id', $shukko_col.' as suu')
                ->leftJoin('shukko_items', 'shukko_items.zaiko_id', '=', 'zaikos.id')
                ->leftJoin('shukkos', 'shukkos.id', '=', 'shukko_items.shukko_id')
                ->where('zaikos.id', $this->id)
                ->whereIn('shukkos.status', $shukkos)
                ->orderBy('id')
                ->get()->toArray();
            $this->get_rel_rec_cache[$name] = $ret1;
        }
        $name = "2-".serialize($shukkos)."-".$shukko_col."-".serialize($zidos);
        $ret2 = [];
        if(array_key_exists($name, $this->get_rel_rec_cache)){
            $ret2 = $this->get_rel_rec_cache[$name];
        }else{
            $ret2 = DB::table('zaikos')->select(DB::raw("1 as type"), 'zidos.id', 'suu')
                ->leftJoin('zido_items', 'zido_items.zaiko_id', '=', 'zaikos.id')
                ->leftJoin('zidos', 'zidos.id', '=', 'zido_items.zido_id')
                ->where('zaikos.id', $this->id)
                ->whereIn('zidos.status', $zidos)
                ->orderBy('id')
                ->get()->toArray();
            $this->get_rel_rec_cache[$name] = $ret2;
        }
        return array_merge($ret1, $ret2);
    }
    public function getSinseiSuuAttribute(): ?int{
        return $this->shukko_req_suu + $this->zido_req_suu;
    }
    public function getSinseisAttribute(){
        return $this->get_rel_rec([10], 'req_suu', [10,20]);
    }
    public function getKashiSuuAttribute() : ?int{
        return $this->shukko_out_suu + $this->zido_out_suu;
    }
    public function getKashisAttribute(){
        return $this->get_rel_rec([20,30], 'out_suu', [30]);
    }
    public function getCanMakeKobetuAttribute() : ?int{
        return $this->kobetu_flg || ($this->kashi_suu <= 0);
    }
	public function getFreeZaikoSuuAttribute(): int{
		$a = $this->zaiko_suu;
		$b = $this->getKashiSuuAttribute();
		$c = $this->getSinseiSuuAttribute();
		return $a - $b - $c;
	}
    public static function make_query($keys){
        $q = Zaiko::query()->with(['xhinmoku', 'xjigyosyo']);
        {
            $subq = Kzaiko::select('zaiko_id', DB::raw('count(id) AS kzaiko_suu'))
                ->where('status', '<', 9999)
                ->groupBy('zaiko_id');
            $q->mergeBindings($subq->getQuery());
            $q->leftJoin(DB::raw("({$subq->toSql()}) as kz"), 'kz.zaiko_id', 'id');
        }
        {
            $subq = Shukko::select('zaiko_id', DB::raw('sum(req_suu) AS shukko_req_suu'))
                ->leftJoin(DB::raw("shukko_items as si"), 'si.shukko_id', 'shukkos.id')
                ->whereIn('status', [10])
                ->groupBy('zaiko_id');
            $q->mergeBindings($subq->getQuery());
            $q->leftJoin(DB::raw("({$subq->toSql()}) as srs"), 'srs.zaiko_id', 'id');
        }
        {
            $subq = Shukko::select('zaiko_id', DB::raw('sum(out_suu) AS shukko_out_suu'))
                ->leftJoin(DB::raw("shukko_items as si"), 'si.shukko_id', 'shukkos.id')
                ->whereIn('status', [20, 30])
                ->groupBy('zaiko_id');
            $q->mergeBindings($subq->getQuery());
            $q->leftJoin(DB::raw("({$subq->toSql()}) as sos"), 'sos.zaiko_id', 'id');
        }
        {
            $subq = Zido::select('zaiko_id', DB::raw('sum(suu) AS zido_req_suu'))
                ->leftJoin(DB::raw("zido_items as si"), 'si.zido_id', 'zidos.id')
                ->whereIn('status', [10, 20])
                ->groupBy('zaiko_id');
            $q->mergeBindings($subq->getQuery());
            $q->leftJoin(DB::raw("({$subq->toSql()}) as zrs"), 'zrs.zaiko_id', 'id');
        }
        {
            $subq = Zido::select('zaiko_id', DB::raw('sum(suu) AS zido_out_suu'))
                ->leftJoin(DB::raw("zido_items as si"), 'si.zido_id', 'zidos.id')
                ->whereIn('status', [30])
                ->groupBy('zaiko_id');
            $q->mergeBindings($subq->getQuery());
            $q->leftJoin(DB::raw("({$subq->toSql()}) as zos"), 'zos.zaiko_id', 'id');
        }
        if (isset($keys['sort'])) {
            Zaiko::orderByIdx($q, $keys['sort']);
            if(100 <= $keys['sort']){
                $adddate = function($q, $date1, $date2){
                    if(isset($date1)){
                        $q->where('created_at', '>=', $date1);
                    }
                    if(isset($date2)){
                        $q->where('created_at', '<=', $date2);
                    }
                };
                $subq = ZaikoRireki::select('zaiko_id', DB::raw('sum(used_suu) AS used_suu'))->where('type', 1)->groupBy('zaiko_id');
                $adddate($subq, $keys['date1'], $keys['date2']);
                $q->mergeBindings($subq->getQuery());
                $q->leftJoin(DB::raw("({$subq->toSql()}) as zr1"), 'zr1.zaiko_id', 'id');
                $subq = ZaikoRireki::select('zaiko_id', DB::raw('sum(out_suu) AS out_suu'))->where('type', 1)->groupBy('zaiko_id');
                $adddate($subq, $keys['date1'], $keys['date2']);
                $q->mergeBindings($subq->getQuery());
                $q->leftJoin(DB::raw("({$subq->toSql()}) as zr2"), 'zr2.zaiko_id', 'id');
                $subq = ZaikoRireki::select('zaiko_id', DB::raw('sum(COALESCE(zaiko_suu_new,0) - COALESCE(zaiko_suu_old,0)) AS nyuko_suu'))
                ->whereRaw ('COALESCE(zaiko_suu_old,0) < COALESCE(zaiko_suu_new,0)')
                ->groupBy('zaiko_id');
                $adddate($subq, $keys['date1'], $keys['date2']);
                $q->mergeBindings($subq->getQuery());
                $q->leftJoin(DB::raw("({$subq->toSql()}) as zr3"), 'zr3.zaiko_id', 'id');
            }
        }else{
            $q->orderBy('id');
        }
        if (isset($keys['id'])) {
            $q->Where('id', intval($keys['id']));
        }else{
            if (isset($keys['scaw_flg'])) {
                switch ($keys['scaw_flg']) {
                    case "0":
                    case "1":
                        $q->where('scaw_flg', $keys['scaw_flg']);
                        break;
                }
            }
            if (isset($keys['jigyosyo_id']) && 0 < $keys['jigyosyo_id']) {
                $q->Where('jigyosyo_id', intval($keys['jigyosyo_id']));
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
            if (isset($keys['hinmoku_id'])) {
                $q->Where('hinmoku_id', 'ilike', Helper::get_like_str($keys['hinmoku_id']));
            }
            if (isset($keys['biko'])) {
                $q->Where('biko', 'ilike', Helper::get_like_str($keys['biko']));
            }
            $hinmoku_ids = [];
            $hin_q = Hinmoku::query();
            if (isset($keys['model'])) {
                $hin_q->Where('model', 'ilike', Helper::get_like_str($keys['model']));
            }
            if (isset($keys['name'])) {
                $hin_q->Where('name', 'ilike', Helper::get_like_str($keys['name']));
            }
            if (isset($keys['maker'])) {
                $hin_q->Where('maker', 'ilike', Helper::get_like_str($keys['maker']));
            }
            if (isset($keys['type'])) {
                switch ($keys['type']) {
                    case "0":
                        break;
                    case "1": // 在庫数に差異
                        $q->where('scaw_flg', '1')->whereRaw('(zaiko_suu != scaw_fzsu)');
                        break;
                    case "2": // SCAW側存在せず
                        $q->where('scaw_flg', '1')->whereRaw('scaw_fzsu is null');
                        break;
                    case "3": // 個別在庫未対応
                        $q->where('kobetu_flg', '!=', 1);
                        break;
                    case "4": // 個別在庫数不一致
                        $q->where('kobetu_flg', 1)->whereColumn('zaiko_suu', '!=', 'kzaiko_suu');
                        break;
                }
            }
            if (0 < count($hin_q->getQuery()->wheres)) {
                $hinmoku_ids = $hin_q->distinct()->pluck('id')->toArray();
                if (0 < count($hinmoku_ids)) {
                    $q->whereIn('hinmoku_id', $hinmoku_ids);
                } else {
                    $q->whereNull('hinmoku_id');
                }
            }
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
            case 2: // 在庫区分 昇順
                $q->orderBy('scaw_flg');
                break;
            case 3: // 在庫区分 降順
                $q->orderBy('scaw_flg', 'desc');
                break;
            case 4: // 場所 昇順
                $q->orderBy('basho')->orderBy('basho_no')->orderBy('basho_tana');
                break;
            case 5: // 場所 降順
                $q->orderBy('basho', 'desc')->orderBy('basho_no', 'desc')->orderBy('basho_tana', 'desc');
                break;
            case 6: // 品目コード 昇順
                $q->orderBy('hinmoku_id');
                break;
            case 7: // 品目コード 降順
                $q->orderBy('hinmoku_id', 'desc');
                break;
            case 8: // 型式 昇順
                $q->orderBy('model')->orderBy('model_v')->orderBy('model_kind');
                break;
            case 9: // 型式 降順
                $q->orderBy('model', 'desc')->orderBy('model_v', 'desc')->orderBy('model_kind', 'desc');
                break;
            case 10: // 名称 昇順
                $q->orderBy('name');
                break;
            case 11: // 名称 降順
                $q->orderBy('name', 'desc');
                break;
            case 12: // メーカ 昇順
                $q->orderBy('maker');
                break;
            case 13: // メーカ 降順
                $q->orderBy('maker', 'desc');
                break;
            case 14: // 製造年月 昇順
                $q->orderBy('seizo_date');
                break;
            case 15: // 製造年月 降順
                $q->orderBy('seizo_date', 'desc');
                break;
            case 16: // 適正在庫割れ 降順
                $q->orderByRaw('(COALESCE(zaiko_tekisei,0) - COALESCE(zaiko_suu,0)) DESC NULLS LAST');
                break;
            case 17: // 申請中 降順
                $sub_str = "(COALESCE(shukko_req_suu,0) + COALESCE(zido_req_suu,0))";
                $q->orderByRaw("{$sub_str} DESC NULLS LAST");
                $q->whereRaw("{$sub_str} > 0");
                break;
            case 18: // 貸出中 降順
                $sub_str = "(COALESCE(shukko_out_suu,0) + COALESCE(zido_out_suu,0))";
                $q->orderByRaw("{$sub_str} DESC NULLS LAST");
                $q->whereRaw("{$sub_str} > 0");
                break;
            case 19: // 申請中+貸出中 降順
                $sub_str = "(COALESCE(shukko_req_suu,0) + COALESCE(zido_req_suu,0) + COALESCE(shukko_out_suu,0) + COALESCE(zido_out_suu,0))";
                $q->orderByRaw("{$sub_str} DESC NULLS LAST");
                $q->whereRaw("{$sub_str} > 0");
                break;
            case 100: // 使用実績
                $q->orderByRaw('COALESCE(used_suu,0) DESC NULLS LAST');
                break;
            case 100: // 貸出実績
                $q->orderByRaw('COALESCE(used_suu,0) DESC NULLS LAST');
                break;
            case 101: // 貸出実績
                $q->orderByRaw('COALESCE(out_suu,0) DESC NULLS LAST');
                break;
            case 102: // 入庫実績
                $q->orderByRaw('COALESCE(nyuko_suu,0) DESC NULLS LAST');
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
    public static function get_tanas(){
        $zaikos = Zaiko::select('jigyosyo_id', 'jigyosyos.name', 'jigyosyos.tana_id', 'basho', 'basho_no', 'basho_tana')
        ->leftJoin('jigyosyos', 'jigyosyo_id', '=', 'jigyosyos.id')
        ->groupBy('jigyosyo_id', 'jigyosyos.name', 'jigyosyos.tana_id', 'basho', 'basho_no', 'basho_tana')
        ->orderBy('jigyosyo_id')->orderBy('jigyosyos.name')->orderBy('basho')->orderBy('basho_no')->orderBy('basho_tana')
        ->get();
        $items = [];
        foreach ($zaikos as $zaiko){
            if(!array_key_exists($zaiko->jigyosyo_id, $items)){
                $items[$zaiko->jigyosyo_id] = [
                    'id' => $zaiko->jigyosyo_id,
                    'name' => "{$zaiko->name}({$zaiko->tana_id})",
                    'items' => [],
                ];
            }
            $item1 = &$items[$zaiko->jigyosyo_id];
            if(!array_key_exists($zaiko->basho, $item1['items'])){
                $item1['items'][$zaiko->basho] = [
                    'id' => $zaiko->basho,
                    'name' => $zaiko->basho ? $zaiko->basho : "(空白)",
                    'items' => [],
                ];
            }
            $item2 = &$item1['items'][$zaiko->basho];
            if(!array_key_exists($zaiko->basho_no, $item2['items'])){
                $item2['items'][$zaiko->basho_no] = [
                    'id' => $zaiko->basho_no,
                    'name' => $zaiko->basho_no ? $zaiko->basho_no : "(空白)",
                    'items' => [],
                ];
            }
            $item3 = &$item2['items'][$zaiko->basho_no];
            if(!array_key_exists($zaiko->basho_tana, $item3['items'])){
                $item3['items'][$zaiko->basho_tana] = [
                    'id' => $zaiko->basho_tana,
                    'name' => $zaiko->basho_tana ? $zaiko->basho_tana : "(空白)",
                ];
            }
        }
        //dd($items);
        foreach ($items as &$item1){
            foreach ($item1['items'] as &$item2){
                foreach ($item2['items'] as &$item3){
                    $item3['items'] = array_values($item3['items']);
                }
                $item2['items'] = array_values($item2['items']);
            }
            $item1['items'] = array_values($item1['items']);
        }
        $items = array_values($items);
        return $items;
    }
    public function move_check($to_zaiko_id, $to_jigyosyo_id, $to_basho, $to_basho_no, $to_basho_tana, $suu){
        $err = true;
        $dst = null;
        $mess = "";
		$free_zaiko_suu = $this->getFreeZaikoSuuAttribute();
        if(!(isset($to_zaiko_id) || (isset($to_basho) && isset($to_basho_no) && isset($to_basho_tana))) || !isset($suu)){
            $mess = "未入力の項目があります。";
        }else if($suu < 1){
            $mess = "移動数に有効な値を設定してください。";
        }else if($free_zaiko_suu < $suu){
            $mess = "移動元の在庫数が足りません。";
        }else{
            if(isset($to_zaiko_id)){
                $dst = Zaiko::find($to_zaiko_id);
            }else{
                $dst = Zaiko::where('jigyosyo_id', $to_jigyosyo_id)
                    ->where('scaw_flg', $this->scaw_flg)
                    ->where('basho', $to_basho)
                    ->where('basho_no', $to_basho_no)
                    ->where('basho_tana', $to_basho_tana)
                    ->where('hinmoku_id', $this->hinmoku_id)
                    ->where('model_v', $this->model_v)
                    ->where('model_kind', $this->model_kind)
                    ->first();
            }
            if(isset($dst)){
                if($this->id == $dst->id){
                    $mess = "移動元と移動先が同じです。";
                }else{
                    $mess = "既に存在する棚へ在庫移動しますか？";
                    $err = false;
                }
            }else{
                $dst = $this->replicate();
                $dst->jigyosyo_id = $to_jigyosyo_id;
                $dst->basho = $to_basho;
                $dst->basho_no = $to_basho_no;
                $dst->basho_tana = $to_basho_tana;
                $mess = "棚を新規に作成して在庫移動しますか？";
                $dst->zaiko_suu = 0;
                $err = false;
            }
        }
        return [
            "dst" => $dst,
            "err" => $err,
            "message" => $mess
        ];
    }
    public function move($to_zaiko_id, $to_jigyosyo_id, $to_basho, $to_basho_no, $to_basho_tana, $suu, $zido_id){
        $src = $this;
        $dst = null;
        if(isset($to_zaiko_id)){
            $dst = Zaiko::find($to_zaiko_id);
        }else{
            $dst = Zaiko::where('jigyosyo_id', $to_jigyosyo_id)
                ->where('scaw_flg', $src->scaw_flg)
                ->where('basho', $to_basho)
                ->where('basho_no', $to_basho_no)
                ->where('basho_tana', $to_basho_tana)
                ->where('hinmoku_id', $src->hinmoku_id)
                ->where('model_v', $src->model_v)
                ->where('model_kind', $src->model_kind)
                ->first();
        }
        if(!isset($dst)){
            $dst = $src->replicate();
            $dst->jigyosyo_id = $to_jigyosyo_id;
            $dst->basho = $to_basho;
            $dst->basho_no = $to_basho_no;
            $dst->basho_tana = $to_basho_tana;
            $dst->zaiko_suu = 0;
            $dst->scaw_fzcd = null;
            $dst->scaw_fzsu = null;
            $dst->scaw_fhokancd = null;
        }
        $src_old = $src->zaiko_suu;
        $dst_old = $dst->zaiko_suu;
        $src->zaiko_suu -= $suu;
        $dst->zaiko_suu += $suu;
        if(0<$src->kobetu_flg){
            $dst->kobetu_flg = $src->kobetu_flg;
        }
        $src->save();
        $dst->save();
        ZaikoRireki::add($src->id, 5, $src_old, $src->zaiko_suu, null, null, $dst->id, $zido_id);
        ZaikoRireki::add($dst->id, 6, $dst_old, $dst->zaiko_suu, null, null, $src->id, $zido_id);
        return $dst;
    }
    public static function move_from_scaw($hinmoku_id, $to_zaiko_id, $to_jigyosyo_id, $to_basho, $to_basho_no, $to_basho_tana, $suu, $zido_id){
        $dst = null;
        if(isset($to_zaiko_id)){
            $dst = Zaiko::find($to_zaiko_id);
        }else{
            $dst = Zaiko::firstOrNew([
                'jigyosyo_id' => $to_jigyosyo_id,
                'scaw_flg' => 1,
                'basho' => $to_basho,
                'basho_no' => $to_basho_no,
                'basho_tana' => $to_basho_tana,
                'hinmoku_id' => $hinmoku_id
            ], [
                'zaiko_suu' => 0,
            ]);
        }
        if($dst->exists){
            // 移動先が既存
        }else{
            // 移動先が新規
            $name = "[未登録]";
            $model = null;
            $maker = null;
            $src = Hrkbuhinv::find($hinmoku_id);
            if(isset($src)){
                $name = $src->fhinrmei;
                $model = mb_convert_kana($src->fmekerhincd, "a");
                $maker = $src->fmekermei;
            }
            $hin = Hinmoku::firstOrCreate([
                'id' => $hinmoku_id,
            ],[
                'name' => $name,
                'model' => $model,
                'maker' => $maker,
                'original' => "SCAW"
            ]);
        }
        $dst_old = $dst->zaiko_suu;
        $dst->zaiko_suu += $suu;
        $dst->save();
        ZaikoRireki::add($dst->id, 6, $dst_old, $dst->zaiko_suu, null, null, null, $zido_id);
        return $dst;
    }
    public function add($suu, $hasei_id){
        $old_suu = $this->zaiko_suu;
        $this->zaiko_suu += $suu;
        $this->save();
        ZaikoRireki::add($this->id, 7, $old_suu, $this->zaiko_suu, null, null, null, null, null, null, $hasei_id);
    }
    public function short_name(){
        return "[{$this->id}]{$this->scaw_flg_str}:{$this->jigyosyo_name}:{$this->basho}:{$this->basho_no}:{$this->basho_tana}";
    }
    public function getTanaStrAttribute(){
        return "[{$this->id}]{$this->jigyosyo_name}:{$this->jigyosyo_tana_id}:{$this->basho}:{$this->basho_no}:{$this->basho_tana}";
    }
}
