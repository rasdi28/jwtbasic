<?php

namespace App\Http\Controllers;
use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;

class ProductController extends Controller 
{

    //create
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

    //read 

    public function index()
    {
        $product = Product::all();
        return response()->json($product);
    }

    public function destroy()
    {
        
    }
}