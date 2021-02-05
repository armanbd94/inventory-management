<?php

namespace Modules\HRM\Http\Controllers;

use Illuminate\Http\Request;
use Modules\Base\Http\Controllers\BaseController;
use Modules\HRM\Entities\Attendance;
use Modules\HRM\Entities\Employee;
use Modules\HRM\Http\Requests\AttendanceFormRequest;
use Modules\System\Entities\HRMSetting;

class AttendanceController extends BaseController
{
    public function __construct(Attendance $model)
    {
        $this->model = $model;
    }

    public function index()
    {
        if(permission('attendance-access')){
            $this->setPageData('Attendance','Attendance','fas fa-business-time');
            $employees = Employee::where('status',1)->get();
            return view('hrm::attendance.index',compact('employees'));
        }else{
            return $this->unauthorized_access_blocked();
        }
    }

    public function get_datatable_data(Request $request)
    {
        if(permission('attendance-access')){
            if($request->ajax()){
                if (!empty($request->employee_id)) {
                    $this->model->setEmployeeID($request->employee_id);
                }
                if (!empty($request->from_date)) {
                    $this->model->setFromDate($request->from_date);
                }
                if (!empty($request->to_date)) {
                    $this->model->setToDate($request->to_date);
                }

                $this->set_datatable_default_property($request);
                $list = $this->model->getDatatableList();
    
                $data = [];
                $no = $request->input('start');
                foreach ($list as $value) {
                    $no++;
                    $action = '';
                    
                    if(permission('attendance-edit')){
                        $action .= ' <a class="dropdown-item edit_data" data-id="' . $value->id . '"><i class="fas fa-edit text-primary"></i> Edit</a>';
                    }
                    if(permission('attendance-delete')){
                        $action .= ' <a class="dropdown-item delete_data"  data-id="' . $value->id . '" data-name="' . $value->name . '"><i class="fas fa-trash text-danger"></i> Delete</a>';
                    }
                    
    
                    $row = [];
                    
                    if(permission('attendance-bulk-delete')){
                        $row[] = table_checkbox($value->id);
                    }
                    $row[] = $no;
                    $row[] = $value->employee->name;
                    $row[] = $value->check_in;
                    $row[] = $value->check_out;
                    $row[] = date('d-M-Y',strtotime($value->date));
                    $row[] = ATTENDANCE_STATUS_LABEL[$value->status];
                    $row[] = action_button($action);
                    $data[] = $row;
                }
                return $this->datatable_draw($request->input('draw'),$this->model->count_all(),
                 $this->model->count_filtered(), $data);
            }else{
                $output = $this->access_blocked();
            }

            return response()->json($output);
        }
    }

    public function store_or_update_data(AttendanceFormRequest $request)
    {
        if($request->ajax()){
            if(permission('attendance-add') || permission('attendance-edit')){
                $hrm_setting = HRMSetting::latest()->first();
                $attendance = $this->model->where(['employee_id'=>$request->employee_id,'date'=>$request->date])->first();
                if($hrm_setting)
                {
                    if((strtotime($hrm_setting->check_in) - strtotime($request->check_in)) >= 0)
                    {
                        $status = 1; //Present
                    }else{
                        $status = 2; //Late
                    }
                }
                if(!$attendance)
                {
                    $collection = collect($request->validated());
                    $collection = $collection->merge(compact('status'));
                    $collection = $this->track_data($request->update_id,$collection);
                    $result = $this->model->updateOrCreate(['id'=>$request->update_id],$collection->all());
                    $output = $this->store_message($result,$request->update_id);
                }else{
                    $attendance->check_in = $request->check_in;
                    $attendance->check_out = $request->check_out;
                    $attendance->status = $status;
                    if($attendance->update())
                    {
                        $output = ['status'=>'success','message'=>'Attendance data updated successfully'];
                    }else{
                        $output = ['status'=>'error','message'=>'Attendance data failed to update'];
                    }
                }
                
            }else{
                $output = $this->access_blocked();
            }
            return response()->json($output);
        }else{
           return response()->json($this->access_blocked());
        }
    }

    public function edit(Request $request)
    {
        if($request->ajax()){
            if(permission('attendance-edit')){
                $data = $this->model->findOrFail($request->id);
                $output = $this->data_message($data);
            }else{
                $output = $this->access_blocked();
            }
            return response()->json($output);
        }else{
            return response()->json($this->access_blocked());
        }
    }

    public function delete(Request $request)
    {
        if($request->ajax()){
            if(permission('attendance-delete')){
                $result = $this->model->find($request->id)->delete();
                $output = $this->delete_message($result);
            }else{
                $output = $this->access_blocked();
            }
            return response()->json($output);
        }else{
            return response()->json($this->access_blocked());
        }
    }

    public function bulk_delete(Request $request)
    {
        if($request->ajax()){
            if(permission('attendance-bulk-delete')){
                $result = $this->model->destroy($request->ids);
                $output = $this->bulk_delete_message($result);
            }else{
                $output = $this->access_blocked();
            }
            return response()->json($output);
        }else{
            return response()->json($this->access_blocked());
        }
    }
}
