<?php
namespace App\Repositories;

use App\Models\Role;
use App\Repositories\BaseRepository;

class RoleRepository extends BaseRepository
{

    protected $order = array('id' => 'desc');
    protected $role_name;

    public function __construct(Role $model)
    {
        $this->model = $model;
    }

    public function setRoleName($role_name)
    {
        $this->role_name = $role_name;
    }

    private function get_datatable_query()
    {
        if (permission('role-bulk-delete')) {
            $this->column_order = [null,'id','role_name','deletable',null];
        }else{
            $this->column_order = ['id','role_name','deletable',null];
        }

        $query = $this->model::toBase();

        /*****************
         * *Search Data **
         ******************/
        if (!empty($this->role_name)) {
            $query->where('role_name', 'like', '%' . $this->role_name . '%');
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

    public function find_data_with_module_permission(int $id)
    {
        return $this->model->with('module_role','permission_role')->find($id);
    }



}
