<?php

namespace Modules\System\Entities;

use Modules\Base\Entities\BaseModel;
use Illuminate\Support\Facades\Cache;

class Unit extends BaseModel
{
    protected $fillable = ['unit_code','unit_name','base_unit','operator',
    'operation_value','status','created_by','updated_by'];

    public function baseUnit()
    {
        return $this->belongsTo(Unit::class,'base_unit','id')
        ->withDefault(['unit_name'=>'N/A']);
    }

    protected $unit_name;

    public function setUnitName($unit_name)
    {
        $this->unit_name = $unit_name;
    }

    private function get_datatable_query()
    {
        if(permission('unit-bulk-delete')){
            $this->column_order = [null,'id','unit_name','code','base_unit','operator','operation_value','status',null];
        }else{
            $this->column_order = ['id','unit_name','code','base_unit','operator','operation_value','status',null];
        }

        $query = self::with('baseUnit');

        /*****************
         * *Search Data **
         ******************/
        if (!empty($this->unit_name)) {
            $query->where('unit_name', 'like', '%' . $this->unit_name . '%');
        }

        if (isset($this->orderValue) && isset($this->dirValue)) {
            $query->orderBy($this->column_order[$this->orderValue], $this->dirValue);
        } else if (isset($this->order)) {
            $query->orderBy(key($this->order), $this->order[key($this->order)]);
        }
        return $query;
    }

    public function getDatatableList()
    {
        $query = $this->get_datatable_query();
        if ($this->lengthVlaue != -1) {
            $query->offset($this->startVlaue)->limit($this->lengthVlaue);
        }
        return $query->get();
    }

    public function count_filtered()
    {
        $query = $this->get_datatable_query();
        return $query->get()->count();
    }

    public function count_all()
    {
        return self::toBase()->get()->count();
    }

    /***********************
     * Cache Data
     *******************/
    private const ALL_UNITS = '_all_units';
    private const ACTIVE_UNITS = '_active_units';

    public static function allUnits()
    {
        return Cache::rememberForever(self::ALL_UNITS, function () {
            return self::toBase()->get();
        });
    }
    public static function activeUnits()
    {
        return Cache::rememberForever(self::ACTIVE_UNITS, function () {
            return self::toBase()->where('status',1)->get();
        });
    }

    public static function flushCache()
    {
        Cache::forget(self::ALL_UNITS);
        Cache::forget(self::ACTIVE_UNITS);
    }

    public static function boot()
    {
        parent::boot();

        static::created(function(){
            self::flushCache();
        });
        static::updated(function(){
            self::flushCache();
        });
        static::deleted(function(){
            self::flushCache();
        });
    }
    
}
