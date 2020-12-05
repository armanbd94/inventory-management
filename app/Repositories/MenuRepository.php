<?php
namespace App\Repositories;

use App\Models\Menu;
use App\Repositories\BaseRepository;

class MenuRepository extends BaseRepository
{

    protected $order = array('id' => 'desc');
    protected $menu_name;

    public function __construct(Menu $model)
    {
        $this->model = $model;
    }

    public function setMenuName($menu_name)
    {
        $this->menu_name = $menu_name;
    }

    private function get_datatable_query()
    {
        if(permission('menu-bulk-delete')){
            $this->column_order = [null,'id','menu_name','deletable',null];
        }else{
            $this->column_order = ['id','menu_name','deletable',null];
        }

        $query = $this->model->toBase();

        /*****************
         * *Search Data **
         ******************/
        if (!empty($this->menu_name)) {
            $query->where('menu_name', 'like', '%' . $this->menu_name . '%');
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

    public function withMenuItems($id)
    {
        return $this->model->with('menuItems')->findOrFail($id);
    }

}
