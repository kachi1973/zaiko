<?php

namespace App;

use Illuminate\Support\Facades\Storage;
use Carbon\Carbon;
use Jenssegers\Agent\Agent;

class Helper
{
    public const SHUKKO_PATH = 'shukko';
    public const JOREN_PATH = 'joren';
    public const KONYU_PATH = 'konyu';
    public const HASEI_PATH = 'hasei';
    public const ZIDO_PATH = 'zido';
    public static function ISO8601toDATE($value)
    {
        $datetime = Carbon::parse($value);
        //$datetime->addHour(9);
        return $datetime;
    }
    public static function get_path(string $path, int $id)
    {
        return 'upload/'.$path.'/'.sprintf('%010d', $id);
    }
    public static function get_filelist(string $path, int $id)
    {
        $path2 = Helper::get_path($path, $id);
        $files = Storage::disk('public')->files($path2);
        $urls = [];
        foreach($files as $file){
            $urls[] = [
                'name' => basename($file),
                'path' => Storage::disk('public')->url($file),
            ];
        }
        return $urls;
    }
    public static function YMDHM($value)
    {
        if (isset($value)) {
            return date('Y/m/d H:i', strtotime($value));
        } else {
            return "";
        }
    }
    public static function YMD($value)
    {
        if (isset($value)) {
            return date('Y/m/d', strtotime($value));
        } else {
            return "";
        }
    }
    public static function YMD2($value){
        if (isset($value)) {
            return date('Y-m-d', strtotime($value));
        } else {
            return "";
        }
    }
    public static function YM($value)
    {
        if (isset($value)) {
            return date('Y/m', strtotime($value));
        } else {
            return "";
        }
    }
    public static function get_date($value)
    {
        $value = str_replace("-", "/", $value);
        switch (mb_substr_count($value, '/')) {
            case 1:
                $value = $value . '/1';
                break;
            case 2:
                break;
            default:
                return null;
        }
        try {
            return Carbon::parse($value);
        } catch (\Exception $e) {
            return null;
        }
    }
    public static function get_juchu_nos($value)
    {
        $juchu_nos = [];
        $juchu_no = $value;
        $juchu_no = preg_replace('/-.{1,3}$/', '', $juchu_no);
        if (!strpos($juchu_no, '-')) {
            if (3 < mb_strlen($juchu_no)) {
                $juchu_no = preg_replace("/^.{2}+\K/us", '-', $juchu_no);
                //$juchu_no = substr_replace($juchu_no, '-', 2, 0);
            }
        }
        $juchu_nos[] = $juchu_no;
        $juchu_nos[] = str_replace('-', '', $juchu_no);
        return $juchu_nos;
    }
    public static function str_cut($value, $len = 6)
    {
        if ($len < mb_strlen($value)) {
            $value = mb_substr($value, 0, $len) . "…";
        }
        return $value;
    }
    public static function get_tosyo_path($id)
    {
        return 'upload/' . sprintf('%010d', $id) . '/資料/';
    }
    public static function get_check_str($val)
    {
        return $val ? '有' : '無';
    }
    public static function get_check_str2($val)
    {
        return $val ? '〇' : '×';
    }
    public static function get_like_str($val)
    {
        return '%' . mb_ereg_replace('\s+', '%', $val) . '%';
    }
    public static function get_zenkaku($val)
    {
        return mb_convert_kana($val, 'AK');
    }
    public static function get_hankaku($val)
    {
        return mb_convert_kana($val, 'ak');
    }
    public static function Money($val)
    {
        if (isset($val)) {
            return '\\' . number_format($val) . '-';
        } else {
            return '';
        }
    }
    public static function IsMobile(){
        $agent = new Agent();
        return $agent->isPhone();
    }
    public static function RunCmd($cmd, $json){
        $proc = proc_open($cmd, array(
            0 => array('pipe', 'r'),
            1 => array('pipe', 'w'),
            2 => array('pipe', 'w'),
        ), $pipes);
        stream_set_write_buffer($pipes[0], 0);
        stream_set_blocking($pipes[1], 0);
        stream_set_blocking($pipes[2], 0);
        if(isset($json)){
            $len = strlen($json);
            for ($written = 0; $written < $len;) {
                $written += fwrite($pipes[0], substr($json, $written, 4096));
            }
        }
        fclose($pipes[0]);
        $stdout = $stderr = '';
        while (feof($pipes[1]) === false) {
            $read = array($pipes[1]);
            $write  = NULL;
            $except = NULL;
            $ret = stream_select($read, $write, $except, 1);
            if ($ret === false) {
                // error
                break;
            } else if ($ret === 0) {
                // timeout
                continue;
            } else {
                foreach ($read as $sock) {
                    if ($sock === $pipes[1]) {
                        $stdout .= fread($sock, 4096);
                    }
                }
            }
        }
        fclose($pipes[1]);
        $stderr = stream_get_contents($pipes[2]);
        fclose($pipes[2]);
        $ret = proc_close($proc);
        //file_put_contents(base_path("bin\\" . $filename), $stdout);1
        return [$ret, $stdout, $stderr];
    }
}
