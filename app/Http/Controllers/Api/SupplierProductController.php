<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Supplier;
use Illuminate\Http\Request;

class SupplierProductController extends Controller
{
    public function getSupplierProducts($supplierId)
    {
        $supplier = Supplier::with('supplierProducts.product')->find($supplierId);
        return response()->json($supplier->supplierProducts ?? []);
    }
}