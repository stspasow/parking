<?php

use Illuminate\Http\Request;

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


Route::get('/vehicle/types', 'VehicleController@getVehicleTypes');
Route::get('/vehicle/{platenumber}/currentFee', 'VehicleController@getCurrentFee');
Route::post('/vehicle/register', 'VehicleController@store');
Route::delete('/vehicle/{platenumber}', 'VehicleController@destroy');

Route::get('/parking/freespaces', 'ParkingController@getFreeSpaces');
Route::get('/parking/discounts', 'ParkingController@getDiscounts');