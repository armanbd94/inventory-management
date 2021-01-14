<?php

namespace Modules\Purchase\Entities;

use Modules\Base\Entities\BaseModel;
use Modules\Product\Entities\Product;
use Modules\Supplier\Entities\Supplier;

class Purchase extends BaseModel
{

    protected $fillable = ['purchase_no', 'supplier_id', 'warehouse_id', 'item', 'total_qty', 
    'total_discount', 'total_tax', 'total_cost', 'order_tax_rate', 'order_tax', 'order_discount',
     'shipping_cost', 'grand_total', 'paid_amount', 'purchase_status', 'payment_status', 
     'document', 'note', 'status', 'created_by', 'updated_by'];
     

     public function supplier()
     {
         return $this->belongsTo(Supplier::class);
     }

     public function purchase_products()
     {
         return $this->belongsToMany(Product::class,'purchase_products','purchase_id','product_id','id','id')
                    ->withPivot(['qty', 'received', 'unit_id', 
                    'net_unit_cost', 'discount', 'tax_rate', 'tax', 'total'])
                    ->withTimestamps();
     }

}
