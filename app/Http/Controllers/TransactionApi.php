<?php

namespace App\Http\Controllers;

use App\Models\Transaction_details;
use App\Models\Transactions;
use Dotenv\Parser\Value;
use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Carbon\Carbon;
class TransactionApi extends Controller
{
    //

    public function dashboardView(Request $request)
    { 
        return view('admin.dashboard.index');
    }

    public function getTransactionById(Request $request){
        $trans_number = Transactions::query()->where('id',$request->id)->first();

    }

    public function getTransactioDetailnByTaransNumber($id_number){
        $count = Transaction_details::query()->where('transaction_number',$id_number)->get();
        return $count;
        
    }

    public function DeletedTransaction(Request $request){
        $validated = Validator::make($request->all(),[
            'no_trans' => 'required|integer|exists:transactions,transaction_number',
        ]);
        
        if ($validated->fails()) {
            return $validated->errors();
        }
        $total_detail = $this->getTransactioDetailnByTaransNumber($request->no_trans)->count();
        DB::beginTransaction();
        try {
            if ($total_detail > 0) {
                Transaction_details::query()->where('transaction_number',$request->no_trans)->delete();
                Transactions::query()->where('transaction_number',$request->no_trans)->delete();
            }else{
                Transactions::query()->where('transaction_number',$request->no_trans)->delete();
            }
            DB::commit();
            return response()->json([
                "status"=>"ok",
                "msg"=> "success hapus"
            ],200);
        } catch (\Exception $e) {
            DB::rollBack();
            $hapus = "gagal hapus";
            return response()->json([
                "status"=>"error",
                "msg"=> $hapus
            ],400);
        }
    }

    public function AllDataTrans(Request $request){
        $resultDb = DB::select('
        SELECT transactions.id,transactions.transaction_number as "transaction_number" ,
        COUNT(transaction_details.item_name) as "total_item", SUM(transaction_details.quantity) as "total_qty"
        FROM `transaction_details` JOIN transactions ON transaction_details.transaction_number = transactions.transaction_number 
        GROUP BY transactions.transaction_number');

        $results = DB::table('transaction_details')
        ->rightJoin('transactions', 'transaction_details.transaction_number', '=', 'transactions.transaction_number')
        ->select(
            'transactions.id',
            'transactions.transaction_number as transaction_number',
            DB::raw('COUNT(transaction_details.item_name) as total_item'),
            DB::raw('SUM(transaction_details.quantity) as total_qty')
        )
        ->groupBy('transactions.id', 'transactions.transaction_number')->get();
                
        return response()->json([
            "status"=>"ok",
            "msg"=> "success",
            "data"=> $results
        ],200);
      
    }

    public function AddTransactionDetails(Request $request){
        $validated = Validator::make($request->all(),[
            'transaction_date' => 'required|date',
            'transaction_number' => 'required|integer|unique:transactions,transaction_number',
            'item_name.*' => 'nullable|string',
            'quantity.*' => 'nullable|integer'
        ]);

        if ($validated->fails()) {
            return $validated->errors();
        }

         DB::beginTransaction();
         try {
            $modelTrasn = new Transactions();

            $modelTrasn->transaction_number = $request->transaction_number;
            $modelTrasn->transaction_date = $request->transaction_date;
            $modelTrasn->save();
            $dataArray_detail = [];
            
            $model_detail = [
                'created_at' => Carbon::now()
            ];
            
           for ($i=0; $i < count($request->item_name) ; $i++) { 
                $model_detail['item_name'] =$request->item_name[$i];
                $model_detail['transaction_number'] =$modelTrasn->transaction_number;
                $model_detail["quantity"] = $request->quantity[$i];
                array_push($dataArray_detail,$model_detail);
           }
            
           Transaction_details::query()->insert($dataArray_detail);
            
           DB::commit();
           return response()->json([
            "status"=>"ok",
            "msg"=> "success",
            "transaction"=> $modelTrasn->fresh(),
            "transaction_detail"=> $dataArray_detail
        ],200);
        } catch (Exception $e) {
            DB::rollBack();
            return response()->json([
                "status"=>"no",
                "msg"=> "validated error",
                "data"=> "error",
                "error exp"=> $this->error($e->getMessage(), $e->getCode())
            ],404);
        }
       
    }

    public function kategoriView(Request $request)
    {
        return view('list');
    }
}
