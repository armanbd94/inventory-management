<?php

namespace Modules\Customer\Entities;

use Modules\Base\Entities\BaseModel;

class Customer extends BaseModel
{
    protected $fillable = ['customer_group_id','name', 'company_name', 'tax_number', 'email', 'phone', 'address', 
    'city', 'state', 'postal_code', 'country', 'status', 'created_by', 'updated_by'];

    public function customer_group()
    {
        return $this->belongsTo(\Modules\System\Entities\CustomerGroup::class);
    }

    protected $customer_group_id;
    protected $name;
    protected $phone;
    protected $email;

    public function setCustomerGroupID($customer_group_id)
    {
        $this->customer_group_id = $customer_group_id;
    }
    public function setName($name)
    {
        $this->name = $name;
    }
    public function setPhone($phone)
    {
        $this->phone = $phone;
    }
    public function setEmail($email)
    {
        $this->email = $email;
    }

    private function get_datatable_query()
    {
        if(permission('customer-bulk-delete')){
            $this->column_order = [null,'id','customer_group_id','name','company_name','vat_number','phone','email','address','city',
            'state','postal_code','country','status',null];
        }else{
            $this->column_order = ['id','customer_group_id','name','company_name','vat_number','phone','email','address','city',
            'state','postal_code','country','status',null];
        }

        $query = self::with('customer_group');

        /*****************
         * *Search Data **
         ******************/
        if (!empty($this->customer_group_id)) {
            $query->where('customer_group_id', $this->customer_group_id);
        }
        if (!empty($this->name)) {
            $query->where('name', 'like', '%' . $this->name . '%');
        }
        if (!empty($this->phone)) {
            $query->where('phone', 'like', '%' . $this->phone . '%');
        }
        if (!empty($this->email)) {
            $query->where('email', 'like', '%' . $this->email . '%');
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
