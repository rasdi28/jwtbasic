<?php

namespace App\Http\Controllers;
use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;

class ProductController extends Controller 
{

    public function create(Request $request)
    {
        $this-> validate($request, [
            'name'=>'required|string',
            'description'=>'required|string',
            'stock'=>'required|integer',
            'price'=>'required|integer'
        ]);
          
        $data = $request->all();
        $product = Product::create($data);
        return response()->json([
            'message'=> 'Your data has beeen Successfuly',
            $product
        ]);

    }
}