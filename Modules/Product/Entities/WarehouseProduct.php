<?php

namespace Modules\Product\Entities;

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
}
