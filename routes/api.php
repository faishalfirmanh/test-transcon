<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\TransactionApi;
/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

Route::middleware('auth:api')->get('/user', function (Request $request) {
    return $request->user();
});

Route::post('addtrans',[TransactionApi::class,'AddTransactionDetails'])->name('addtrans');
Route::post('deleted_trans',[TransactionApi::class,'DeletedTransaction'])->name('deleted_trans');
Route::get('all-trans',[TransactionApi::class,'AllDataTrans'])->name('all-trans');
