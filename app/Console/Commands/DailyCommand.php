<?php

namespace App\Console\Commands;

use Illuminate\Support\Facades\Log;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Storage;
use DB;

use Carbon\Carbon;

use App\Models\Zaiko;

class DailyCommand extends Command
{
	/**
	 * The name and signature of the console command.
	 *
	 * @var string
	 */
	protected $signature = 'daily';

	/**
	 * The console command description.
	 *
	 * @var string
	 */
	protected $description = 'Command description';

	/**
	 * Create a new command instance.
	 *
	 * @return void
	 */
	public function __construct()
	{
		parent::__construct();
	}

	/**
	 * Execute the console command.
	 *
	 * @return mixed
	 */
	public function handle()
	{
        $path = Storage::disk('local')->path("zaiko.csv");
        //Storage::disk('local')->put('example.txt', $path);
        $zaiko = config('app.zaiko_schema');
        $zs = DB::select(
            "select ".
            "{$zaiko}.zaikos.*, ".
            "jigyosyos.name jigyosyos_name, ".
            "hinmokus.model hinmokus_model, ".
            "hinmokus.name hinmokus_name, ".
            "hinmokus.maker hinmokus_maker ".
            "from {$zaiko}.zaikos ".
            "left join jigyosyos ON (jigyosyos.id = {$zaiko}.zaikos.jigyosyo_id) ".
            "left join {$zaiko}.hinmokus ON ({$zaiko}.hinmokus.id = {$zaiko}.zaikos.hinmoku_id) ".
            "order by id");
        $f = fopen($path, 'w');
        if ($f) {
            foreach ($zs as $z) {
                $fields = [
                    $z->id,
                    $z->scaw_flg ? "SCAW品" : "貯蔵品",
                    $z->jigyosyos_name,
                    $z->basho,
                    $z->basho_no,
                    $z->basho_tana,
                    $z->hinmoku_id,
                    $z->kakaku,
                    $z->hinmokus_model,
                    $z->model_v,
                    $z->model_kind,
                    $z->biko,
                    $z->hinmokus_name,
                    $z->hinmokus_maker,
                    $z->seizo_date,
                    $z->zaiko_suu,
                    $z->tanka,
                    $z->zaiko_tekisei,
                ];
                mb_convert_variables('SJIS', 'UTF-8', $fields);
                fputcsv($f, $fields);
            }
        }
        // ファイルを閉じる
        fclose($f);
        copy($path, config('app.aw_export_path'));
    }
}
