<?php

namespace Modules\HRM\Entities;

use Modules\Base\Entities\BaseModel;
use Modules\HRM\Entities\Department;

class Employee extends BaseModel
{
    protected $fillable = ['name', 'phone', 'address', 
    'city', 'state', 'postal_code', 'country', 'department_id','status', 'created_by', 'updated_by'];

    public function department()
    {
        return $this->belongsTo(Department::class);
    }


    protected $_name;
    protected $_phone;
    protected $_department_id;

    public function setName($name)
    {
        $this->_name = $name;
    }
    public function setPhone($phone)
    {
        $this->_phone = $phone;
    }
    public function setDepartmentID($department_id)
    {
        $this->_department_id = $department_id;
    }

    private function get_datatable_query()
    {
        if(permission('employee-bulk-delete')){
            $this->column_order = [null,'id','image','name','phone','department_id','city',
            'state','postal_code','country','status',null];
        }else{
            $this->column_order = ['id','image','name','phone','department_id','city',
            'state','postal_code','country','status',null];
        }

        $query = self::with('department');

        /*****************
         * *Search Data **
         ******************/
        if (!empty($this->_department_id)) {
            $query->where('department_id', $this->_department_id);
        }
        if (!empty($this->_name)) {
            $query->where('name', 'like', '%' . $this->_name . '%');
        }
        if (!empty($this->_phone)) {
            $query->where('phone', 'like', '%' . $this->_phone . '%');
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
}
