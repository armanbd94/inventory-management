<?php

namespace Modules\HRM\Http\Controllers;

use Illuminate\Http\Request;
use Modules\Account\Entities\Account;
use Modules\HRM\Entities\Payroll;
use Modules\Base\Http\Controllers\BaseController;
use Modules\HRM\Entities\Employee;
use Modules\HRM\Http\Requests\PayrollFormRequest;

class PayrollController extends BaseController
{
    public function __construct(Payroll $model)
    {
        $this->model = $model;
    }

    public function index()
    {
        if(permission('payroll-access')){
            $this->setPageData('Payroll','Payroll','far fa-money-bill-alt');
            $employees = Employee::where('status',1)->get();
            $accounts = Account::where('status',1)->get();
            return view('hrm::payroll.index',compact('employees','accounts'));
        }else{
            return $this->unauthorized_access_blocked();
        }
    }

    public function get_datatable_data(Request $request)
    {
        if(permission('payroll-access')){
            if($request->ajax()){
                if (!empty($request->employee_id)) {
                    $this->model->setEmployeeID($request->employee_id);
                }
                if (!empty($request->account_id)) {
                    $this->model->setAccountID($request->account_id);
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
                    
                    if(permission('payroll-edit')){
                        $action .= ' <a class="dropdown-item edit_data" data-id="' . $value->id . '"><i class="fas fa-edit text-primary"></i> Edit</a>';
                    }
                    if(permission('payroll-delete')){
                        $action .= ' <a class="dropdown-item delete_data"  data-id="' . $value->id . '" data-name="' . $value->employee->name . '"><i class="fas fa-trash text-danger"></i> Delete</a>';
                    }
                    
    
                    $row = [];
                    
                    if(permission('payroll-bulk-delete')){
                        $row[] = table_checkbox($value->id);
                    }
                    $row[] = $no;
                    $row[] = $value->employee->name;
                    $row[] = $value->account->name;
                    $row[] = number_format($value->amount,2);
                    $row[] = PAYROLL_PAYMENT_METHOD[$value->payment_method];
                    $row[] = date('Y-m-d',strtotime($value->created_at));
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

    public function store_or_update_data(PayrollFormRequest $request)
    {
        if($request->ajax()){
            if(permission('payroll-add') || permission('payroll-edit')){
                $collection = collect($request->validated());
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

    public function edit(Request $request)
    {
        if($request->ajax()){
            if(permission('payroll-edit')){
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
            if(permission('payroll-delete')){
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
            if(permission('payroll-bulk-delete')){
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
