<?php

namespace App\Models;

use Illuminate\Support\Facades\Config;
use Illuminate\Database\Eloquent\Model;

class Setting extends Model
{
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = ['name','value'];

    public static function get($name){
        $setting = new self();
        $entry = $setting->where('name',$name)->first();
        if(!$entry){
            return;
        }
        return $entry->value;
    }

    public static function set($name, $value=null)
    {
        self::updateOrInsert(['name'=>$name],['name'=>$name,'value'=>$value]);
        Config::set('name',$value);
        if(Config::get($name) == $value){
            return true;
        }
        return false;
    }
}
