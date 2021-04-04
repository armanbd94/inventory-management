<?php

namespace Modules\System\Http\Controllers;

use Illuminate\Http\Request;
use Modules\Base\Http\Controllers\BaseController;
use Modules\System\Entities\HRMSetting;
use Modules\System\Http\Requests\HRMSettingFormRequest;

class HRMSettingController extends BaseController
{
    public function __construct(HRMSetting $model)
    {
        $this->model = $model;
    }

    public function index()
    {
        if(permission('hrm-setting-access')){
            $this->setPageData('HRM Setting','HRM Setting','fas fa-wrench');
            $data = $this->model->find(1);
            return view('system::hrm-setting.index',compact('data'));
        }else{
            return $this->unauthorized_access_blocked();
        }
    }

    public function store(HRMSettingFormRequest $request)
    {
        if($request->ajax()){
            
                $collection = collect($request->validated());
                $collection = $this->track_data($collection,$request->update_id);
                $result = $this->model->updateOrCreate(['id'=>$request->update_id],$collection->all());
                $output = $this->store_message($result,$request->update_id);
           
            return response()->json($output);
        }else{
           return response()->json($this->access_blocked());
        }
    }
}
