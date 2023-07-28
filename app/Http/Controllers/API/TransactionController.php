<?php

namespace App\Http\Controllers\API;

use App\Models\Transaction;
use App\Models\TransactionItem;
use Illuminate\Http\Request;

use App\Helpers\ResponseFormatter;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;

class TransactionController extends Controller
{
    
    public function all(Request $request){

        $id = $request->input('id');
        $limit = $request->input('limit', 6);
        $status = $request->input('status');

        if($id){
            // mengambil relasi di model Transaction, yaitu items dan 
            $transaction = Transaction::with(['items.product'])->find($id);

            if($id){
                return ResponseFormatter::success(
                    $transaction,
                    'Data Transaksi Berhasil Diambil'
                );
            }else{
                return ResponseFormatter::error(
                    null,
                    'Data Transaksi tidak ada',
                    404
                ); 
            }
        }
        $transaction = Transaction::with(['items.product'])->where('users_id', Auth::user()->id);

        if($status){
            $transaction->where('status', $status);
        }

        return ResponseFormatter::success(

            $transaction->paginate($limit),
            'Data Transaksi berhasil diambil'
        );
    }

    public function checkout(Request $request){
        $request->validate([
            // validasi kiriman data array
            'items' => 'required|array',
            
            //didalam array 'items' terdapat object yang punya key 'id' yang kita validasi (exist:products,id mksdnya cek field id di table products) 
            'items.*.id' => 'exists:products,id',
            'total_price' => 'required',
            'shipping_price' => 'required',

            // in:PENDING,SUCCESS,CANCELLED,FAILED,SHIPPING,SHIPPED adalah mngecek memastikan agar inputan hanya berlaku untuk value yang di daftarkan tersebut
            'status' => 'required|in:PENDING,SUCCESS,CANCELLED,FAILED,SHIPPING,SHIPPED'

        ]);

        $transaction = Transaction::create([
            'users_id' => Auth::user()->id,
            'address' => $request->address,
            'total_price' => $request->total_price,
            'shipping_price' => $request->shipping_price,
            'status' => $request->status,
        ]);

        foreach($request->items as $product){
            TransactionItem::create([
                'users_id' => Auth::user()->id,
                'products_id' => $product['id'],
                'transactions_id' => $transaction->id,
                'quantity'=> $product['quantity']
            ]);
        }

        return ResponseFormatter::success(
            // karena diatas baru submit, maka data terbaru belum terupdate di variable $transaction, maka dari itu gunakan fungsi load(relasinya)
            $transaction->load('items.product'), 'Transaksi Berhasil'
        );
    }
}
