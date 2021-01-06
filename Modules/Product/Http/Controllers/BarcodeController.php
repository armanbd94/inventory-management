<?php

namespace Modules\Product\Http\Controllers;

use Illuminate\Http\Request;
use Modules\Product\Entities\Product;
use Modules\Base\Http\Controllers\BaseController;
use Modules\Product\Http\Requests\BarcodeFormRequest;

class BarcodeController extends BaseController
{
    public function __construct(Product $model)
    {
        $this->model = $model;
    }

    public function index()
    {
        if(permission('print-barcode-access')){
            $this->setPageData('Print Barcode','Print Barcode','fas fa-barcode');
            $products = $this->model->all();
            return view('product::barcode.index',compact('products'));
        }else{
            return $this->unauthorized_access_blocked();
        }
    }

    
    public function generateBarcode(BarcodeFormRequest $request)
    {
        if($request->ajax())
        {
            $data = [
                'name'        => $request->name,
                'code'        => $request->code,
                'barcode_symbology'=>$request->barcode_symbology,
                'price'       => $request->price ? number_format($request->price,2) : '0.00',
                'barcode_qty' => $request->barcode_qty,
                'row_qty'     => $request->row_qty,
                'width'       => $request->width,
                'height'      => $request->height,
                'unit'        => $request->unit,
            ];
            return view('product::barcode.barcode',$data)->render();
        }
    }

    
}
