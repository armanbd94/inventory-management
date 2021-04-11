<?php

namespace Modules\Stock\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Modules\System\Entities\Warehouse;
use Modules\Product\Entities\WarehouseProduct;
use Modules\Base\Http\Controllers\BaseController;

class StockController extends BaseController
{
    public function __construct(WarehouseProduct $model)
    {
        $this->model = $model;
    }

    public function index()
    {
        if(permission('stock-access')){
            $this->setPageData('Manage Stock','Manage Stock','fas fa-boxes');
            $data = [
                'warehouses' => Warehouse::toBase()->pluck('name','id'),
            ];
            return view('stock::index',$data);
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

                if (!empty($request->warehouse_id)) {
                    $this->model->setWarehouseID($request->warehouse_id);
                }

                $this->set_datatable_default_property($request);
                $list = $this->model->getDatatableList();
    
                $data = [];
                $no = $request->input('start');
                foreach ($list as $value) {
                    $no++;
                    $row = [];
                    $row[] = $no;
                    $row[] = $value->warehouse_name;
                    $row[] = $value->name;
                    $row[] = $value->code;
                    $row[] = $value->category_name;
                    $row[] = $value->unit_name;
                    $row[] = number_format($value->qty,2);
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
