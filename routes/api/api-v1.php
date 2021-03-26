<?php

use App\Http\Controllers\Api\V1\Customer\Controllers\CustomerController;
use App\Http\Controllers\Api\V1\NAB\Controllers\NabController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

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

// Customer
Route::post('user/add', [CustomerController::class, 'store']);
Route::get('ib/member', [CustomerController::class, 'list']);
Route::post('ib/topup', [CustomerController::class, 'topup']);
Route::post('ib/withdraw', [CustomerController::class, 'withdraw']);

// Nab
Route::post('ib/updateTotalBalance', [NabController::class, 'updateBalance']);
Route::get('ib/listNAB', [NabController::class, 'list']);