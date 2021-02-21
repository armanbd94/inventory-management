<?php

namespace Modules\Purchase\Http\Controllers;

use Exception;
use App\Traits\UploadAble;
use Illuminate\Http\Request;
use Modules\System\Entities\Tax;
use Modules\System\Entities\Unit;
use Illuminate\Support\Facades\DB;
use Modules\Account\Entities\Account;
use Modules\Product\Entities\Product;
use Modules\System\Entities\Warehouse;
use Modules\Purchase\Entities\Purchase;
use Modules\Supplier\Entities\Supplier;
use Modules\Base\Http\Controllers\BaseController;
use Modules\Product\Entities\WarehouseProduct;
use Modules\Purchase\Entities\PurchaseProduct;
use Modules\Purchase\Http\Requests\PurchaseFormRequest;

class PurchaseController extends BaseController
{
    use UploadAble;

    public function __construct(Purchase $model)
    {
        $this->model = $model;
    }

    public function index()
    {
        if(permission('purchase-access')){
            $this->setPageData('Manage Purchase','Manage Purchase','fas fa-box');
            $suppliers=Supplier::all();
            $accounts = Account::where('status',1)->get();
            return view('purchase::index',compact('suppliers','accounts'));
        }else{
            return $this->unauthorized_access_blocked();
        }
    }

    public function get_datatable_data(Request $request)
    {
        if(permission('purchase-access')){
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
                if (!empty($request->purchase_status)) {
                    $this->model->setPurchaseStatus($request->purchase_status);
                }
                if (!empty($request->payment_status)) {
                    $this->model->setPaymentStatus($request->payment_status);
                }

                $this->set_datatable_default_property($request);
                $list = $this->model->getDatatableList();
    
                $data = [];
                $no = $request->input('start');
                foreach ($list as $value) {
                    $no++;
                    $action = '';
                    
                    if(permission('purchase-edit')){
                        $action .= ' <a class="dropdown-item edit_data" href="'.url("purchase/edit",$value->id).'"><i class="fas fa-edit text-primary"></i> Edit</a>';
                    }
                    if(permission('purchase-view')){
                        $action .= ' <a class="dropdown-item view_data" href="'.url("purchase/details",$value->id).'"><i class="fas fa-eye text-success"></i> View</a>';
                    }

                    if(permission('purchase-payment-add')){
                        if(($value->grand_total - $value->paid_amount) != 0){
                            $action .= ' <a class="dropdown-item add_payment"  data-id="' . $value->id . '" data-due="'.($value->grand_total - $value->paid_amount).'"><i class="fas fa-plus-square text-info"></i> Add Payment</a>';
                        }
                    }
                    if(permission('purchase-payment-view')){
                        $action .= ' <a class="dropdown-item view_payment_list"  data-id="' . $value->id . '"><i class="fas fa-file-invoice-dollar text-default"></i> Payment List</a>';
                    }
                    if(permission('purchase-delete')){
                        $action .= ' <a class="dropdown-item delete_data"  data-id="' . $value->id . '" data-name="' . $value->name . '"><i class="fas fa-trash text-danger"></i> Delete</a>';
                    }
                    
                    $row = [];
                    
                    if(permission('purchase-bulk-delete')){
                        $row[] = table_checkbox($value->id);
                    }
                    $row[] = $no;
                    $row[] = $value->purchase_no;
                    $row[] = $value->supplier->name.' - '.$value->supplier->phone;
                    $row[] = number_format($value->item,2,'.',',');
                    $row[] = number_format($value->total_qty,2,'.',',');
                    $row[] = number_format($value->total_discount,2,'.',',');
                    $row[] = number_format($value->total_tax,2,'.',',');
                    $row[] = number_format($value->total_cost,2,'.',',');
                    $row[] = number_format($value->order_tax_rate,2,'.',',');
                    $row[] = number_format($value->order_tax,2,'.',',');
                    $row[] = number_format($value->order_discount,2,'.',',');
                    $row[] = number_format($value->shipping_cost,2,'.',',');
                    $row[] = number_format($value->grand_total,2,'.',',');
                    $row[] = number_format($value->paid_amount,2,'.',',');
                    $row[] = number_format(($value->grand_total - $value->paid_amount),2,'.',',');
                    $row[] = PURCHASE_STATUS_LABEL[$value->purchase_status];
                    $row[] = PAYMENT_STATUS_LABEL[$value->payment_status];
                    $row[] = $value->created_by;
                    $row[] = date(config('settings.date_format'),strtotime($value->created_at));
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



    public function create()
    {
        if(permission('purchase-add')){
            $this->setPageData('Add Purchase','Add Purchase','fas fa-plus-square');
            $data = [
                'suppliers'  => Supplier::where('status',1)->get(),
                'warehouses' => Warehouse::where('status',1)->get(),
                'taxes'      => Tax::where('status',1)->get(),
            ];
            return view('purchase::create',$data);
        }else{
            return $this->unauthorized_access_blocked();
        }
    }

    public function store(PurchaseFormRequest $request)
    {
        // dd($request->all());
        if($request->ajax())
        {
            if(permission('purchase-add'))
            {
                DB::beginTransaction();
                try {
                    $purchase_data = [
                        'purchase_no'     => 'PINV-'.date('Ymd').'-'.date('His'),
                        'supplier_id'     => $request->supplier_id,
                        'warehouse_id'    => $request->warehouse_id,
                        'item'            => $request->item,
                        'total_qty'       => $request->total_qty,
                        'total_discount'  => $request->total_discount,
                        'total_tax'       => $request->total_tax,
                        'total_cost'      => $request->total_cost,
                        'order_tax_rate'  => $request->order_tax_rate,
                        'order_tax'       => $request->order_tax,
                        'order_discount'  => $request->order_discount,
                        'shipping_cost'   => $request->shipping_cost,
                        'grand_total'     => $request->grand_total,
                        'paid_amount'     => 0,
                        'purchase_status' => $request->purchase_status,
                        'payment_status'  => 2,
                        'note'            => $request->note,
                        'created_by'      => auth()->user()->name
                    ];
            
                    if($request->hasFile('document')){
                        $purchase_data['document'] = $this->upload_file($request->file('document'),PURCHASE_DOCUMENT_PATH);
                    }

                    $products = [];
                    if($request->has('products'))
                    {
                        foreach ($request->products as $key => $value) {
                            $unit = Unit::where('unit_name',$value['unit'])->first();
                            if($unit->operator == '*'){
                                $qty = $value['received'] * $unit->operation_value;
                            }else{
                                $qty = $value['received'] / $unit->operation_value;
                            }

                            $products[$value['id']] = [
                                'qty'           => $value['qty'],
                                'received'      => $value['received'],
                                'unit_id'       => $unit ? $unit->id : null,
                                'net_unit_cost' => $value['net_unit_cost'],
                                'discount'      => $value['discount'],
                                'tax_rate'      => $value['tax_rate'],
                                'tax'           => $value['tax'],
                                'total'         => $value['subtotal']
                            ];

                            $product = Product::find($value['id']);
                            $product->qty = $product->qty + $qty;
                            $product->save();

                            $warehouse_product = WarehouseProduct::where(['warehouse_id'=>$request->warehouse_id,'product_id'=>$value['id']])->first();
                            if($warehouse_product){
                                $warehouse_product->qty = $warehouse_product->qty + $qty;
                                $warehouse_product->save();
                            }else{
                                WarehouseProduct::create([
                                    'warehouse_id'=>$request->warehouse_id,
                                    'product_id'=>$value['id'],
                                    'qty' => $qty
                                ]);
                            }
                        }
                    }

                    $purchase = $this->model->create($purchase_data);
                    $purchaseData = Purchase::with('purchase_products')->find($purchase->id);
                    $purchaseData->purchase_products()->sync($products);
                    $output = $purchase ? ['status' => 'success','message' => 'Data saved successfully'] : ['status' => 'error','message' => 'Data failed to save'];
                    DB::commit();
                } catch (Exception $e) {
                    DB::rollBack();
                    $output = ['status'=>'error','message'=>$e->getMessage()];
                }
                return response()->json($output);
            }
        }else{
            return response()->json($this->access_blocked());
        }
    }

    public function edit(int $id)
    {
        if(permission('purchase-edit')){
            $this->setPageData('Edit Purchase','Edit Purchase','fas fa-edit');
            $data = [
                'purchase'   => $this->model->with('purchase_products')->findOrFail($id),
                'suppliers'  => Supplier::where('status',1)->get(),
                'warehouses' => Warehouse::where('status',1)->get(),
                'taxes'      => Tax::where('status',1)->get(),
            ];
            // dd($data['purchase']);
            return view('purchase::edit',$data);
        }else{
            return $this->unauthorized_access_blocked();
        }
    }

    public function update(PurchaseFormRequest $request)
    {
        if($request->ajax())
        {
            if(permission('purchase-edit'))
            {
                DB::beginTransaction();
                try {
                    $purchase_data = [
                        'supplier_id'     => $request->supplier_id,
                        'warehouse_id'    => $request->warehouse_id,
                        'item'            => $request->item,
                        'total_qty'       => $request->total_qty,
                        'total_discount'  => $request->total_discount,
                        'total_tax'       => $request->total_tax,
                        'total_cost'      => $request->total_cost,
                        'order_tax_rate'  => $request->order_tax_rate,
                        'order_tax'       => $request->order_tax,
                        'order_discount'  => $request->order_discount,
                        'shipping_cost'   => $request->shipping_cost,
                        'grand_total'     => $request->grand_total,
                        'purchase_status' => $request->purchase_status,
                        'payment_status'  => ($request->grand_total - $request->paid_amount) == 0 ? 1 : 2,
                        'note'            => $request->note,
                        'updated_by'      => auth()->user()->name
                    ];
            
                    if($request->hasFile('document')){
                        $purchase_data['document'] = $this->upload_file($request->file('document'),PURCHASE_DOCUMENT_PATH);
                    }

                    $purchaseData = Purchase::with('purchase_products')->find($request->purchase_id);
                    $old_document = $purchaseData ? $purchaseData->document : '';
                    if(!$purchaseData->purchase_products->isEmpty())
                    {
                        foreach ($purchaseData->purchase_products as  $purchase_product) {
                            $old_received_qty = $purchase_product->pivot->received;
                            $purchase_unit = Unit::find($purchase_product->pivot->unit_id);
                            if($purchase_unit->operator == '*'){
                                $old_received_qty = $old_received_qty * $purchase_unit->operation_value;
                            }else{
                                $old_received_qty = $old_received_qty / $purchase_unit->operation_value;
                            }
                            $product_data = Product::find($purchase_product->id);
                            $product_data->qty -= $old_received_qty;
                            $product_data->update();

                            $warehouse_product = WarehouseProduct::where([
                                'warehouse_id'=>$purchaseData->warehouse_id,
                                'product_id'=>$purchase_product->id])->first();
                            $warehouse_product->qty -= $old_received_qty;
                            $warehouse_product->update();
                        }
                    }

                    $products = [];
                    if($request->has('products'))
                    {
                        foreach ($request->products as $key => $value) {
                            $unit = Unit::where('unit_name',$value['unit'])->first();
                            if($unit->operator == '*'){
                                $qty = $value['received'] * $unit->operation_value;
                            }else{
                                $qty = $value['received'] / $unit->operation_value;
                            }

                            $products[$value['id']] = [
                                'qty'           => $value['qty'],
                                'received'      => $value['received'],
                                'unit_id'       => $unit ? $unit->id : null,
                                'net_unit_cost' => $value['net_unit_cost'],
                                'discount'      => $value['discount'],
                                'tax_rate'      => $value['tax_rate'],
                                'tax'           => $value['tax'],
                                'total'         => $value['subtotal']
                            ];

                            $product = Product::find($value['id']);
                            $product->qty = $product->qty + $qty;
                            $product->save();

                            $warehouse_product = WarehouseProduct::where(['warehouse_id'=>$request->warehouse_id,'product_id'=>$value['id']])->first();
                            if($warehouse_product){
                                $warehouse_product->qty = $warehouse_product->qty + $qty;
                                $warehouse_product->save();
                            }else{
                                WarehouseProduct::create([
                                    'warehouse_id'=>$request->warehouse_id,
                                    'product_id'=>$value['id'],
                                    'qty' => $qty
                                ]);
                            }
                        }
                    }

                    $purchase = $purchaseData->update($purchase_data);
                    if($purchase && $old_document != '')
                    {
                        $this->delete_file($old_document,PURCHASE_DOCUMENT_PATH);
                    }
                    $purchaseData->purchase_products()->sync($products);
                    $output = $purchase ? ['status' => 'success','message' => 'Data updated successfully'] : ['status' => 'error','message' => 'Data failed to update'];
                    DB::commit();
                } catch (Exception $e) {
                    DB::rollBack();
                    $output = ['status'=>'error','message'=>$e->getMessage()];
                }
                return response()->json($output);
            }
        }else{
            return response()->json($this->access_blocked());
        }
    }

    public function show(int $id)
    {
        if(permission('purchase-view')){
            $this->setPageData('Purchase Details','Purchase Details','fas fa-eye');
            $data = [
                'purchase'   => $this->model->with('purchase_products','supplier')->findOrFail($id),
            ];
            // dd($data['purchase']);
            return view('purchase::details',$data);
        }else{
            return $this->unauthorized_access_blocked();
        }
    }


    public function delete(Request $request)
    {
        if($request->ajax()){
            if(permission('purchase-delete')){
                DB::beginTransaction();
                try {
                    $purchaseData = Purchase::with('purchase_products','payments')->find($request->id);
                    if(!$purchaseData->purchase_products->isEmpty())
                    {
                        foreach ($purchaseData->purchase_products as  $purchase_product) {
                            $purchase_unit = Unit::find($purchase_product->pivot->unit_id);
                            if($purchase_unit->operator == '*'){
                                $received_qty = $purchase_product->pivot->received * $purchase_unit->operation_value;
                            }else{
                                $received_qty = $purchase_product->pivot->received / $purchase_unit->operation_value;
                            }
                            $product_data = Product::find($purchase_product->id);
                            $product_data->qty -= $received_qty;
                            $product_data->update();

                            $warehouse_product = WarehouseProduct::where([
                                'warehouse_id'=>$purchaseData->warehouse_id,
                                'product_id'=>$purchase_product->id])->first();
                            $warehouse_product->qty -= $received_qty;
                            $warehouse_product->update();
                        }
                    }
                    if(!$purchaseData->purchase_products->isEmpty()){
                        $purchaseData->purchase_products()->detach();
                    }
                    if(!$purchaseData->payments->isEMpty()){
                        $purchaseData->payments()->delete();
                    }
                    
                    $result = $purchaseData->delete();
                    $output = $result ? ['status' => 'success','message' => 'Data has been deleted successfully'] : ['status' => 'error','message' => 'failed to delete data'];
                    DB::commit();
                } catch (Exception $e) {
                    DB::rollBack();
                    $output = ['status'=>'error','message'=>$e->getMessage()];
                }
                return response()->json($output);
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
            if(permission('purchase-bulk-delete')){
                foreach ($request->ids as $id) {
                    DB::beginTransaction();
                    try {
                        $purchaseData = Purchase::with('purchase_products','payments')->find($id);
                        $old_document = $purchaseData ? $purchaseData->document : '';
                        if(!$purchaseData->purchase_products->isEmpty())
                        {
                            foreach ($purchaseData->purchase_products as  $purchase_product) {
                                $purchase_unit = Unit::find($purchase_product->pivot->unit_id);
                                if($purchase_unit->operator == '*'){
                                    $received_qty = $purchase_product->pivot->received * $purchase_unit->operation_value;
                                }else{
                                    $received_qty = $purchase_product->pivot->received / $purchase_unit->operation_value;
                                }
                                $product_data = Product::find($purchase_product->id);
                                $product_data->qty -= $received_qty;
                                $product_data->update();
    
                                $warehouse_product = WarehouseProduct::where([
                                    'warehouse_id'=>$purchaseData->warehouse_id,
                                    'product_id'=>$purchase_product->id])->first();
                                $warehouse_product->qty -= $received_qty;
                                $warehouse_product->update();
                            }
                        }
                        if(!$purchaseData->purchase_products->isEmpty()){
                            $purchaseData->purchase_products()->detach();
                        }
                        if(!$purchaseData->payments->isEMpty()){
                            $purchaseData->payments()->delete();
                        }
                        $result = $purchaseData->delete();
                        $output = $result ? ['status' => 'success','message' => 'Data has been deleted successfully'] : ['status' => 'error','message' => 'failed to delete data'];
                        DB::commit();
                    } catch (Exception $e) {
                        DB::rollBack();
                        $output = ['status'=>'error','message'=>$e->getMessage()];
                    }
                    return response()->json($output);
                }
            }else{
                $output = $this->access_blocked();
            }
            return response()->json($output);
        }else{
            return response()->json($this->access_blocked());
        }
    }


}
