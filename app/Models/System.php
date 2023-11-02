<?php

namespace App\Models;

use App\Helper;
use Illuminate\Database\Eloquent\Model;

class System extends LocalBase
{
    public static function add_login_cnt()
    {
        $sys = System::first();
        $sys->increment('z_login_cnt');
        if(Helper::IsMobile()){
            $sys->increment('z_login_cnt_m');
        }
    }
    public static function add_search_cnt()
    {
        $sys = System::first();
        $sys->increment('z_search_cnt');
        if(Helper::IsMobile()) $sys->increment('z_search_cnt_m');
    }
    public static function add_show_cnt()
    {
        $sys = System::first();
        $sys->increment('z_show_cnt');
        if(Helper::IsMobile()) $sys->increment('z_show_cnt_m');
    }
}
