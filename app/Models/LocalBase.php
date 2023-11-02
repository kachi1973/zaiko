<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;

use App\Helper;

class LocalBase extends Model
{
    protected $connection = 'pgsql';
    protected $FILE_PATH = '';
    protected $hidden = ['xstatus10user', 'xstatus20user', 'xstatus30user', 'xstatus40user', 'xstatus50user', 'xstatus60user', 'xstatus70user'];
    public function get_name($item){
        if (isset($item)) {
            return $item['name'];
        }
        return null;
    }
    public function getFileUrlsAttribute()
    {
        if(isset($this->id)){
            return Helper::get_filelist($this->FILE_PATH, $this->id);
        }else{
            return [];
        }
    }
    public function xstatus10user(){
        return $this->hasOne(User::class, 'id', 'status10_user_id');
    }
    public function xstatus20user(){
        return $this->hasOne(User::class, 'id', 'status20_user_id');
    }
    public function xstatus30user(){
        return $this->hasOne(User::class, 'id', 'status30_user_id');
    }
    public function xstatus40user(){
        return $this->hasOne(User::class, 'id', 'status40_user_id');
    }
    public function xstatus50user(){
        return $this->hasOne(User::class, 'id', 'status50_user_id');
    }
    public function xstatus60user(){
        return $this->hasOne(User::class, 'id', 'status60_user_id');
    }
    public function xstatus70user(){
        return $this->hasOne(User::class, 'id', 'status70_user_id');
    }
    public function getStatus10UserNameAttribute(){
        if(isset($this->status10_user_id)){
            return $this->get_name($this->xstatus10user);
        }
        return null;
    }
    public function getStatus20UserNameAttribute(){
        if(isset($this->status20_user_id)){
            return $this->get_name($this->xstatus20user);
        }
        return null;
    }
    public function getStatus30UserNameAttribute(){
        if(isset($this->status30_user_id)){
            return $this->get_name($this->xstatus30user);
        }
        return null;
    }
    public function getStatus40UserNameAttribute(){
        if(isset($this->status40_user_id)){
            return $this->get_name($this->xstatus40user);
        }
        return null;
    }
    public function getStatus50UserNameAttribute(){
        if(isset($this->status50_user_id)){
            return $this->get_name($this->xstatus50user);
        }
        return null;
    }
    public function getStatus60UserNameAttribute(){
        if(isset($this->status60_user_id)){
            return $this->get_name($this->xstatus60user);
        }
        return null;
    }
    public function getStatus70UserNameAttribute(){
        if(isset($this->status70_user_id)){
            return $this->get_name($this->xstatus70user);
        }
        return null;
    }
}
