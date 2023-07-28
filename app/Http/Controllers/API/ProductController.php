<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\Product;
use App\Helpers\ResponseFormatter;
use Illuminate\Http\Request;

class ProductController extends Controller
{
    public function all(Request $request){
        $id = $request->input('id');
        $limit = $request->input('limit');
        $name = $request->input('name');
        $description = $request->input('description');
        $tags = $request->input('tags');
        $categories = $request->input('categories');
        $price_from = $request->input('price_from');
        $price_to = $request->input('price_to');

        if($id){

            // mengambil model product beserta relasi nya(galerries, category) dan menjalankan fungsi find/cari
            $product = Product::with(['category','galleries'])->find($id);

            if($product){
                return ResponseFormatter::success(
                $product,
                'data produk berhasil di ambil'
                );
            }else{
                return ResponseFormatter::success(
                null,
                'data produk tidak ada',
                404
                );                
            }

        }

        // pencarian berdasarkan request dibawah dengan like
        $product = Product::with(['category','galleries']);

        if($name){
            $product->where('name', 'like', '%'.$name.'%');
        }
        if($description){
            $product->where('descriptio$description', 'like', '%'.$description.'%');
        }
        if($tags){
            $product->where('tags', 'like', '%'.$tags.'%');
        }
        if($price_from){
            $product->where('price', '>=', $price_from);
        }
        if($price_to){
            $product->where('price', '<=', $price_to);
        }
        if($categories){
            $product->where('price',  $price_to);
        }

        return ResponseFormatter::success(
            $product->paginate($limit),
            'data produk berhasil di ambil'
            );
    }
}
