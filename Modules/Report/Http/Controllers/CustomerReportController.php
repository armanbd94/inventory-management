<?php

namespace Modules\Report\Http\Controllers;

use Illuminate\Http\Request;
use Modules\Account\Entities\Account;
use Modules\Customer\Entities\Customer;
use Modules\Report\Entities\CustomerReport;
use Modules\Base\Http\Controllers\BaseController;

class CustomerReportController extends BaseController
{
    public function __construct(CustomerReport $model)
    {
        $this->model = $model;
    }

    public function index()
    {
        if(permission('customer-report-access')){
            $this->setPageData('Customer Report','Customer Report','fas fa-file');
            $customers = Customer::all();
            return view('report::customer-report.index',compact('customers'));
        }else{
            return $this->unauthorized_access_blocked();
        }
    }

    public function get_datatable_data(Request $request)
    {
        if(permission('customer-report-access')){
            if($request->ajax()){
                if (!empty($request->sale_no)) {
                    $this->model->setSaleNo($request->sale_no);
                }
                if (!empty($request->customer_id)) {
                    $this->model->setCustomerID($request->customer_id);
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
                    $row[] = $value->customer->name.' - '.$value->customer->phone;
                    $row[] = $value->sale_no;
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
