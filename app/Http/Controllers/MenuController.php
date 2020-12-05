<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Services\MenuService;
use App\Services\ModuleService;
use App\Http\Controllers\BaseController;
use App\Http\Requests\MenuRequest;

class MenuController extends BaseController
{
    protected $module;
    public function __construct(MenuService $menu,ModuleService $module)
    {
        $this->service = $menu;
        $this->module = $module;
    }

    public function index()
    {
        if(permission('menu-access')){
            $this->setPageData('Menu','Menu','fas fa-th-list');
            return view('menu.index');
        }else{
            return $this->unauthorized_access_blocked();
        }
       
    }

    public function get_datatable_data(Request $request)
    {
        if(permission('menu-access')){
            if($request->ajax()){
                $output = $this->service->get_datatable_data($request);
            }else{
                $output = ['status'=>'error','message'=>'Unauthorized Access Blocked!'];
            }

            return response()->json($output);
        }
    }

    public function store_or_update_data(MenuRequest $request)
    {
        if($request->ajax()){
            if(permission('menu-add') || permission('menu-edit')){
                $result = $this->service->store_or_update_data($request);
                if($result){
                    return $this->response_json($status='success',$message='Data Has Been Saved Successfully',$data=null,$response_code=200);
                }else{
                    return $this->response_json($status='error',$message='Data Cannot Save',$data=null,$response_code=204);
                }
            }else{
                return $this->response_json($status='error',$message='Unauthorized Access Blocked',$data=null,$response_code=401);
            }
        }else{
           return $this->response_json($status='error',$message=null,$data=null,$response_code=401);
        }
    }

    public function edit(Request $request)
    {
        if($request->ajax()){
            if(permission('menu-edit')){
                $data = $this->service->edit($request);
                if($data->count()){
                    return $this->response_json($status='success',$message=null,$data=$data,$response_code=201);
                }else{
                    return $this->response_json($status='error',$message='No Data Found',$data=null,$response_code=204);
                }
            }else{
                return $this->response_json($status='error',$message='Unauthorized Access Blocked',$data=null,$response_code=401);
            }
        }else{
           return $this->response_json($status='error',$message=null,$data=null,$response_code=401);
        }
    }

    public function delete(Request $request)
    {
        if($request->ajax()){
            if(permission('menu-delete')){
                $result = $this->service->delete($request);
                if($result){
                    return $this->response_json($status='success',$message='Data Has Been Deleted Successfully',$data=null,$response_code=200);
                }else{
                    return $this->response_json($status='error',$message='Data Cannot Delete',$data=null,$response_code=204);
                }
            }else{
                return $this->response_json($status='error',$message='Unauthorized Access Blocked',$data=null,$response_code=401);
            }
        }else{
           return $this->response_json($status='error',$message=null,$data=null,$response_code=401);
        }
    }

    public function bulk_delete(Request $request)
    {
        if($request->ajax()){
            if(permission('menu-bulk-delete')){
                $result = $this->service->bulk_delete($request);
                if($result){
                    return $this->response_json($status='success',$message='Data Has Been Deleted Successfully',$data=null,$response_code=200);
                }else{
                    return $this->response_json($status='error',$message='Data Cannot Delete',$data=null,$response_code=204);
                }
            }else{
                return $this->response_json($status='error',$message='Unauthorized Access Blocked',$data=null,$response_code=401);
            }
        }else{
           return $this->response_json($status='error',$message=null,$data=null,$response_code=401);
        }
    }

    public function orderItem(Request $request){
        $menuItemOrder = json_decode($request->input('order'));
        $this->service->orderMenu($menuItemOrder, null);
        $this->module->restore_session_module();
    }
}
