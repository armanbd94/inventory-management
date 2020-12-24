<?php

namespace Modules\Supplier\Entities;

use Modules\Base\Entities\BaseModel;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Supplier extends BaseModel
{
    // use HasFactory;

    protected $fillable = [ 'name', 'company_name', 'vat_number', 'email', 'phone', 'address', 
    'city', 'state', 'postal_code', 'country', 'status', 'created_by', 'updated_by'];
    // protected static function newFactory()
    // {
    //     return \Modules\Supplier\Database\factories\SupplierFactory::new();
    // }

    protected $name;
    protected $phone;
    protected $email;

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
        if(permission('supplier-bulk-delete')){
            $this->column_order = [null,'id','name','company_name','vat_number','phone','email','address','city',
            'state','postal_code','country','status',null];
        }else{
            $this->column_order = ['id','name','company_name','vat_number','phone','email','address','city',
            'state','postal_code','country','status',null];
        }

        $query = self::toBase();

        /*****************
         * *Search Data **
         ******************/
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
