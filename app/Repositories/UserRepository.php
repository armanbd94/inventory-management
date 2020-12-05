<?php
namespace App\Repositories;

use App\Models\User;
use App\Repositories\BaseRepository;

class UserRepository extends BaseRepository
{

    protected $order = array('id' => 'desc');

    protected $role_id;
    protected $name;
    protected $email;
    protected $mobile_no;
    protected $status;

    public function __construct(User $model)
    {
        $this->model = $model;
    }

    public function setName($name)
    {
        $this->name = $name;
    }
    public function setEmail($email)
    {
        $this->email = $email;
    }
    public function setRoleID($role_id)
    {
        $this->role_id = $role_id;
    }
    public function setMobileNo($mobile_no)
    {
        $this->mobile_no = $mobile_no;
    }
    public function setStatus($status)
    {
        $this->status = $status;
    }

    private function get_datatable_query()
    {
        if(permission('user-bulk-delete')){
            $this->column_order = [null,'id','id','name','role_id','email','mobile_no','gender','status',null];
        }else{
            $this->column_order = ['id','id','name','role_id','email','mobile_no','gender','status',null]; 
        }
        $query = $this->model->with('role:id,role_name');

        /*****************
         * *Search Data **
         ******************/
        if (!empty($this->name)) {
            $query->where('name', 'like', '%' . $this->name . '%');
        }
        if (!empty($this->email)) {
            $query->where('email', 'like', '%' . $this->email . '%');
        }
        if (!empty($this->mobile_no)) {
            $query->where('mobile_no', 'like', '%' . $this->mobile_no . '%');
        }
        if (!empty($this->role_id)) {
            $query->where('role_id', 'like', '%' . $this->role_id . '%');
        }
        if (!empty($this->status)) {
            $query->where('status', 'like', '%' . $this->status . '%');
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
        return $this->model->toBase()->get()->count();
    }


}
