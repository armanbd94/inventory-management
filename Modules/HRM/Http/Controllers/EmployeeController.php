<?php

namespace Modules\HRM\Http\Controllers;

use App\Traits\UploadAble;
use Illuminate\Http\Request;
use Modules\HRM\Entities\Employee;
use Modules\HRM\Entities\Department;
use Modules\Base\Http\Controllers\BaseController;
use Modules\HRM\Http\Requests\EmployeeFormRequest;

class EmployeeController extends BaseController
{
    use UploadAble;
    public function __construct(Employee $model)
    {
        $this->model = $model;
    }

    public function index()
    {
        if(permission('employee-access')){
            $this->setPageData('Employee','Employee','fas fa-users');
            $departments = Department::where('status',1)->get();
            return view('hrm::employee.index',compact('departments'));
        }else{
            return $this->unauthorized_access_blocked();
        }
    }

    public function get_datatable_data(Request $request)
    {
        if(permission('employee-access')){
            if($request->ajax()){
                if (!empty($request->department_id)) {
                    $this->model->setDepartmentID($request->department_id);
                }
                if (!empty($request->name)) {
                    $this->model->setName($request->name);
                }
                if (!empty($request->phone)) {
                    $this->model->setPhone($request->phone);
                }

                $this->set_datatable_default_property($request);
                $list = $this->model->getDatatableList();
    
                $data = [];
                $no = $request->input('start');
                foreach ($list as $value) {
                    $no++;
                    $action = '';
                    
                    if(permission('employee-edit')){
                        $action .= ' <a class="dropdown-item edit_data" data-id="' . $value->id . '"><i class="fas fa-edit text-primary"></i> Edit</a>';
                    }
                    if(permission('employee-view')){
                        $action .= ' <a class="dropdown-item view_data" data-id="' . $value->id . '"><i class="fas fa-eye text-success"></i> View</a>';
                    }
                    if(permission('employee-delete')){
                        $action .= ' <a class="dropdown-item delete_data"  data-id="' . $value->id . '" data-name="' . $value->name . '"><i class="fas fa-trash text-danger"></i> Delete</a>';
                    }
                    
    
                    $row = [];
                    
                    if(permission('employee-bulk-delete')){
                        $row[] = table_checkbox($value->id);
                    }
                    $row[] = $no;

                    $row[] = $value->name;
                    $row[] = $this->avatar($value);
                    $row[] = $value->phone;
                    $row[] = $value->department->name;
                    $row[] = $value->city;
                    $row[] = $value->state;
                    $row[] = $value->postal_code;
                    $row[] = $value->country;
                    $row[] = permission('employee-edit') ? change_status($value->id,$value->status,$value->name) : STATUS_LABEL[$value->status];;
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

    protected function avatar($employee){
        if($employee->image){
            return "<img src='storage/".EMPLOYEE_IMAGE_PATH.$employee->image."' style='width:50px;'/>";
        }else{
            return "<img src='images/male.svg' style='width:50px;'/>";
        }
    }

    public function store_or_update_data(EmployeeFormRequest $request)
    {
        if($request->ajax()){
            if(permission('employee-add') || permission('employee-edit')){
                $collection = collect($request->validated())->except(['image']);
                $image = $request->old_image;
                if($request->hasFile('image')){
                    $image = $this->upload_file($request->file('image'),EMPLOYEE_IMAGE_PATH);
                    
                    if(!empty($request->old_image)){
                        $this->delete_file($request->old_image,EMPLOYEE_IMAGE_PATH);
                    }
                }
                $collection = $collection->merge(compact('image'));
                $collection = $this->track_data($request->update_id,$collection);
                $result = $this->model->updateOrCreate(['id'=>$request->update_id],$collection->all());
                $output = $this->store_message($result,$request->update_id);
            }else{
                $output = $this->access_blocked();
            }
            return response()->json($output);
        }else{
           return response()->json($this->access_blocked());
        }
    }

    public function show(Request $request)
    {
        if($request->ajax()){
            if (permission('employee-view')) {
                $employee = $this->model->with('department')->findOrFail($request->id);
                return view('hrm::employee.details',compact('employee'))->render();
            }
        }
    }

    public function edit(Request $request)
    {
        if($request->ajax()){
            if(permission('employee-edit')){
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
            if(permission('employee-delete')){
                $employee = $this->model->find($request->id);
                $image = $employee->image;
                $result = $employee->delete();
                if($result && !empty($image)){
                    $this->delete_file($image,EMPLOYEE_IMAGE_PATH);
                }
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
            if(permission('employee-bulk-delete')){
                $employees = $this->model->toBase()->select('image')->whereIn('id',$request->ids)->get();
                $result = $this->model->destroy($request->ids);
                if($result){
                    if(!empty($employees)){
                        foreach ($employees as $employee) {
                            if($employee->image){
                                $this->delete_file($employee->image,EMPLOYEE_IMAGE_PATH);
                            }
                        }
                    }
                }
                $output = $this->bulk_delete_message($result);
            }else{
                $output = $this->access_blocked();
            }
            return response()->json($output);
        }else{
            return response()->json($this->access_blocked());
        }
    }

    public function change_status(Request $request)
    {
        if($request->ajax()){
            if (permission('employee-edit')) {
                $result = $this->model->find($request->id)->update(['status'=>$request->status]);
                $output = $result ? ['status'=>'success','message'=>'Status has been changed successfully']
                : ['status'=>'error','message'=>'Failed to change status'];
            }else{
                $output = $this->access_blocked();
            }
            return response()->json($output);
        }else{
            return response()->json($this->access_blocked());
        }
    }
}
