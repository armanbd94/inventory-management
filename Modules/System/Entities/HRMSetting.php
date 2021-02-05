<?php

namespace Modules\System\Entities;

use Illuminate\Database\Eloquent\Model;

class HRMSetting extends Model
{
    protected $table = 'hrm_settings';
    protected $fillable = ['check_in','check_out','created_by','updated_by'];
    
}
