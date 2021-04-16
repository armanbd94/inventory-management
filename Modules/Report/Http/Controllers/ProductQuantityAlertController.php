<?php

namespace Modules\Report\Http\Controllers;

use Illuminate\Http\Request;
use Modules\System\Entities\Unit;
use Modules\System\Entities\Brand;
use Modules\Category\Entities\Category;
use Modules\Base\Http\Controllers\BaseController;
use Modules\Report\Entities\ProductQuantityAlert;

class ProductQuantityAlertController extends BaseController
{
    public function __construct(ProductQuantityAlert $model)
    {
        $this->model = $model;
    }

    public function index()
    {
        if(permission('product-quantity-alert-access')){
            $this->setPageData('Product Quantity Alert','Product Quantity Alert','fas fa-box');
            $data = [
                'brands' => Brand::all(),
                'categories' => Category::all(),
                'units' => Unit::all(),
            ];
            return view('report::product-quantity-alert.index',$data);
        }else{
            return $this->unauthorized_access_blocked();
        }
    }

    public function get_datatable_data(Request $request)
    {
        if(permission('product-quantity-alert-access')){
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
                    
                    $row = [];
     
                    $row[] = $no;
                    $row[] = table_image($value->image,PRODUCT_IMAGE_PATH,$value->name);
                    $row[] = $value->name;
                    $row[] = $value->code;
                    $row[] = $value->brand->title;
                    $row[] = $value->category->name;
                    $row[] = $value->unit->unit_name;
                    $row[] = number_format($value->qty,2);
                    $row[] = $value->alert_qty ? number_format($value->alert_qty,2) : 0;
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
}
