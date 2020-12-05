<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ModuleRole extends Model
{
    protected $table = 'module_role';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = ['module_id','role_id'];

}
