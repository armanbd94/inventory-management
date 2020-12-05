<?php
namespace App\Services;

use Carbon\Carbon;
use Illuminate\Http\Request;
use App\Services\BaseService;
use Illuminate\Support\Facades\Session;
use App\Repositories\MenuRepository AS Menu;
use App\Repositories\ModuleRepository AS Module;

class ModuleService extends BaseService{

    protected $module;
    protected $menu;

    public function __construct(Module $module,Menu $menu)
    {
        $this->module = $module;
        $this->menu   = $menu;
    }

    public function index(int $id){
        $data['menu'] = $this->menu->withMenuItems($id);
        return $data;
    }

    public function store_or_update_data(Request $request)
    {
        $collection   = collect($request->validated());
        $menu_id      = $request->menu_id;
        $created_at   = $updated_at = Carbon::now();
        if($request->updated_id){
            $collection = $collection->merge(compact('updated_at'));
        }else{
            $collection = $collection->merge(compact('menu_id','created_at'));
        }

        $result =  $this->module->updateOrCreate(['id'=>$request->update_id],$collection->all());
        if($result){
            if(auth()->user()->role_id == 1){
                $this->restore_session_module();
            }
        }
        return $result;
    }

    public function edit($menu,$module){
        $data['menu']    = $this->menu->withMenuItems($menu);
        $data['module'] = $this->module->findOrFail($module);
        return $data;
    }

    public function delete($module)
    {
        $result =  $this->module->delete($module);
        if($result){
            if(auth()->user()->role_id == 1){
                $this->restore_session_module();
            }
        }
        return $result;
    }


    public function restore_session_module(){
        $modules = $this->module->session_module_list();

        if(!$modules->isEmpty())
        {
            Session::forget('menu');
            Session::put('menu',$modules);
            return true;
        }
        return false;

    }
}