<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use Illuminate\Support\Facades\Auth;
use Illuminate\Testing\TestResponse;

use App\Models\Shukko;
use App\Models\Zaiko;
use App\Models\Kzaiko;
use App\Models\Joren;
use App\Models\Konyu;
use App\Models\Hasei;
use App\Models\Zido;

class ExampleTest extends TestCase
{
    /**
     * A basic test example.
     *
     * @return \Illuminate\Testing\TestResponse
     */
    public function test1()
    {
        $this->Login();
        $this->Shukko();
        $this->Joren();
        $this->Konyu();
        $this->Hasei();
        $this->Zido();
    }
    public function _test2()
    {
        $this->Login();
        foreach (Shukko::orderby('id')->get() as $shukko){
            //if($shukko->id < 800) continue;
            echo "部品出庫:{$shukko->id}", PHP_EOL;
            ob_flush();flush();
            $id = $shukko->id;
            $response = $this->get(route('shukko.edit', ['id' => $id]));
            $response->assertStatus(200);
            $response = $this->postJson2(route('shukko.AjaxGetShukko'), '{"id":'.$id.'}');
            $response->assertStatus(200);
            $response = $this->get(route('shukko.descprint', ['id' => $id]));
            $response->assertStatus(200);
        }
    }
    public function _test3()
    {
        $this->Login();
        foreach (Joren::orderby('id')->get() as $joren){
            //if($shukko->id < 800) continue;
            echo "情報連絡票:{$joren->id}", PHP_EOL;
            ob_flush();flush();
            $id = $joren->id;
            $response = $this->get(route('joren.edit', ['id' => $id]));
            $response->assertStatus(200);
            $response = $this->postJson2(route('joren.AjaxGetJoren'), '{"id":"'.$id.'"}');
            $response->assertStatus(200);
            $response = $this->get(route('joren.descprint', ['id' => $id]));
            $response->assertStatus(200);
        }
    }
    public function _test4()
    {
        $this->Login();
        foreach (Konyu::orderby('id')->get() as $konyu){
            //if($shukko->id < 800) continue;
            echo "部品購入依頼:{$konyu->id}", PHP_EOL;
            ob_flush();flush();
            $id = $konyu->id;
            $response = $this->get(route('konyu.edit', ['id' => $id]));
            $response->assertStatus(200);
            $response = $this->postJson2(route('konyu.AjaxGetKonyu'), '{"id":"'.$id.'"}');
            $response->assertStatus(200);
            $response = $this->get(route('konyu.descprint', ['id' => $id]));
            $response->assertStatus(200);
        }
    }
    public function _test5()
    {
        $this->Login();
        foreach (Hasei::orderby('id')->get() as $hasei){
            //if($shukko->id < 800) continue;
            echo "発生品入庫:{$hasei->id}", PHP_EOL;
            ob_flush();flush();
            $id = $hasei->id;
            $response = $this->get(route('hasei.edit', ['id' => $id]));
            $response->assertStatus(200);
            $response = $this->postJson2(route('hasei.AjaxGetHasei'), '{"id":"'.$id.'"}');
            $response->assertStatus(200);
            $response = $this->get(route('hasei.descprint', ['id' => $id]));
            $response->assertStatus(200);
        }
    }
    public function _test6()
    {
        $this->Login();
        foreach (Zido::orderby('id')->get() as $zido){
            //if($shukko->id < 800) continue;
            echo "倉庫移動伝票:{$zido->id}", PHP_EOL;
            ob_flush();flush();
            $id = $zido->id;
            $response = $this->get(route('zido.edit', ['id' => $id]));
            $response->assertStatus(200);
            $response = $this->postJson2(route('zido.AjaxGetZido'), '{"id":"'.$id.'"}');
            $response->assertStatus(200);
            $response = $this->get(route('zido.descprint', ['id' => $id]));
            $response->assertStatus(200);
        }
    }
    public function Login()
    {
        // 認証失敗
        $this->assertFalse(Auth::check());
        $response = $this->post('/login', ['id' => 'solution@wurzel.co.jp','password' => 'aaa',]);
        $this->assertFalse(Auth::check());
        // 認証成功
        $response = $this->post('/login', ['id' => 'solution@wurzel.co.jp','password' => 'pass',]);
        $this->assertTrue(Auth::check());
        $response->assertRedirect(route('shukko.list'));
    }
    public function postJson2($uri, $content = "", array $headers = [])
    {
        $data = [];
        $files = $this->extractFilesFromDataArray($data);
        $headers = array_merge([
            'CONTENT_LENGTH' => mb_strlen($content, '8bit'),
            'CONTENT_TYPE' => 'application/json',
            'Accept' => 'application/json',
        ], $headers);
        return $this->call(
            'POST',
            $uri,
            [],
            $this->prepareCookiesForJsonRequest(),
            $files,
            $this->transformHeadersToServerVars($headers),
            $content
        );
    }
    public function Shukko()
    {
        // ---部品出庫
        // 在庫[4][1257]の在庫が必要
        $ShukkoCnt = Shukko::count();
        // 一覧
        $response = $this->get(route('shukko.list'));
        $response->assertStatus(200);
        $response = $this->postJson2(route('shukko.AjaxInit'));
        $response->assertStatus(200);
        $response = $this->postJson2(route('shukko.AjaxGetShukkos'), '{"search":{"sort":1,"jigyosyo_id":0,"bumon_id":0,"user_id":0,"seiban":null,"date2_str":null,"stschk00":true,"stschk10":true,"stschk20":true,"stschk30":true,"stschk40":true,"stschk50":true,"stschk60":false,"stschk70":false,"stschk99":false},"pageNum":1}');
        $response->assertStatus(200);
        //$response = $this->postJson2(route('shukko.AjaxGetShukkos'), '{"search":{"sort":1,"jigyosyo_id":1,"bumon_id":"4433","user_id":20477,"seiban":"22-0267","date2_str":"2022-12-22","stschk00":true,"stschk10":true,"stschk20":true,"stschk30":true,"stschk40":true,"stschk50":true,"stschk60":false,"stschk70":false,"stschk99":false},"pageNum":1}');
        $response = $this->postJson2(route('shukko.AjaxGetShukkos'), '{"search":{"sort":1,"id":null,"jigyosyo_id":1,"bumon_id":"4461","user_id":10575,"seiban":"22-03282","date_sel":0,"date1_str":"2023-03-27","date2_str":"2023-03-27","stschk00":true,"stschk10":true,"stschk20":true,"stschk30":true,"stschk40":true,"stschk50":true,"stschk60":true,"stschk70":true,"stschk99":true},"pageNum":1}');
        $response->assertStatus(200);
        $response->assertJson(['total_cnt' => 1]);
        $response = $this->postJson2(route('ajax.AjaxJigyosyos'), '');
        $response->assertStatus(200);
        // 新規
        $response = $this->get(route('shukko.edit', ['id' => 0]));
        $response->assertStatus(200);
        $response = $this->postJson2(route('shukko.AjaxInit'));
        $response->assertStatus(200);
        $response = $this->postJson2(route('ajax.AjaxGetBumons'));
        $response->assertStatus(200);
        $response = $this->postJson2(route('shukko.AjaxGetZaikos'), '{"search":{"sort":0,"scaw_flg":-1,"hinmoku_id":"007C345000","model":"PC-345","name":"基板","maker":"NEW","tana":[1,"MK-3","2-1318FH-1","A2"], "biko":""},"pageNum":1}');
        $response->assertStatus(200);
        $response = $this->postJson2(route('ajax.AjaxGetUserName'), '{"id":"9999"}');
        $response->assertStatus(200);
        $response = $this->postJson2(route('ajax.AjaxGetUsers'), '{"bumon_id":"4400"}');
        $response->assertStatus(200);
        $response = $this->postJson2(route('shukko.AjaxPutShukko'), '{"shukko":{"ing":false,"id":"0","jigyosyo_id":1,"user_id":"9999","shukko_user_id":null,"shukko_date":"2023-02-01","status10_date":null,"status20_date":null,"status30_date":null,"status40_date":null,"status50_date":null,"status60_date":null,"status":0,"seiban":null,"shukko_user_name":null,"items":[{"zaiko":{"id":4,"basho":"MK-3","basho_no":"2-1318FH-1","basho_tana":"A2","hinmoku_id":"007C355000","tanka":89700,"kakaku":null,"shuko":null,"nyuko":null,"model_v":"B","dengen_in":null,"dengen_out":null,"biko":"CNT-A2","seizo_date":null,"zaiko_tekisei":null,"zaiko_tuki":0,"zaiko_new":null,"zaiko_suu":1000,"zaiko_zan":0,"updated_at":"2023-02-01T03:46:21.000000Z","created_at":null,"scaw_flg":0,"jigyosyo_id":1,"scaw_fzcd":"00901","scaw_fzsu":null,"scaw_fhokancd":"1","model_kind":null,"status":0,"kobetu_flg":1,"zaiko_id":null,"kzaiko_suu":"1000","shukko_req_suu":null,"shukko_out_suu":null,"zido_req_suu":null,"zido_out_suu":null,"scaw_flg_str":"貯蔵品","scaw_flg_str_s":"貯","jigyosyo_name":"本社","jigyosyo_tana_id":"00901 1","hinmoku":{"id":"007C355000","name":"基板","model":"PC-355","model_kind":null,"maker":"NEW","updated_at":null,"created_at":null},"tana_str":"[4]本社:00901 1:MK-3:2-1318FH-1:A2","kashi_suu":0,"sinsei_suu":0},"req_suu":2,"biko":null,"shukko_date":"2023-02-01"},{"zaiko":{"id":1257,"basho":"MK-3","basho_no":"2-1318FH-7-1","basho_tana":"A6","hinmoku_id":"0010036428","tanka":261200,"kakaku":"SCAW","shuko":null,"nyuko":null,"model_v":null,"dengen_in":null,"dengen_out":null,"biko":null,"seizo_date":null,"zaiko_tekisei":null,"zaiko_tuki":0,"zaiko_new":null,"zaiko_suu":1000,"zaiko_zan":0,"updated_at":"2023-02-01T03:47:26.000000Z","created_at":null,"scaw_flg":1,"jigyosyo_id":1,"scaw_fzcd":"00901","scaw_fzsu":null,"scaw_fhokancd":"1","model_kind":null,"status":0,"kobetu_flg":1,"zaiko_id":null,"kzaiko_suu":"1000","shukko_req_suu":null,"shukko_out_suu":null,"zido_req_suu":null,"zido_out_suu":null,"scaw_flg_str":"SCAW品","scaw_flg_str_s":"S","jigyosyo_name":"本社","jigyosyo_tana_id":"00901 1","hinmoku":{"id":"0010036428","name":"ﾓﾃﾞﾑ基板","model":"TC-55D","model_kind":null,"maker":"NECﾏｸﾞﾅｽ","updated_at":null,"created_at":null},"tana_str":"[1257]本社:00901 1:MK-3:2-1318FH-7-1:A6","kashi_suu":0,"sinsei_suu":0},"req_suu":3,"biko":null,"shukko_date":"2023-02-01"}],"file_urls":[],"command":0}}');
        $response->assertStatus(200);
        $data = json_decode($response->getContent());
        $id = $data->item->id;
		echo "部品出庫:{$id}", PHP_EOL;
        $this->assertEquals($ShukkoCnt+1, Shukko::count());
        $response = $this->get(route('shukko.descprint', ['id' => $id]));
        $response->assertStatus(200);
		// 申請
        $response = $this->postJson2(route('shukko.AjaxGetShukko'), '{"id":'.$id.'}');
        $response->assertStatus(200);
        $data = json_decode($response->getContent());
        $data->item->command = 1;
        $shukko = [];
        $shukko['shukko'] = $data->item;
        $response = $this->postJson2(route('shukko.AjaxPutShukko2'), json_encode($shukko));
        $response->assertStatus(200);
        $response = $this->get(route('shukko.descprint', ['id' => $id]));
        $response->assertStatus(200);
		// 出庫
        $response = $this->postJson2(route('shukko.AjaxGetShukko'), '{"id":'.$id.'}');
        $response->assertStatus(200);
        $data = json_decode($response->getContent());
        foreach($data->item->items as &$item){
            $kzaikos = Kzaiko::where('zaiko_id', $item->zaiko_id)->where('status', 0)->get();
            $idx=0;
            foreach($kzaikos as $kzr){
                $idx++;
                if($item->req_suu < $idx) break;
                $kz = new Kzaiko();
                $kz->item_id = $item->id;
                $kz->kzaiko_id = $kzr->id;
                $kz->status = 1;
                $item->items[] = $kz;
            }
            $item->out_suu = $item->req_suu;
        }
        $data->item->command = 1;
        $shukko = [];
        $shukko['shukko'] = $data->item;
        $response = $this->postJson2(route('shukko.AjaxPutShukko2'), json_encode($shukko));
        $response->assertStatus(200);
        $response = $this->get(route('shukko.descprint', ['id' => $id]));
        $response->assertStatus(200);
		// 返却
        $response = $this->postJson2(route('shukko.AjaxGetShukko'), '{"id":'.$id.'}');
        $response->assertStatus(200);
        $data = json_decode($response->getContent());
        $data->item->items[0]->used_suu = 1;
        $data->item->items[0]->items[0]->status = 2;
        $data->item->items[0]->items[1]->status = 1;
        $data->item->items[1]->used_suu = 1;
        $data->item->items[1]->items[0]->status = 2;
        $data->item->items[1]->items[1]->status = 2;
        $data->item->items[1]->items[2]->status = 1;
        $data->item->command = 1;
		$data->item->seiban = "12-345678";
        $shukko = [];
        $shukko['shukko'] = $data->item;
        $response = $this->postJson2(route('shukko.AjaxPutShukko2'), json_encode($shukko));
        $response->assertStatus(200);
        $data = json_decode($response->getContent());
        $response = $this->get(route('shukko.descprint', ['id' => $id]));
        $response->assertStatus(200);
		// 返却承認
        $response = $this->postJson2(route('shukko.AjaxGetShukko'), '{"id":'.$id.'}');
        $response->assertStatus(200);
        $data = json_decode($response->getContent());
        $data->item->command = 1;
        $shukko = [];
        $shukko['shukko'] = $data->item;
        $response = $this->postJson2(route('shukko.AjaxPutShukko2'), json_encode($shukko));
        $response->assertStatus(200);
        $response = $this->get(route('shukko.descprint', ['id' => $id]));
        $response->assertStatus(200);
		// 製番
        $response = $this->postJson2(route('shukko.AjaxGetShukko'), '{"id":'.$id.'}');
        $response->assertStatus(200);
        $data = json_decode($response->getContent());
        $data->item->items[1]->seiban = "87-654321";
        $data->item->command = 1;
        $shukko = [];
        $shukko['shukko'] = $data->item;
        $response = $this->postJson2(route('shukko.AjaxPutShukko2'), json_encode($shukko));
        $response->assertStatus(200);
        $response = $this->get(route('shukko.descprint', ['id' => $id]));
        $response->assertStatus(200);
		// 課長承認
        $response = $this->postJson2(route('shukko.AjaxGetShukko'), '{"id":'.$id.'}');
        $response->assertStatus(200);
        $data = json_decode($response->getContent());
        $data->item->command = 1;
        $shukko = [];
        $shukko['shukko'] = $data->item;
        $response = $this->postJson2(route('shukko.AjaxPutShukko2'), json_encode($shukko));
        $response->assertStatus(200);
        $response = $this->get(route('shukko.descprint', ['id' => $id]));
        $response->assertStatus(200);
        // 編集 既存
        $response = $this->get(route('shukko.edit', ['id' => $id]));
        $response->assertStatus(200);
        $response = $this->postJson2(route('shukko.AjaxInit'));
        $response->assertStatus(200);
        $response = $this->postJson2(route('ajax.AjaxGetBumons'));
        $response->assertStatus(200);
        $response = $this->postJson2(route('shukko.AjaxGetShukko'), '{"id":'.$id.'}');
        $response->assertStatus(200);
        $response = $this->postJson2(route('shukko.AjaxGetZaikos'), '{"search":{"sort":0,"scaw_flg":-1,"hinmoku_id":"007C345000","model":"PC-345","name":"基板","maker":"NEW","tana":[1,"MK-3","2-1318FH-1","A2"], "biko":""},"pageNum":1}');
        $response->assertStatus(200);
        $response = $this->postJson2(route('ajax.AjaxGetUserName'), '{"id":"20459"}');
        $response->assertStatus(200);
        $response = $this->postJson2(route('ajax.AjaxGetUsers'), '{"bumon_id":"4433"}');
        $response->assertStatus(200);
        $response = $this->postJson2(route('ajax.AjaxGetKzaiko'), '{"id":1292}');
        $response->assertStatus(200);
        // 詳細
        $response = $this->get(route('shukko.desc', ['id' => $id]));
        $response->assertStatus(200);
        $response = $this->postJson2(route('shukko.AjaxInit'));
        $response->assertStatus(200);
        $response = $this->postJson2(route('shukko.AjaxGetShukko'), '{"id":'.$id.'}');
        $response->assertStatus(200);
        $response->assertJson(['item' => ['id' => $id]]);
        $response = $this->get(route('shukko.descprint', ['id' => $id]));
        $response->assertStatus(200);
    }
    public function Joren()
    {
		// ---情報連絡票
        $JorenCnt = Joren::count();
        // 一覧
        $response = $this->get(route('joren.list'));
        $response->assertStatus(200);
        $response = $this->postJson2(route('joren.AjaxInit'));
        $response->assertStatus(200);
        $response = $this->postJson2(route('joren.AjaxGetJorens'), '{"search":{"sort":"0","bumon_id":"4461","user_id":10671,"seiban":null,"date2_str":null,"stschk00":true,"stschk10":true,"stschk20":true,"stschk30":true,"stschk40":true,"stschk50":true,"stschk60":true,"stschk70":true,"stschk99":true},"pageNum":1}');
        $response->assertStatus(200);
        // 新規
        $response = $this->get(route('joren.edit', ['id' => 0]));
        $response->assertStatus(200);
        $response = $this->postJson2(route('joren.AjaxInit'));
        $response->assertStatus(200);
        $response = $this->postJson2(route('shukko.AjaxGetSZaikos'), '{"search":{"sort":"2","id":"0010000005","model":"ＰＥ５８","name":"コネクタ","maker":"日本ボデーパーツ","perPage":10},"pageNum":1}');
        $response->assertStatus(200);
        $response = $this->postJson2(route('joren.AjaxPutJoren'), '{"item":{"id":0,"bumon_id":"4441","anken_id":"123","kubun":"計画・Ａ","anken":"ソリューション在庫不足による補充","kokyaku":"ABC","status10_date":null,"status10_user_name":null,"status20_date":null,"status20_user_name":null,"status30_date":null,"status30_user_name":null,"status40_date":null,"status40_user_name":null,"kouki_start":"2023-02-01","kouki_end":"2023-02-28","kojyo_end":"2023-03-31","biko":"ソリューション在庫の保有数が無く、障害（クレーム）対応が出来ないため補充する。","siyou":"なし","kiki":"決定","status":0,"editable":true,"items":[{"id":0,"joren_id":null,"hinmoku":{"rn":"1","fhincd":"0010000005","fhinrmei":"コネクタ","fmekerhincd":"ＰＥ５８","fmekermei":"日本ボデーパーツ","id":"0010000005","name":"コネクタ","model":"ＰＥ５８","maker":"日本ボデーパーツ"},"suu":2,"tanka":3000,"hinmoku_id":"0010000005"}],"file_urls":[],"user_id":"9999"}}');
        $response->assertStatus(200);
        $data = json_decode($response->getContent());
        $id = $data->item->id;
		echo "情報連絡票:{$id}", PHP_EOL;
        $this->assertEquals($JorenCnt+1, Joren::count());
        $response = $this->get(route('joren.descprint', ['id' => $id]));
        $response->assertStatus(200);
		// 申請
        $response = $this->get(route('joren.edit', ['id' => $id]));
        $response->assertStatus(200);
        $response = $this->postJson2(route('joren.AjaxInit'));
        $response->assertStatus(200);
        $response = $this->postJson2(route('joren.AjaxGetJoren'), '{"id":"'.$id.'"}');
        $response->assertStatus(200);
        $data = json_decode($response->getContent());
        $data->item->command = 1;
        $data->item->editable = true;
        $response = $this->postJson2(route('joren.AjaxPutJoren'), json_encode($data));
        $response->assertStatus(200);
        $response = $this->get(route('joren.descprint', ['id' => $id]));
        $response->assertStatus(200);
		// 課長承認
        $response = $this->get(route('joren.edit', ['id' => $id]));
        $response->assertStatus(200);
        $response = $this->postJson2(route('joren.AjaxInit'));
        $response->assertStatus(200);
        $response = $this->postJson2(route('joren.AjaxGetJoren'), '{"id":"'.$id.'"}');
        $response->assertStatus(200);
        $data = json_decode($response->getContent());
        $data->item->command = 1;
        $data->item->editable = true;
        $response = $this->postJson2(route('joren.AjaxPutJoren'), json_encode($data));
        $response->assertStatus(200);
        $response = $this->get(route('joren.descprint', ['id' => $id]));
        $response->assertStatus(200);
		// 管理課提出
        $response = $this->get(route('joren.edit', ['id' => $id]));
        $response->assertStatus(200);
        $response = $this->postJson2(route('joren.AjaxInit'));
        $response->assertStatus(200);
        $response = $this->postJson2(route('joren.AjaxGetJoren'), '{"id":"'.$id.'"}');
        $response->assertStatus(200);
        $data = json_decode($response->getContent());
        $data->item->command = 1;
        $data->item->editable = true;
        $response = $this->postJson2(route('joren.AjaxPutJoren'), json_encode($data));
        $response->assertStatus(200);
        $response = $this->get(route('joren.descprint', ['id' => $id]));
        $response->assertStatus(200);
		// 倉庫移動完了
        $response = $this->get(route('joren.edit', ['id' => $id]));
        $response->assertStatus(200);
        $response = $this->postJson2(route('joren.AjaxInit'));
        $response->assertStatus(200);
        $response = $this->postJson2(route('joren.AjaxGetJoren'), '{"id":"'.$id.'"}');
        $response->assertStatus(200);
        $data = json_decode($response->getContent());
        $data->item->command = 1;
        $data->item->editable = true;
        $response = $this->postJson2(route('joren.AjaxPutJoren'), json_encode($data));
        $response->assertStatus(200);
        $response = $this->get(route('joren.descprint', ['id' => $id]));
        $response->assertStatus(200);
        // 編集
        $response = $this->get(route('joren.edit', ['id' => $id]));
        $response->assertStatus(200);
        $response = $this->postJson2(route('joren.AjaxInit'));
        $response->assertStatus(200);
        $response = $this->postJson2(route('joren.AjaxGetJoren'), '{"id":"'.$id.'"}');
        $response->assertStatus(200);
        $response = $this->get(route('ajax.GetHanko', ['name'=>'宇佐美清治', 'date'=>'2021/09/21']));
        $response->assertStatus(200);
    }
    public function Konyu()
    {
        // ---部品購入依頼
        $KonyuCnt = Konyu::count();
        $response = $this->get(route('konyu.list'));
        $response->assertStatus(200);
        $response = $this->postJson2(route('konyu.AjaxInit'));
        $response->assertStatus(200);
        $response = $this->postJson2(route('konyu.AjaxGetKonyus'), '{"search":{"sort":1,"bumon_id":"4400","user_id":9999,"seiban":null,"date2_str":null,"stschk00":true,"stschk10":true,"stschk20":true,"stschk30":true,"stschk40":true,"stschk50":true,"stschk60":true,"stschk70":true,"stschk99":true},"pageNum":1}');
        $response->assertStatus(200);
        // 新規
        $response = $this->get(route('konyu.edit', ['id' => 0]));
        $response->assertStatus(200);
        $response = $this->postJson2(route('shukko.AjaxGetSZaikos'), '{"search":{"sort":0,"id":"0000266900","model":"ＥＫ－２６６９","name":"カメラ制御装置","maker":"ＮＥＷ","perPage":10},"pageNum":1}');
        $response->assertStatus(200);
        $response = $this->postJson2(route('konyu.AjaxPutKonyu'), '{"item":{"id":0,"seiban":"12-345678","mitumori":true,"biko":"ABCDEF","hachu_date":"2023-02-01","irai_no":"123","juchu_no":"456","kkamoku_id":1,"status":0,"status10_user_name":null,"status20_user_name":null,"status30_user_name":null,"status40_user_name":null,"editable":true,"items":[{"id":0,"konyu_id":null,"hinmoku_id":"0000266900","suu":3,"tanka":12000,"hinmoku":{"rn":"1","fhincd":"0000266900","fhinrmei":"カメラ制御装置","fmekerhincd":"ＥＫ－２６６９","fmekermei":"ＮＥＷ","id":"0000266900","name":"カメラ制御装置","model":"ＥＫ－２６６９","maker":"ＮＥＷ"}}],"user_id":"9999","nouki_date":"2023-02-01"}}');
        $response->assertStatus(200);
        $data = json_decode($response->getContent());
        $id = $data->item->id;
		echo "部品購入依頼:{$id}", PHP_EOL;
        $this->assertEquals($KonyuCnt+1, Konyu::count());
		// 申請
        $response = $this->get(route('konyu.edit', ['id' => $id]));
        $response->assertStatus(200);
        $response = $this->postJson2(route('konyu.AjaxInit'));
        $response->assertStatus(200);
        $response = $this->postJson2(route('konyu.AjaxGetKonyu'), '{"id":"'.$id.'"}');
        $response->assertStatus(200);
        $data = json_decode($response->getContent());
        $data->item->command = 1;
        $data->item->editable = true;
        $response = $this->postJson2(route('konyu.AjaxPutKonyu'), json_encode($data));
        $response->assertStatus(200);
        $response = $this->get(route('konyu.descprint', ['id' => $id]));
        $response->assertStatus(200);
		// 課長承認
        $response = $this->get(route('konyu.edit', ['id' => $id]));
        $response->assertStatus(200);
        $response = $this->postJson2(route('konyu.AjaxInit'));
        $response->assertStatus(200);
        $response = $this->postJson2(route('konyu.AjaxGetKonyu'), '{"id":"'.$id.'"}');
        $response->assertStatus(200);
        $data = json_decode($response->getContent());
        $data->item->command = 1;
        $data->item->editable = true;
        $response = $this->postJson2(route('konyu.AjaxPutKonyu'), json_encode($data));
        $response->assertStatus(200);
        $response = $this->get(route('konyu.descprint', ['id' => $id]));
        $response->assertStatus(200);
		// 受取
        $response = $this->get(route('konyu.edit', ['id' => $id]));
        $response->assertStatus(200);
        $response = $this->postJson2(route('konyu.AjaxInit'));
        $response->assertStatus(200);
        $response = $this->postJson2(route('konyu.AjaxGetKonyu'), '{"id":"'.$id.'"}');
        $response->assertStatus(200);
        $data = json_decode($response->getContent());
        $data->item->command = 1;
        $data->item->editable = true;
        $response = $this->postJson2(route('konyu.AjaxPutKonyu'), json_encode($data));
        $response->assertStatus(200);
        $response = $this->get(route('konyu.descprint', ['id' => $id]));
        $response->assertStatus(200);
		// 倉庫移動
        $response = $this->get(route('konyu.edit', ['id' => $id]));
        $response->assertStatus(200);
        $response = $this->postJson2(route('konyu.AjaxInit'));
        $response->assertStatus(200);
        $response = $this->postJson2(route('konyu.AjaxGetKonyu'), '{"id":"'.$id.'"}');
        $response->assertStatus(200);
        $data = json_decode($response->getContent());
        $data->item->command = 1;
        $data->item->editable = true;
        $response = $this->postJson2(route('konyu.AjaxPutKonyu'), json_encode($data));
        $response->assertStatus(200);
        $response = $this->get(route('konyu.descprint', ['id' => $id]));
        $response->assertStatus(200);
    }
    public function Hasei()
    {
        // ---発生品入庫
        $HaseiCnt = Hasei::count();
        $ZaikoCnt = Zaiko::find(1)->zaiko_suu;
        $response = $this->get(route('hasei.list'));
        $response->assertStatus(200);
        $response = $this->postJson2(route('hasei.AjaxInit'));
        $response->assertStatus(200);
        $response = $this->postJson2(route('hasei.AjaxGetHaseis'), '{"search":{"sort":1,"bumon_id":"4400","user_id":9999,"seiban":null,"date2_str":null,"stschk00":true,"stschk10":true,"stschk20":true,"stschk30":true,"stschk40":true,"stschk50":true,"stschk60":true,"stschk70":true,"stschk99":true},"pageNum":1}');
        $response->assertStatus(200);
        // 新規
        $response = $this->get(route('hasei.edit', ['id' => 0]));
        $response->assertStatus(200);
        $response = $this->postJson2(route('hasei.AjaxInit'));
        $response->assertStatus(200);
        $response = $this->postJson2(route('shukko.AjaxGetZaikos'), '{"search":{"sort":0,"scaw_flg":-1,"hinmoku_id":null,"model":null,"name":null,"maker":null,"perPage":10,"tana":[], "biko":""},"pageNum":1}');
        $response->assertStatus(200);
        $response = $this->postJson2(route('hasei.AjaxPutHasei'), '{"item":{"id":0,"status10_date":null,"status10_user_name":null,"status20_date":null,"status20_user_name":null,"status30_date":null,"status30_user_name":null,"status40_date":null,"status40_user_name":null,"status50_date":null,"status50_user_name":null,"status60_date":null,"status60_user_name":null,"status70_date":null,"status70_user_name":null,"biko":"PHPUnit","status":0,"editable":true,"items":[{"id":0,"hasei_id":null,"hinmoku":{"name":null,"model":null},"suu":1,"tanka":null,"zaiko_id":1,"zaiko":{"id":1,"basho":"MK-3","basho_no":"2-1318FH-1","basho_tana":"A2","hinmoku_id":"007C345000","tanka":null,"kakaku":null,"shuko":0,"nyuko":0,"model_v":null,"dengen_in":null,"dengen_out":null,"biko":null,"seizo_date":null,"zaiko_tekisei":null,"zaiko_tuki":0,"zaiko_new":null,"zaiko_suu":0,"zaiko_zan":0,"updated_at":null,"created_at":null,"scaw_flg":0,"jigyosyo_id":1,"scaw_fzcd":"00901","scaw_fzsu":null,"scaw_fhokancd":"1","model_kind":null,"status":0,"kobetu_flg":0,"zaiko_id":null,"kzaiko_suu":null,"shukko_req_suu":null,"shukko_out_suu":null,"zido_req_suu":null,"zido_out_suu":null,"scaw_flg_str":"貯蔵品","scaw_flg_str_s":"貯","jigyosyo_name":"本社","jigyosyo_tana_id":"00901 1","hinmoku":{"id":"007C345000","name":"基板","model":"PC-345","model_kind":null,"maker":"NEW","updated_at":null,"created_at":null},"tana_str":"[1]本社:00901 1:MK-3:2-1318FH-1:A2","kashi_suu":0,"sinsei_suu":0,"tana":"本社(00901 1):MK-3:2-1318FH-1:A2"}}],"file_urls":[],"user_id":"9999"}}');
        $response->assertStatus(200);
        $data = json_decode($response->getContent());
        $id = $data->item->id;
		echo "発生品入庫:{$id}", PHP_EOL;
        $this->assertEquals($HaseiCnt+1, Hasei::count());
        $response = $this->postJson2(route('ajax.AjaxGetUsers'), '{"bumon_id":"4400"}');
        $response->assertStatus(200);
		// 申請
        $response = $this->get(route('hasei.edit', ['id' => $id]));
        $response->assertStatus(200);
        $response = $this->postJson2(route('hasei.AjaxInit'));
        $response->assertStatus(200);
        $response = $this->postJson2(route('hasei.AjaxGetHasei'), '{"id":"'.$id.'"}');
        $response->assertStatus(200);
        $data = json_decode($response->getContent());
        $data->item->command = 1;
        $data->item->editable = true;
        $response = $this->postJson2(route('hasei.AjaxPutHasei'), json_encode($data));
        $response->assertStatus(200);
        $response = $this->get(route('hasei.descprint', ['id' => $id]));
        $response->assertStatus(200);
		// 課長承認
        $response = $this->get(route('hasei.edit', ['id' => $id]));
        $response->assertStatus(200);
        $response = $this->postJson2(route('hasei.AjaxInit'));
        $response->assertStatus(200);
        $response = $this->postJson2(route('hasei.AjaxGetHasei'), '{"id":"'.$id.'"}');
        $response->assertStatus(200);
        $data = json_decode($response->getContent());
        $data->item->command = 1;
        $data->item->editable = true;
        $response = $this->postJson2(route('hasei.AjaxPutHasei'), json_encode($data));
        $response->assertStatus(200);
        $response = $this->get(route('hasei.descprint', ['id' => $id]));
        $response->assertStatus(200);
        $this->assertEquals($ZaikoCnt+1, Zaiko::find(1)->zaiko_suu);
    }
    public function Zido()
    {
        // ---倉庫移動伝票
        $ZidoCnt = Zido::count();
        $ZaikoCnt = Zaiko::find(1256)->zaiko_suu;
        $response = $this->get(route('zido.list'));
        $response->assertStatus(200);
        $response = $this->postJson2(route('zido.AjaxInit'));
        $response->assertStatus(200);
        $response = $this->postJson2(route('ajax.AjaxGetUsers'), '{"bumon_id":"4400"}');
        $response->assertStatus(200);
        $response = $this->postJson2(route('zido.AjaxGetZidos'), '{"search":{"sort":1,"bumon_id":"4400","user_id":9999,"seiban":null,"date2_str":null,"stschk00":true,"stschk10":true,"stschk20":true,"stschk30":true,"stschk40":true,"stschk50":false,"stschk60":false,"stschk70":false,"stschk99":false,"parent_kind":0,"parent_id":null},"pageNum":1}');
        $response->assertStatus(200);
        // 新規 SCAW品->棚指定
        $response = $this->get(route('zido.edit', ['id' => 0]));
        $response->assertStatus(200);
        $response = $this->postJson2(route('zido.AjaxInit'));
        $response->assertStatus(200);
        $response = $this->postJson2(route('zido.AjaxPutZido'), '{"item":{"id":"0","inout_date":"2023-02-05","from_type":0,"from_jigyosyo_id":null,"from_basho":null,"from_basho_no":null,"from_basho_tana":null,"to_type":99,"to_jigyosyo_id":null,"to_basho":null,"to_basho_no":null,"to_basho_tana":null,"to_zaiko_id":null,"to_zaiko_str":null,"sizai":"材料","juchu_id":"12-345678","bumon_id":"4461","in_riyuu":"新規(情報連絡票完成分入庫)","out_riyuu":"自家消費","kubun":"SCAW品","tana":"00901 1","biko":"PHPUnit SCAW品->棚指定","status":0,"status10_date":null,"status10_user_name":null,"status20_date":null,"status20_user_name":null,"status30_date":null,"status30_user_name":null,"status40_date":null,"status40_user_name":null,"status50_date":null,"status50_user_name":null,"status60_date":null,"status60_user_name":null,"status70_date":null,"status70_user_name":null,"parent_kind":0,"parent_id":null,"editable":true,"items":[{"id":0,"zido_id":null,"hinmoku_id":"0010044418","seiban":"87-654321","suu":2,"zaiko_id":null,"name":null,"model":null,"from_tana_str":null,"to_type":99,"to_jigyosyo_id":1,"to_basho":"MK-3","to_basho_no":"2-1318FH-7-1","to_basho_tana":"A6","to_zaiko_id":null,"to_zaiko_str":null,"kubun":"SCAW品","to_tana":[1,"MK-3","2-1318FH-7-1","A6"],"hinmoku":{"rn":"1","fhincd":"0010044418","fhinrmei":"モデム","fmekerhincd":"ＴＣ－３８ＬⅡ","fmekermei":"東洋通信機(株)","id":"0010044418","name":"モデム","model":"ＴＣ－３８ＬⅡ","maker":"東洋通信機(株)"}}],"user_id":"9999"}}');
        $response->assertStatus(200);
        $data = json_decode($response->getContent());
        $id = $data->id;
        $response = $this->get(route('zido.descprint', ['id' => $id]));
        $response->assertStatus(200);
		echo "倉庫移動伝票:{$id}", PHP_EOL;
        $this->assertEquals($ZidoCnt+1, Zido::count());
		// 申請
        $response = $this->get(route('zido.edit', ['id' => $id]));
        $response->assertStatus(200);
        $response = $this->postJson2(route('zido.AjaxInit'));
        $response->assertStatus(200);
        $response = $this->postJson2(route('zido.AjaxGetZido'), '{"id":"'.$id.'"}');
        $response->assertStatus(200);
        $data = json_decode($response->getContent());
        $data->item->command = 1;
        $data->item->editable = true;
        $response = $this->postJson2(route('zido.AjaxPutZido'), json_encode($data));
        $response->assertStatus(200);
        $response = $this->get(route('zido.descprint', ['id' => $id]));
        $response->assertStatus(200);
		// 課長承認
        $response = $this->get(route('zido.edit', ['id' => $id]));
        $response->assertStatus(200);
        $response = $this->postJson2(route('zido.AjaxInit'));
        $response->assertStatus(200);
        $response = $this->postJson2(route('zido.AjaxGetZido'), '{"id":"'.$id.'"}');
        $response->assertStatus(200);
        $data = json_decode($response->getContent());
        $data->item->command = 1;
        $data->item->editable = true;
        $response = $this->postJson2(route('zido.AjaxPutZido'), json_encode($data));
        $response->assertStatus(200);
        $response = $this->get(route('zido.descprint', ['id' => $id]));
        $response->assertStatus(200);
		// 出庫
        $response = $this->get(route('zido.edit', ['id' => $id]));
        $response->assertStatus(200);
        $response = $this->postJson2(route('zido.AjaxInit'));
        $response->assertStatus(200);
        $response = $this->postJson2(route('zido.AjaxGetZido'), '{"id":"'.$id.'"}');
        $response->assertStatus(200);
        $data = json_decode($response->getContent());
        $data->item->command = 1;
        $data->item->editable = true;
        $response = $this->postJson2(route('zido.AjaxPutZido'), json_encode($data));
        $response->assertStatus(200);
        $response = $this->get(route('zido.descprint', ['id' => $id]));
        $response->assertStatus(200);
		// 入庫
        $response = $this->get(route('zido.edit', ['id' => $id]));
        $response->assertStatus(200);
        $response = $this->postJson2(route('zido.AjaxInit'));
        $response->assertStatus(200);
        $response = $this->postJson2(route('zido.AjaxGetZido'), '{"id":"'.$id.'"}');
        $response->assertStatus(200);
        $data = json_decode($response->getContent());
        $data->item->command = 1;
        $data->item->editable = true;
        $response = $this->postJson2(route('zido.AjaxPutZido'), json_encode($data));
        $response->assertStatus(200);
        $response = $this->get(route('zido.descprint', ['id' => $id]));
        $response->assertStatus(200);
        $this->assertEquals($ZaikoCnt+2, Zaiko::find(1256)->zaiko_suu); // 2個増える
    }
}
