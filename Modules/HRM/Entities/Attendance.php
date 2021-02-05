<?php

namespace Modules\HRM\Entities;

use Modules\Base\Entities\BaseModel;

class Attendance extends BaseModel
{
    protected $fillable = ['employee_id','date','check_in','check_out','status','created_by','updated_by'];

    public function employee()
    {
        return $this->belongsTo(Employee::class);
    }

    protected $_employee_id;
    protected $_fromDate;
    protected $_toDate;

    public function setEmployeeID($employee_id)
    {
        $this->_employee_id = $employee_id;
    }
    public function setFromDate($fromDate)
    {
        $this->_fromDate = $fromDate;
    }
    public function setToDate($toDate)
    {
        $this->_toDate = $toDate;
    }

    private function get_datatable_query()
    {
        if(permission('attendance-bulk-delete')){
            $this->column_order = [null,'id','employee_id','check_in','check_out','date','status',null];
        }else{
            $this->column_order = ['id','employee_id','check_in','check_out','date','status',null];
        }

        $query = self::with('employee');

        /*****************
         * *Search Data **
         ******************/
        if (!empty($this->_employee_id)) {
            $query->where('employee_id',  $this->_employee_id );
        }
        if (!empty($this->_fromDate)) {
            $query->where('date',  '>=',$this->_fromDate );
        }
        if (!empty($this->_toDate)) {
            $query->where('date',  '<=',$this->_toDate );
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
