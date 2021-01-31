<?php

namespace Modules\Customer\Http\Controllers;

use Illuminate\Http\Request;
use Modules\Customer\Entities\Customer;
use Modules\System\Entities\CustomerGroup;
use Modules\Base\Http\Controllers\BaseController;
use Modules\Customer\Http\Requests\CustomerFormRequest;

class CustomerController extends BaseController
{
    public function __construct(Customer $model)
    {
        $this->model = $model;
    }

    public function index()
    {
        if(permission('customer-access')){
            $this->setPageData('Customer','Customer','fas fa-user');
            $customer_groups = CustomerGroup::activeCustomerGroups();
            return view('customer::index',compact('customer_groups'));
        }else{
            return $this->unauthorized_access_blocked();
        }
    }

    public function get_datatable_data(Request $request)
    {
        if(permission('customer-access')){
            if($request->ajax()){
                if (!empty($request->customer_group_id)) {
                    $this->model->setCustomerGroupID($request->customer_group_id);
                }
                if (!empty($request->name)) {
                    $this->model->setName($request->name);
                }
                if (!empty($request->phone)) {
                    $this->model->setPhone($request->phone);
                }
                if (!empty($request->email)) {
                    $this->model->setEmail($request->email);
                }

                $this->set_datatable_default_property($request);
                $list = $this->model->getDatatableList();
    
                $data = [];
                $no = $request->input('start');
                foreach ($list as $value) {
                    $no++;
                    $action = '';
                    
                    if(permission('customer-edit')){
                        $action .= ' <a class="dropdown-item edit_data" data-id="' . $value->id . '"><i class="fas fa-edit text-primary"></i> Edit</a>';
                    }
                    if(permission('customer-view')){
                        $action .= ' <a class="dropdown-item view_data" data-id="' . $value->id . '"><i class="fas fa-eye text-success"></i> View</a>';
                    }
                    if(permission('customer-delete')){
                        $action .= ' <a class="dropdown-item delete_data"  data-id="' . $value->id . '" data-name="' . $value->name . '"><i class="fas fa-trash text-danger"></i> Delete</a>';
                    }
                    
    
                    $row = [];
                    
                    if(permission('customer-bulk-delete')){
                        $row[] = table_checkbox($value->id);
                    }
                    $row[] = $no;
                    $row[] = $value->customer_group->group_name;
                    $row[] = $value->name;
                    $row[] = $value->company_name;
                    $row[] = $value->tax_number;
                    $row[] = $value->phone;
                    $row[] = $value->email;
                    $row[] = $value->address;
                    $row[] = $value->city;
                    $row[] = $value->state;
                    $row[] = $value->postal_code;
                    $row[] = $value->country;
                    $row[] = permission('customer-edit') ? change_status($value->id,$value->status,$value->name) : STATUS_LABEL[$value->status];;
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

    public function store_or_update_data(CustomerFormRequest $request)
    {
        if($request->ajax()){
            if(permission('customer-add') || permission('customer-edit')){
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

    public function show(Request $request)
    {
        if($request->ajax()){
            if (permission('customer-view')) {
                $customer = $this->model->with('customer_group')->findOrFail($request->id);
                
                return view('customer::details',compact('customer'))->render();
            }
        }
    }

    public function edit(Request $request)
    {
        if($request->ajax()){
            if(permission('customer-edit')){
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
            if(permission('customer-delete')){
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
            if(permission('customer-bulk-delete')){
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

    public function change_status(Request $request)
    {
        if($request->ajax()){
            if (permission('customer-edit')) {
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

    public function groupData(int $id)
    {
        $data = $this->model->with('customer_group')->find($id);
        return $data ? $data->customer_group->percentage : 0;
    }
}
