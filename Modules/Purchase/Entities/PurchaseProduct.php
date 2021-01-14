<?php

namespace Modules\Purchase\Entities;

use Modules\Base\Entities\BaseModel;

class PurchaseProduct extends BaseModel
{
    protected $table = 'purchase_products';
    
    protected $fillable = ['purchase_id', 'product_id', 'qty', 'received', 'unit_id', 
    'net_unit_cost', 'discount', 'tax_rate', 'tax', 'total'];
    

}
