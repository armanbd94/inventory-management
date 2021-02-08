<?php

namespace Modules\Account\Http\Controllers;

use Illuminate\Http\Request;
use Modules\Account\Entities\Account;
use Modules\Account\Entities\Payment;
use Modules\Base\Http\Controllers\BaseController;
use Modules\Account\Http\Requests\AccountFormRequest;
use Modules\Expense\Entities\Expense;
use Modules\HRM\Entities\Payroll;

class AccountController extends BaseController
{
    public function __construct(Account $model)
    {
        $this->model = $model;
    }

    public function index()
    {
        if(permission('account-access')){
            $this->setPageData('Manage Account','Manage Account','fas fa-money-bill-alt');
            return view('account::index');
        }else{
            return $this->unauthorized_access_blocked();
        }
    }

    public function get_datatable_data(Request $request)
    {
        if(permission('account-access')){
            if($request->ajax()){
                if (!empty($request->account_no)) {
                    $this->model->setAccountNo($request->account_no);
                }
                if (!empty($request->name)) {
                    $this->model->setAccountName($request->name);
                }

                $this->set_datatable_default_property($request);
                $list = $this->model->getDatatableList();
    
                $data = [];
                $no = $request->input('start');
                foreach ($list as $value) {
                    $no++;
                    $action = '';
                    
                    if(permission('account-edit')){
                        $action .= ' <a class="dropdown-item edit_data" data-id="' . $value->id . '"><i class="fas fa-edit text-primary"></i> Edit</a>';
                    }
                    if(permission('account-delete')){
                        $action .= ' <a class="dropdown-item delete_data"  data-id="' . $value->id . '" data-name="' . $value->name . '"><i class="fas fa-trash text-danger"></i> Delete</a>';
                    }
                    
    
                    $row = [];
                    
                    if(permission('account-bulk-delete')){
                        $row[] = table_checkbox($value->id);
                    }
                    $row[] = $no;
                    $row[] = $value->account_no;
                    $row[] = $value->name;
                    $row[] = $value->initial_balance ? number_format($value->initial_balance,2) : 0;
                    $row[] = $value->note;
                    $row[] = permission('account-edit') ? change_status($value->id,$value->status,$value->name) : STATUS_LABEL[$value->status];;
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

    public function store_or_update_data(AccountFormRequest $request)
    {
        if($request->ajax()){
            if(permission('account-add') || permission('account-edit')){
                $collection = collect($request->validated());
                $initial_balance = $request->initial_balance ? $request->initial_balance : 0;
                $collection = $collection->merge(compact('initial_balance'));
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
            if(permission('account-edit')){
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
            if(permission('account-delete')){
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
            if(permission('account-bulk-delete')){
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
            if (permission('account-edit')) {
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

    public function balance_sheet()
    {
        if(permission('balance-sheet-access')){
            $this->setPageData('Balance Sheet','Balance Sheet','fas fa-file-invoice-dollar');
            $accounts = $this->model->where('status',1)->get();
            $debit = [];
            $credit = [];
            if(!$accounts->isEmpty())
            {
                foreach ($accounts as $account) {
                    $payment_received = Payment::whereNotNull('sale_id')->where('account_id',$account->id)->sum('amount');
                    $payment_paid     = Payment::whereNotNull('purchase_id')->where('account_id',$account->id)->sum('amount');
                    $expenses         = Expense::where('account_id',$account->id)->sum('amount');
                    $payrolls         = Payroll::where('account_id',$account->id)->sum('amount');
                    $credit[]           = $payment_received + $account->inital_balance;
                    $debit[]            = $payment_paid + $expenses + $payrolls;
                }
            }
            return view('account::balance-sheet',compact('accounts','debit','credit'));
        }else{
            return $this->unauthorized_access_blocked();
        }
    }
}
