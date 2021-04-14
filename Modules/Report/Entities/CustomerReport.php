<?php

namespace Modules\Report\Entities;

use Modules\Base\Entities\BaseModel;
use Modules\Customer\Entities\Customer;

class CustomerReport extends BaseModel
{
    protected $table = "sales";
    protected $fillable = ['sale_no', 'customer_id', 'warehouse_id', 'item', 'total_qty', 
    'total_discount', 'total_tax', 'total_price', 'order_tax_rate', 'order_tax', 'order_discount',
    'shipping_cost', 'grand_total', 'paid_amount', 'sale_status', 'payment_status', 
    'document', 'note', 'status', 'created_by', 'updated_by'];

     public function customer()
     {
         return $this->belongsTo(Customer::class);
     }


    protected $sale_no;
    protected $customer_id;
    protected $from_date;
    protected $to_date;

    public function setSaleNo($sale_no)
    {
        $this->sale_no = $sale_no;
    }
    public function setCustomerID($customer_id)
    {
        $this->customer_id = $customer_id;
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

        $this->column_order = ['id','customer_id', 'sale_no', 'created_at', 'grand_total', 'paid_amount', null];
        
        $query = self::with('customer');

        /*****************
         * *Search Data **
         ******************/
        if (!empty($this->sale_no)) {
            $query->where('sale_no', 'like', '%' . $this->sale_no . '%');
        }
        if (!empty($this->customer_id)) {
            $query->where('customer_id', $this->customer_id);
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
        $query = self::toBase();
        if (!empty($this->customer_id)) {
            $query->where('customer_id', $this->customer_id);
        }
        return $query->get()->count();
    }

}
