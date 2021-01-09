<?php

namespace Modules\Purchase\Entities;

use Modules\Base\Entities\BaseModel;

class Purchase extends BaseModel
{

    protected $fillable = ['purchase_no', 'supplier_id', 'warehouse_id', 'item', 'total_qty', 
    'total_discount', 'total_tax', 'total_cost', 'order_tax_rate', 'order_tax', 'order_discount',
     'shipping_cost', 'grand_total', 'paid_amount', 'purchase_status', 'payment_status', 
     'document', 'note', 'status', 'created_by', 'updated_by'];

     

}
