<?php

namespace Modules\Report\Entities;

use Modules\Base\Entities\BaseModel;
use Modules\Supplier\Entities\Supplier;

class SupplierReport extends BaseModel
{
    protected $table="purchases";
    protected $fillable = ['purchase_no', 'supplier_id', 'warehouse_id', 'item', 'total_qty', 
    'total_discount', 'total_tax', 'total_cost', 'order_tax_rate', 'order_tax', 'order_discount',
    'shipping_cost', 'grand_total', 'paid_amount', 'purchase_status', 'payment_status', 
    'document', 'note', 'status', 'created_by', 'updated_by'];

     public function supplier()
     {
         return $this->belongsTo(Supplier::class);
     }

    protected $purchase_no;
    protected $supplier_id;
    protected $from_date;
    protected $to_date;

    public function setPurchaseNo($purchase_no)
    {
        $this->purchase_no = $purchase_no;
    }
    public function setSupplierID($supplier_id)
    {
        $this->supplier_id = $supplier_id;
    }
    public function setFromDate($from_date)
    {
        $this->from_date = $from_date;
    }
    public function setToDate($to_date)
    {
        $this->to_date = $to_date;
    }


    private function get_datatable_query()
    {

        $this->column_order = ['id', 'supplier_id','purchase_no', 'created_at', 'grand_total', 'paid_amount', null];
        

        $query = self::with('supplier');

        /*****************
         * *Search Data **
         ******************/
        if (!empty($this->purchase_no)) {
            $query->where('purchase_no', 'like', '%' . $this->purchase_no . '%');
        }
        if (!empty($this->supplier_id)) {
            $query->where('supplier_id', $this->supplier_id);
        }
        if (!empty($this->from_date)) {
            $query->where('created_at', '>=',$this->from_date.' 00:00:00');
        }
        if (!empty($this->to_date)) {
            $query->where('created_at', '<=',$this->to_date.' 23:59:59');
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
        $query =  self::toBase();
        if (!empty($this->supplier_id)) {
            $query->where('supplier_id', $this->supplier_id);
        }
        return $query->get()->count();
    }
}
