<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Products;
use Carbon\Carbon;
use Illuminate\Support\Facades\Validator;

use Storage;
use Image;
use Exception;

class ProductsController extends Controller
{   
    public function store(Request $request, Products $products) {
        try {
            $validated = Validator::make($request->all(), [
                'sku'    => 'required',
                'name'   => 'required',
                'product_image' => 'image|nullable',
                'description' => 'required|max:255',
                'price' => 'required|numeric',
                'qty' => 'required|numeric',
            ]);

            if ($validated->fails()) {
                return response()->json([
                    'message' => $validated->errors()
                ], 400);
            }

            if (isset($request->product_image)) {
                $file       = $request->file('product_image');
                $filename   = 'product_' . time() . '.' .$file->getClientOriginalExtension();
                $path       = '/app/public/assets/' . $filename;

                if (!Storage::disk('public')->exists('assets'))
                {  
                    Storage::disk('public')->makeDirectory('assets');
                }

                $imageFit  = Image::make($file)->fit(250, 250);
                $imageFit->save(storage_path($path));
            }

            $dataAdded = $products->create([
                'sku'    => $request->sku,
                'name'   => $request->name,
                'product_image' => isset($filename) ? $filename : NULL,
                'description' => $request->description,
                'price' => $request->price,
                'qty' => $request->qty
            ]);

            return response()->json([
                'message' => 'Product Created!',
                'data'    => $dataAdded
            ], 201);

        } catch (Exception $e) {
            return response()->json([
                'message' => $e->getMessage()
            ], 409);
        }
    }

    public function list(Products $products) {
        try {
            $result = $products->all();

            return response()->json([
                'data'    => $result
            ], 200);
        } catch (Exception $e) {
            return response()->json([
                'message' => $e->getMessage()
            ], 409);
        }
    }

    public function detail(Request $request, Products $products) {
        try {
            $id = $request->route('id');
            $result = $products->find($id);

            if($result == NULL) {
                return response()->json([
                    'message'    => 'ID Product Not Found'
                ], 404);
            }

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
