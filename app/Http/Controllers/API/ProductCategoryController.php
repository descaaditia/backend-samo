<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\ProductCategory;
use App\Helpers\ResponseFormatter;
use Illuminate\Http\Request;

class ProductCategoryController extends Controller
{
   public function all(Request $request){
    $id = $request->input('id');
    $limit = $request->input('limit');
    $name = $request->input('name');
    $show_product = $request->input('show_product');

    if($id){

        // mengambil model product beserta relasi nya(galerries, category) dan menjalankan fungsi find/cari
        $category = ProductCategory::with(['products'])->find($id);

        if($category){
            return ResponseFormatter::success(
            $category,
            'data category berhasil di ambil'
            );
        }else{
            return ResponseFormatter::success(
            null,
            'data category tidak ada',
            404
            );                
        }

    }

    $category = ProductCategory::query();

    if($name){
        $category->where('name', 'like', '%'.$name.'%');
    }
    if($show_product){
        $category->with('products');
    }
    return ResponseFormatter::success(
        $category->paginate($limit),
        'data category berhasil di ambil'
        );
   }
}
