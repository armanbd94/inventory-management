<?php
namespace App\Services;

use App\Models\ModuleRole;
use App\Models\PermissionRole;
use Illuminate\Http\Request;
use App\Services\BaseService;
use App\Repositories\RoleRepository AS Role;
use App\Repositories\ModuleRepository AS Module;
use Carbon\Carbon;

class RoleService extends BaseService
{
    protected $role;
    protected $module;

    public function __construct(Role $role,Module $module)
    {
        $this->role   = $role;
        $this->module = $module;
    }

    public function index(){
        return $this->role->all();
    }

    public function get_datatable_data(Request $request)
    {
        if ($request->ajax()) {

            if (!empty($request->role_name)) {
                $this->role->setRoleName($request->role_name);
            }

            $this->role->setOrderValue($request->input('order.0.column'));
            $this->role->setDirValue($request->input('order.0.dir'));
            $this->role->setLengthValue($request->input('length'));
            $this->role->setStartValue($request->input('start'));

            $list = $this->role->getDatatableList();

            $data = [];
            $no = $request->input('start');
            foreach ($list as $value) {
                $no++;
                $action = '';
                if (permission('role-edit')) {
                $action .= ' <a class="dropdown-item edit_data" href="'.route('role.edit',['id'=>$value->id]).'"><i class="fas fa-edit text-primary"></i> Edit</a>';
                }
                if (permission('role-view')) {
                $action .= ' <a class="dropdown-item view_data" href="'.route('role.view',['id'=>$value->id]).'"><i class="fas fa-eye text-warning"></i> View</a>';
                }
                if (permission('role-delete')) {
                    if($value->deletable == 1){
                        $action .= ' <a class="dropdown-item delete_data"  data-id="' . $value->id . '" data-name="' . $value->role_name . '"><i class="fas fa-trash text-danger"></i> Delete</a>';
                    }
                }

                $row = [];
                if (permission('role-bulk-delete')) {
                    $row[] = ($value->deletable == 1) ? table_checkbox($value->id) : '';
                }
                $row[] = $no;
                $row[] = $value->role_name;
                $row[] = DELETABLE[$value->deletable];
                $row[] = action_button($action);
                $data[] = $row;
            }
            return $this->datatable_draw($request->input('draw'),$this->role->count_all(),
             $this->role->count_filtered(), $data);
        }
    }

    public function store_or_update_data(Request $request)
    {
        $collection = collect($request->validated());
        $role = $this->role->updateOrCreate(['id'=>$request->update_id],$collection->all());
        if($role){
            $role->module_role()->sync($request->module);
            $role->permission_role()->sync($request->permission);
            return true;
        }
        return false;
    }

    public function edit(int $id)
    {
        $role = $this->role->find_data_with_module_permission($id);
        $role_module = [];
        if(!$role->module_role->isEmpty())
        {
            foreach ($role->module_role as $value) {
                array_push($role_module,$value->id);
            }
        }
        $role_permission = [];
        if(!$role->permission_role->isEmpty())
        {
            foreach ($role->permission_role as $value) {
                array_push($role_permission,$value->id);
            }
        }

        $data = [
            'role' => $role,
            'role_module' => $role_module,
            'role_permission' => $role_permission,
        ];

        return $data;
    }

    public function delete(Request $request)
    {
        $role = $this->role->find_data_with_module_permission($request->id);
        if(!$role->users->isEmpty()){
            $response = 2;
        }else{
            $delete_module_role = $role->module_role()->detach();
            $delete_permission_role = $role->permission_role()->detach();
            if($delete_module_role && $delete_permission_role){
                $role->delete();
                $response = 1;
            }else{
                $response = 3;
            }
        }
        return $response;
    }

    public function bulk_delete(Request $request)
    {
        
        if(!empty($request->ids)){
            $delete_list = [];
            $undelete_list = [];
            foreach ($request->ids as $id) {
                $role = $this->role->find($id);
                if(!$role->users->isEmpty()){
                    array_push($undelete_list,$role->role_name);
                }else{
                    array_push($delete_list,$id);
                }
            }
            $message = !empty($undelete_list) ? 'These roles('.implode(',',$undelete_list).') can\'t delete because they are related with many users' : '';   
            if(!empty($delete_list)){
                $delete_module_role = ModuleRole::whereIn('role_id',$delete_list)->delete();
                $delete_permission_role = PermissionRole::whereIn('role_id',$delete_list)->delete();
                if($delete_module_role && $delete_permission_role){
                    $this->role->destroy($delete_list);
                    $response = ['status' => 1,'message'=> $message];
                }else{
                    $response = ['status' => 2,'message'=> $message];
                }
            }else{
                $response = ['status' => 3,'message'=> $message];
            }
            return $response;
        }
    }

    public function permission_module_list(){
        return $this->module->permission_module_list();
    }

    
}