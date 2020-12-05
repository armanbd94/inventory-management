<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Role extends Model
{
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = ['role_name','deletable'];


    public function module_role(){
        return $this->belongsToMany(Module::class)->withTimestamps();
    }
    public function permission_role(){
        return $this->belongsToMany(Permission::class)->withTimestamps();
    }

    public function users()
    {
        return $this->hasMany(User::class);
    }



    
}
