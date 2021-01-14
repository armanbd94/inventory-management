<?php

namespace Modules\Purchase\Http\Controllers;

use Exception;
use App\Traits\UploadAble;
use Illuminate\Http\Request;
use Modules\System\Entities\Tax;
use Modules\System\Entities\Unit;
use Illuminate\Support\Facades\DB;
use Modules\Product\Entities\Product;
use Modules\System\Entities\Warehouse;
use Modules\Purchase\Entities\Purchase;
use Modules\Supplier\Entities\Supplier;
use Modules\Base\Http\Controllers\BaseController;
use Modules\Product\Entities\WarehouseProduct;
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
            return view('purchase::index');
        }else{
            return $this->unauthorized_access_blocked();
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
}
