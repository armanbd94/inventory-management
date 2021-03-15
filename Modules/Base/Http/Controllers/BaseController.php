<?php

namespace Modules\Base\Http\Controllers;

use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Database\Eloquent\Model;

class BaseController extends Controller
{
    protected $model;
    public function __construct(Model $model)
    {
        $this->model = $model;
    }

    protected function setPageData($page_title,$sub_title,$page_icon)
    {
        view()->share(['page_title'=>$page_title,'sub_title'=>$sub_title,'page_icon'=>$page_icon]);
    } 

    protected function set_datatable_default_property(Request $request)
    {
        $this->model->setOrderValue($request->input('order.0.column'));
        $this->model->setDirValue($request->input('order.0.dir'));
        $this->model->setLengthValue($request->input('length'));
        $this->model->setStartValue($request->input('start'));
    }

    protected function datatable_draw($draw, $recordsTotal, $recordsFiltered, $data)
    {
        return array(
            "draw" => $draw,
            "recordsTotal" => $recordsTotal,
            "recordsFiltered" => $recordsFiltered,
            "data" => $data,
        );
    }

    protected function store_message($result, $update_id=null)
    {
        return $result ? ['status'=>'success','message'=> !empty($update_id) ? 'Data has been updated successfully' : 'Data has been saved successfully']
        : ['status'=>'error','message'=> !empty($update_id) ? 'Failed to update data' : 'Falied to save data'];
    }
    protected function delete_message($result)
    {
        return $result ? ['status'=>'success','message'=> 'Data has been deleted successfully']
        : ['status'=>'error','message'=> 'Failed to delete data'];
    }
    protected function bulk_delete_message($result)
    {
        return $result ? ['status'=>'success','message'=> 'Selected data has been deleted successfully']
        : ['status'=>'error','message'=> 'Failed to delete selected data'];
    }

    protected function unauthorized_access_blocked()
    {
        return redirect('unauthorized');
    }

    protected function access_blocked()
    {
        return ['status'=>'error','message'=> 'Unauthorized access blocked'];
    }
    
    protected function track_data($collection,$update_id=null){
        $created_by = $updated_by = auth()->user()->name;
        $created_at = $updated_at = Carbon::now();
        return $update_id ? $collection->merge(compact('updated_by','updated_at')) 
        : $collection->merge(compact('created_by','created_at'));
    }

    protected function data_message($data)
    {
        return $data ? $data : ['status'=>'error','message'=>'No data found'];
    }
}
