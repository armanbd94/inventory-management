<?php

namespace Modules\Report\Http\Controllers;

use Illuminate\Http\Request;
use Modules\Purchase\Entities\Purchase;
use Modules\Supplier\Entities\Supplier;
use Modules\Base\Http\Controllers\BaseController;

class SupplierReportController extends BaseController
{
    public function __construct(Purchase $model)
    {
        $this->model = $model;
    }

    public function index()
    {
        if(permission('supplier-report-access')){
            $this->setPageData('Supplier Report','Supplier Report','fas fa-file');
            $suppliers=Supplier::all();
            return view('report::supplier-report.index',compact('suppliers'));
        }else{
            return $this->unauthorized_access_blocked();
        }
    }

    public function get_datatable_data(Request $request)
    {
        if(permission('supplier-report-access')){
            if($request->ajax()){
                if (!empty($request->purchase_no)) {
                    $this->model->setPurchaseNo($request->purchase_no);
                }
                if (!empty($request->supplier_id)) {
                    $this->model->setSupplierID($request->supplier_id);
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

                    $row = [];

                    $row[] = $no;
                    $row[] = $value->supplier->name.' - '.$value->supplier->phone;
                    $row[] = $value->purchase_no;
                    $row[] = date(config('settings.date_format'),strtotime($value->created_at));
                    $row[] = number_format($value->grand_total,2,'.',',');
                    $row[] = number_format($value->paid_amount,2,'.',',');
                    $row[] = number_format(($value->grand_total - $value->paid_amount),2,'.',',');
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
