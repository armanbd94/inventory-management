<?php
namespace App\Services;


use Carbon\Carbon;
use Illuminate\Http\Request;
use App\Services\BaseService;
use Illuminate\Support\Facades\Session;
use App\Repositories\ModuleRepository AS Module;
use App\Repositories\PermissionRepository AS Permission;

class PermissionService extends BaseService{

    protected $permission;
    protected $module;

    public function __construct(Permission $permission,Module $module)
    {
        $this->permission   = $permission;
        $this->module      = $module;
    }

    public function index()
    {
        $data['modules'] = $this->module->module_list(1); //1=backend menu
        return $data;
    }

    public function get_datatable_data(Request $request)
    {
        if ($request->ajax()) {

            if (!empty($request->name)) {
                $this->permission->setName($request->name);
            }
            if (!empty($request->module_id)) {
                $this->permission->setModuleID($request->module_id);
            }

            $this->permission->setOrderValue($request->input('order.0.column'));
            $this->permission->setDirValue($request->input('order.0.dir'));
            $this->permission->setLengthValue($request->input('length'));
            $this->permission->setStartValue($request->input('start'));

            $list = $this->permission->getDatatableList();

            $data = [];
            $no = $request->input('start');
            foreach ($list as $value) {
                $no++;
                $action = '';
                if (permission('permission-edit')) {
                $action .= ' <a class="dropdown-item edit_data" data-id="' . $value->id . '"><i class="fas fa-edit text-primary"></i> Edit</a>';
                }
                if (permission('permission-delete')) {
                $action .= ' <a class="dropdown-item delete_data"  data-id="' . $value->id . '" data-name="' . $value->menu_name . '"><i class="fas fa-trash text-danger"></i> Delete</a>';
                }

                $row = [];
                if (permission('permission-bulk-delete')) {
                $row[] = table_checkbox($value->id);
                }
                $row[] = $no;
                $row[] = $value->module->module_name;
                $row[] = $value->name;
                $row[] = $value->slug;
                $row[] = action_button($action);
                $data[] = $row;
            }
            return $this->datatable_draw($request->input('draw'),$this->permission->count_all(),
             $this->permission->count_filtered(), $data);
        }
    }

    public function store(Request $request)
    {
        $permission_data = [];
        foreach ($request->permission as $value) {
            $permission_data[] = [
                'module_id' => $request->module_id,
                'name' => $value['name'],
                'slug' => $value['slug'],
                'created_at' => Carbon::now()
            ];
        }
        return $this->permission->insert($permission_data);
        
    }

    public function edit(Request $request)
    {
        return $this->permission->find($request->id);
    }

    public function update(Request $request)
    {
        $collection = collect($request->validated());
        $updated_at = Carbon::now();
        $collection = $collection->merge(compact('updated_at'));
        return $this->permission->update($collection->all(),$request->update_id);
    }

    public function delete(Request $request)
    {
        return $this->permission->delete($request->id);
    }

    public function bulk_delete(Request $request)
    {
        return $this->permission->destroy($request->ids);
    }

    public function restore_session_permission_list(){
        $permissions = $this->permission->session_permission_list();

        $permission = [];
        if(!$permissions->isEmpty())
        {
            foreach ($permission as $value) {
                array_push($permission,$value->slug);
            }

            Session::forget('permission');
            Session::put('permission',$permission);
            return true;
        }
        return false;
    }

    
}