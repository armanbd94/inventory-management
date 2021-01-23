<?php

namespace Modules\Expense\Entities;

use Modules\Account\Entities\Account;
use Modules\Base\Entities\BaseModel;
use Modules\System\Entities\Warehouse;

class Expense extends BaseModel
{
    protected $fillable = [ 'expense_category_id', 'warehouse_id', 'account_id', 'amount', 'note', 'status', 'created_by', 'updated_by'];

    public function category()
    {
        return $this->belongsTo(ExpenseCategory::class,'expense_category_id','id');
    }
    public function warehouse()
    {
        return $this->belongsTo(Warehouse::class,'warehouse_id','id');
    }
    public function account()
    {
        return $this->belongsTo(Account::class,'account_id','id');
    }

    protected $catgeoryID;
    protected $warehouseID;
    protected $accountID;

    public function setCategoryID($catgeoryID)
    {
        $this->catgeoryID = $catgeoryID;
    }
    public function setWarehouseID($warehouseID)
    {
        $this->warehouseID = $warehouseID;
    }
    public function setAccountID($accountID)
    {
        $this->accountID = $accountID;
    }

    private function get_datatable_query()
    {
        if(permission('expense-bulk-delete')){
            $this->column_order = [null,'id','expense_category_id', 'warehouse_id', 'account_id', 'amount', 'note', 'status',null];
        }else{
            $this->column_order = ['id','expense_category_id', 'warehouse_id', 'account_id', 'amount', 'note', 'status',null];
        }

        $query = self::with('category:id,name','warehouse:id,name','account:id,name,account_no');

        /*****************
         * *Search Data **
         ******************/
        if (!empty($this->catgeoryID)) {
            $query->where('expense_category_id', $this->catgeoryID);
        }
        if (!empty($this->warehouseID)) {
            $query->where('warehouse_id', $this->warehouseID);
        }
        if (!empty($this->accountID)) {
            $query->where('account_id', $this->accountID);
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
