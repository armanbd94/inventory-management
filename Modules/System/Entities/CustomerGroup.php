<?php

namespace Modules\System\Entities;

use Illuminate\Support\Facades\Cache;
use Modules\Base\Entities\BaseModel;

class CustomerGroup extends BaseModel
{
    protected $fillable = ['group_name','percentage','status','created_by','updated_by'];
    
    protected $group_name;

    public function setGroupName($group_name)
    {
        $this->group_name = $group_name;
    }

    private function get_datatable_query()
    {
        if(permission('customer-group-bulk-delete')){
            $this->column_order = [null,'id','group_name','percentage','status',null];
        }else{
            $this->column_order = ['id','group_name','percentage','status',null];
        }

        $query = self::toBase();

        /*****************
         * *Search Data **
         ******************/
        if (!empty($this->group_name)) {
            $query->where('group_name', 'like', '%' . $this->group_name . '%');
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
    private const ALL_CUSTOMER_GROUPS = '_all_customer_groups';
    private const ACTIVE_CUSTOMER_GROUPS = '_active_customer_groups';

    public static function allCustomerGroups()
    {
        return Cache::rememberForever(self::ALL_CUSTOMER_GROUPS, function () {
            return self::toBase()->get();
        });
    }
    public static function activeCustomerGroups()
    {
        return Cache::rememberForever(self::ACTIVE_CUSTOMER_GROUPS, function () {
            return self::toBase()->where('status',1)->get();
        });
    }

    public static function flushCache()
    {
        Cache::forget(self::ALL_CUSTOMER_GROUPS);
        Cache::forget(self::ACTIVE_CUSTOMER_GROUPS);
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
