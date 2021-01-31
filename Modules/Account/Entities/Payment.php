<?php

namespace Modules\Account\Entities;

use Modules\Account\Entities\Account;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Payment extends Model
{
    use HasFactory;

    protected $fillable = ['account_id', 'purchase_id', 'sale_id','amount', 'change', 'payment_method', 
    'payment_no', 'payment_note', 'created_by', 'updated_by'];

    public function account()
    {
        return $this->belongsTo(Account::class,'account_id','id');
    }

}
