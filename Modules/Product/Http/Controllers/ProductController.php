<?php

namespace Modules\Product\Http\Controllers;

use App\Traits\UploadAble;
use Illuminate\Http\Request;
use Modules\Product\Entities\Product;
use Modules\Base\Http\Controllers\BaseController;
use Modules\Product\Http\Requests\ProductFormRequest;
use Keygen;
use Modules\Category\Entities\Category;
use Modules\System\Entities\Brand;
use Modules\System\Entities\Tax;
use Modules\System\Entities\Unit;

class ProductController extends BaseController
{
    use UploadAble;
    public function __construct(Product $model)
    {
        $this->model = $model;
    }

    public function index()
    {
        if(permission('product-access')){
            $this->setPageData('Manage Product','Manage Product','fas fa-box');
            $data = [
                'brands' => Brand::all(),
                'categories' => Category::all(),
                'units' => Unit::all(),
                'taxes' => Tax::all(),
            ];
            return view('product::index',$data);
        }else{
            return $this->unauthorized_access_blocked();
        }
    }

    public function get_datatable_data(Request $request)
    {
        if(permission('product-access')){
            if($request->ajax()){
                if (!empty($request->name)) {
                    $this->model->setName($request->name);
                }
                if (!empty($request->code)) {
                    $this->model->setCode($request->code);
                }
                if (!empty($request->brand_id)) {
                    $this->model->setBrandID($request->brand_id);
                }
                if (!empty($request->category_id)) {
                    $this->model->setCategoryID($request->category_id);
                }

                $this->set_datatable_default_property($request);
                $list = $this->model->getDatatableList();
    
                $data = [];
                $no = $request->input('start');
                foreach ($list as $value) {
                    $no++;
                    $action = '';
                    
                    if(permission('product-edit')){
                        $action .= ' <a class="dropdown-item edit_data" data-id="' . $value->id . '"><i class="fas fa-edit text-primary"></i> Edit</a>';
                    }
                    if(permission('product-view')){
                        $action .= ' <a class="dropdown-item view_data" data-id="' . $value->id . '"><i class="fas fa-eye text-success"></i> View</a>';
                    }
                    if(permission('product-delete')){
                        $action .= ' <a class="dropdown-item delete_data"  data-id="' . $value->id . '" data-name="' . $value->name . '"><i class="fas fa-trash text-danger"></i> Delete</a>';
                    }
                    
                    $row = [];
                    
                    if(permission('product-bulk-delete')){
                        $row[] = table_checkbox($value->id);
                    }
                    $row[] = $no;
                    $row[] = table_image($value->image,PRODUCT_IMAGE_PATH,$value->name);
                    $row[] = $value->name;
                    $row[] = $value->code;
                    $row[] = $value->brand->title;
                    $row[] = $value->category->name;
                    $row[] = $value->unit->unit_name;
                    $row[] = number_format($value->cost,2);
                    $row[] = number_format($value->price,2);
                    $row[] = number_format($value->qty,2);
                    $row[] = $value->alert_qty ? number_format($value->alert_qty,2) : 0;
                    $row[] = $value->tax->name;
                    $row[] = TAX_METHOD[$value->tax_method];
                    $row[] = permission('product-edit') ? change_status($value->id,$value->status,$value->name) : STATUS_LABEL[$value->status];;
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

    public function store_or_update_data(ProductFormRequest $request)
    {
        if($request->ajax()){
            if(permission('product-add') || permission('product-edit')){
                $collection = collect($request->validated())->except(['image','qty','alert_qty']);
                $qty = $request->qty ? $request->qty : null;
                $alert_qty = $request->alert_qty ? $request->alert_qty : null;
                $collection = $this->track_data($request->update_id,$collection);
                $image = $request->old_image;
                if($request->hasFile('image')){
                    $image = $this->upload_file($request->file('image'),PRODUCT_IMAGE_PATH);
                    
                    if(!empty($request->old_image)){
                        $this->delete_file($request->old_image,PRODUCT_IMAGE_PATH);
                    }
                }
                $collection = $collection->merge(compact('image','qty','alert_qty'));
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
            if (permission('product-view')) {
                $product = $this->model->with('brand','category','unit','purchase_unit','sale_unit','tax')->findOrFail($request->id);
                
                return view('product::details',compact('product'))->render();
            }
        }
    }

    public function edit(Request $request)
    {
        if($request->ajax()){
            if(permission('product-edit')){
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
            if(permission('product-delete')){
                $product = $this->model->find($request->id);
                $image = $product->image;
                $result = $product->delete();
                if($result){
                    if(!empty($image)){
                        $this->delete_file($image,PRODUCT_IMAGE_PATH);
                    }
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
            if(permission('product-bulk-delete')){
                $products = $this->model->toBase()->select('image')->whereIn('id',$request->ids)->get();
                $result = $this->model->destroy($request->ids);
                if($result){
                    if(!empty($products)){
                        foreach ($products as $product) {
                            if($product->image){
                                $this->delete_file($product->image,PRODUCT_IMAGE_PATH);
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
            if (permission('product-edit')) {
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

    public function generate_code()
    {
        return Keygen::numeric(8)->generate();
    }

    public function populate_unit($id)
    {
        $units = Unit::where('base_unit',$id)->orWhere('id',$id)->pluck('unit_name','id');
        return json_encode($units);
    }

    public function product_autocomplete_search(Request $request)
    {
        if($request->ajax())
        {
            if(!empty($request->search))
            {
                $data = $this->model->where('name','like','%'.$request->search.'%')
                                    ->orWhere('code','like','%'.$request->search.'%')
                                    ->get();
                $output = [];
                if(!$data->isEmpty())
                {
                    foreach ($data as $key => $value) {
                        $item['id'] = $value->id;
                        $item['value'] = $value->code.' - '.$value->name;
                        $item['label'] = $value->code.' - '.$value->name;
                        $output[] = $item;
                    }
                }else{
                    $output['value'] = '';
                    $output['label'] = 'No Record Found';
                }
                return $output;
            }
        }
    }

    public function product_search(Request $request)
    {
        if($request->ajax())
        {
            $code = explode('-',$request['data']);
            $product_data = $this->model->with('tax')->where('code',$code[0])->first();
            if($product_data)
            {
                $product['id']         = $product_data->id;
                $product['name']       = $product_data->name;
                $product['code']       = $product_data->code;
                $product['cost']       = $product_data->cost;
                $product['tax_rate']   = $product_data->tax->rate;
                $product['tax_name']   = $product_data->tax->name;
                $product['tax_method'] = $product_data->tax_method;
    
                $units = Unit::where('base_unit',$product_data->unit_id)->orWhere('id',$product_data->unit_id)->get();
                $unit_name            = [];
                $unit_operator        = [];
                $unit_operation_value = [];
                if($units)
                {
                    foreach ($units as $unit) {
                        if($product_data->purchase_unit_id == $unit->id)
                        {
                            array_unshift($unit_name,$unit->unit_name);
                            array_unshift($unit_operator,$unit->operator);
                            array_unshift($unit_operation_value,$unit->operation_value);
                        }else{
                            $unit_name           [] = $unit->unit_name;
                            $unit_operator       [] = $unit->operator;
                            $unit_operation_value[] = $unit->operation_value;
                        }
                    }
                }
                $product['unit_name'] = implode(',',$unit_name).',';
                $product['unit_operator'] = implode(',',$unit_operator).',';
                $product['unit_operation_value'] = implode(',',$unit_operation_value).',';
                return $product;
            }
        }
    }
}
