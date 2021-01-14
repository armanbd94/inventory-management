<?php

namespace Modules\Product\Entities;

use Modules\Base\Entities\BaseModel;

class WarehouseProduct extends BaseModel
{
   
    protected $table = 'warehouse_products';
    
    protected $fillable = ['warehouse_id','product_id','qty'];
    

}
