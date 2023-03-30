<?php

namespace App\Http\Controllers;

use App\Models\Orders;
use App\Models\Carts;
use App\Models\Products;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

use Exception;
use Storage;

class OrdersController extends Controller
{
    public function list(Orders $orders)
    {
        try {
            $result = $orders->all();
            unset($result[0]->order_items);
            unset($result[0]->order_items);
            
            return response()->json([
                'data'    => $result
            ], 200);
        } catch (Exception $e) {
            return response()->json([
                'message' => $e->getMessage()
            ], 409);
        }
    }

    public function checkout(Request $request, Orders $orders, Carts $carts)
    {
        try {
            $validated = Validator::make($request->all(), [
                'id_cart'    => 'required',
                'fullname'   => 'required',
                'address' => 'required|max:255',
                'payment_method' => 'required'
            ]);

            if ($validated->fails()) {
                return response()->json([
                    'message' => $validated->errors()
                ], 400);
            }
            $id = $request->id_cart;
            $cartData = $carts->find($id);

            if($cartData == NULL) {
                return response()->json([
                    'message'    => 'ID Cart Not Found'
                ], 404);
            }

            $dataAdded = $orders->create([
                'fullname' => $request->fullname,
                'address' => $request->address,
                'order_items' => $cartData->product_list,
                'status' => 'Processing',
                'is_paid' => false,
                'grand_total' => $cartData->total,
                'payment_method' => $request->payment_method
            ]);

            if($dataAdded) {
                $cartData->is_checkout = true;
                $cartData->save();
            }

            return response()->json([
                'message' => 'Order Checkout Success!',
                'data'    => $dataAdded
            ], 201);

        } catch (Exception $e) {
            return response()->json([
                'message' => $e->getMessage()
            ], 409);
        }
    }

    public function detail(Request $request, Orders $orders, Products $products) {
        try {
            $items = array();
            $list = array();
            $id = $request->route('id');
            $result = $orders->find($id);

            if($result == NULL) {
                return response()->json([
                    'message'    => 'ID Order Not Found'
                ], 404);
            }
            
            $productList = json_decode($result->order_items);
            foreach($productList as $value) {
                $dataProduct = $products->where('sku', '=', $value->sku)
                                        ->get();
                $value->name = $dataProduct[0]->name;
                $value->product_image = asset('storage/assets/'. $dataProduct[0]->product_image);
                $value->price = $dataProduct[0]->price;
                $items = $value;
                array_push($list, $items);
            }
            $result->order_items = $list;

            return response()->json([
                'data'    => $result
            ], 200);


        } catch (Exception $e) {
            return response()->json([
                'message' => $e->getMessage()
            ], 409);
        }
    }

}
