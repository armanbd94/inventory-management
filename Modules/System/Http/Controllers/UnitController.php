<?php

namespace Modules\System\Http\Controllers;

use Illuminate\Http\Request;
use Modules\System\Entities\Unit;
use Modules\Base\Http\Controllers\BaseController;
use Modules\System\Http\Requests\UnitFormRequest;

class UnitController extends BaseController
{
    public function __construct(Unit $model)
    {
        $this->model = $model;
    }

    public function index()
    {
        if(permission('unit-access')){
            $this->setPageData('Unit','Unit','fas fa-weight-hanging');
            
            return view('system::unit.index');
        }else{
            return $this->unauthorized_access_blocked();
        }
    }

    public function get_datatable_data(Request $request)
    {
        if(permission('unit-access')){
            if($request->ajax()){
                if (!empty($request->unit_name)) {
                    $this->model->setUnitName($request->unit_name);
                }

                $this->set_datatable_default_property($request);
                $list = $this->model->getDatatableList();
    
                $data = [];
                $no = $request->input('start');
                foreach ($list as $value) {
                    $no++;
                    $action = '';
                    
                    if(permission('unit-edit')){
                        $action .= ' <a class="dropdown-item edit_data" data-id="' . $value->id . '"><i class="fas fa-edit text-primary"></i> Edit</a>';
                    }
                    if(permission('unit-delete')){
                        $action .= ' <a class="dropdown-item delete_data"  data-id="' . $value->id . '" data-name="' . $value->unit_name . '"><i class="fas fa-trash text-danger"></i> Delete</a>';
                    }
                    
                    $row = [];
                    
                    if(permission('unit-bulk-delete')){
                        $row[] = table_checkbox($value->id);
                    }
                    $row[] = $no;
                    $row[] = $value->unit_name;
                    $row[] = $value->unit_code;
                    $row[] = $value->baseUnit->unit_name;
                    $row[] = $value->operator;
                    $row[] = $value->operation_value;
                    $row[] = permission('unit-edit') ? change_status($value->id,$value->status,$value->unit_name) : STATUS_LABEL[$value->status];;
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

    public function store_or_update_data(UnitFormRequest $request)
    {
        if($request->ajax()){
            if(permission('unit-add') || permission('unit-edit')){
                $collection = collect($request->validated())->except(['operator','operation_value']);
                if($request->operator){
                    $collection = $collection->merge(['operator'=>$request->operator]);
                }
                if($request->operation_value){
                    $collection = $collection->merge(['operation_value'=>$request->operation_value]);
                }
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
            if(permission('unit-edit')){
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
            if(permission('unit-delete')){
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
            if(permission('unit-bulk-delete')){
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
            if (permission('unit-edit')) {
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

    public function base_unit(Request $request)
    {
        if($request->ajax())
        {
            $units = $this->model->where(['base_unit'=>null,'status'=>1])->get();
            $output = '<option value="">Select Please</option>';
            if (!$units->isEmpty()){
                foreach ($units as $unit){
                    $output .=  '<option value="'.$unit->id .'">'. $unit->unit_name.'('.$unit->unit_code.')</option>';
                }
            }
            return $output;
        }
    }
}
