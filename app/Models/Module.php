<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use TypiCMS\NestableTrait;
class Module extends Model
{
    use NestableTrait;
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = ['menu_id', 'type', 'module_name', 'divider_title', 'icon_class', 'url', 'order', 'parent_id', 'target'];
    

    public function menu(){
        return $this->belongsTo(Menu::class);
    }

    public function parent(){
        return $this->belongsTo(Module::class,'parent_id','id');
    }

    public function children(){
        return $this->hasMany(Module::class,'parent_id','id')
                    ->orderBy('order','desc');
    }

    public function submenu(){
        return $this->hasMany(Module::class,'parent_id','id')
        ->orderBy('order','asc')
        ->with('permission:id,module_id,name');
    }

    public function permission(){
        return $this->hasMany(Permission::class);
    }
    public function module_role(){
        return $this->hasMany(ModuleRole::class);
    }




}
