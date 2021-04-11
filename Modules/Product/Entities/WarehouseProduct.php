<?php

namespace Modules\Product\Entities;

use Illuminate\Support\Facades\DB;
use Modules\Base\Entities\BaseModel;
use Modules\Product\Entities\Product;

class WarehouseProduct extends BaseModel
{
   
    protected $table = 'warehouse_products';
    
    protected $fillable = ['warehouse_id','product_id','qty'];
    
    public function product()
    {
        return $this->belongsTo(Product::class,'product_id','id');
    }

    protected $order = ['p.id' => 'asc'];
    protected $_name;
    protected $_warehouse_id;

    public function setName($name)
    {
        $this->_name = $name;
    }
    public function setWarehouseID($warehouse_id)
    {
        $this->_warehouse_id = $warehouse_id;
    }

    private function get_datatable_query()
    {

        $this->column_order = ['p.id','w.name','p.name','p.code', 'c.name','p.unit_id', 'wp.qty'];
        

        $query = DB::table('warehouse_products as wp')
        ->selectRaw('wp.qty,w.name as warehouse_name,p.id,p.name,p.code,c.name as category_name,u.unit_name')
        ->join('warehouses as w','wp.warehouse_id','=','w.id')
        ->join('products as p','wp.product_id','=','p.id')
        ->join('categories as c','p.category_id','=','c.id')
        ->join('units as u','p.unit_id','=','u.id');
        if($this->_warehouse_id != 0){
            $query->where('wp.warehouse_id', $this->_warehouse_id);
        }
        if (!empty($this->_name)) {
            $query->where('p.name', 'like', '%' . $this->_name . '%');
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
        $query = DB::table('warehouse_products as wp')
        ->selectRaw('wp.qty,w.name as warehouse_name,p.id,p.name,p.code,c.name as category_name,u.unit_name')
        ->join('warehouses as w','wp.warehouse_id','=','w.id')
        ->join('products as p','wp.product_id','=','p.id')
        ->join('categories as c','p.category_id','=','c.id')
        ->join('units as u','p.unit_id','=','u.id');
        if($this->_warehouse_id != 0){
            $query->where('wp.warehouse_id', $this->_warehouse_id);
        }
        return $query->get()->count();
    }
}
