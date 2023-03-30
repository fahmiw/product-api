<?php

namespace App\Http\Controllers;

use App\Models\Carts;
use App\Models\Products;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

use Exception;

class CartsController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function list(Carts $carts)
    {
        try {
            $items = array();
            $list = array();
            $result = $carts->where('is_checkout', '=', false)
                            ->get();    
            foreach($result as $key => $value1){
                $productList = json_decode($value1->product_list);
                foreach($productList as $value) {
                    $items = $value;
                    array_push($list, $items);
                }
                $value1->product_list = $list;
                $items = [];
                $list = [];
            }
            
            return response()->json([
                'data'    => $result == NULL ? 'Cart Empty' : $result
            ], 200);

        } catch (Exception $e) {
            return response()->json([
                'message' => $e->getMessage()
            ], 409);
        }
    }

    public function create(Request $request, Carts $carts, Products $products)
    {
        try {
            $total = 0;
            $validated = Validator::make($request->all(), [
                'product_list' => 'required|array'
            ]);

            if ($validated->fails()) {
                return response()->json([
                    'message' => $validated->errors()
                ], 400);
            }

            foreach($request->product_list as $value) {
                $dataProduct = $products->where('sku', '=', $value['sku'])
                                        ->get();
                if($dataProduct[0]->qty != 0){
                    $dataProduct[0]->qty = $dataProduct[0]->qty - $value['qty'];
                    $dataProduct[0]->save();

                    $total += $dataProduct[0]->price * $value['qty'];
                }
            }

            $jsonProduct = json_encode($request->product_list);

            $dataAdded = $carts->create([
                'product_list' => $jsonProduct,
                'total'   => $total,
                'is_checkout' => false
            ]);

            return response()->json([
                'message' => 'Product Added to Cart!',
                'data'    => $dataAdded
            ], 201);

        } catch (Exception $e) {
            return response()->json([
                'message' => $e->getMessage()
            ], 409);
        }
    }

}
