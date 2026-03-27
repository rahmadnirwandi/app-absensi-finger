<?php

use Illuminate\Support\Facades\Route;
use App\Classes\ListRoutes;
/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

Route::group(['middleware' => 'auth'], function () {
    $data = (new ListRoutes)->getDataAuth();
    foreach ($data as $key_list => $value_list) {
        if (!empty($value_list['item'])) {
            $item = $value_list['item'];

            foreach ($item as $key => $value) {
                $value = (object)$value;
                if (!empty($value->method)) {                    
                    if(is_array($value->method)){
                        $hasil = Route::match($value->method,$value->url, $value->controller);
                    }else{
                        if (strtoupper($value->method) == strtoupper('get')) {
                            $hasil = Route::get($value->url, $value->controller);
                        }

                        if (strtoupper($value->method) == strtoupper('head')) {
                            $hasil = Route::head($value->url, $value->controller);
                        }

                        if (strtoupper($value->method) == strtoupper('post')) {
                            $hasil = Route::post($value->url, $value->controller);
                        }

                        if (strtoupper($value->method) == strtoupper('delete')) {
                            $hasil = Route::delete($value->url, $value->controller);
                        }

                        if (strtoupper($value->method) == strtoupper('put')) {
                            $hasil = Route::put($value->url, $value->controller);
                        }
                    }

                    if (!empty($hasil)) {
                        if (!empty($value->name)) {
                            $hasil->name($value->name);
                        }else{
                            $pos = strpos($value->url, '/');
                            $generate_name='';
                            if ($pos !== false) {
                                $generate_name = substr_replace($value->url, '', $pos, strlen('/'));
                            }
                            $generate_name=str_replace('-','_',$generate_name);
                            $hasil->name($generate_name);
                        }

                        if (!empty($value->middleware)) {
                            $hasil->middleware($value->middleware);
                        }
                    }
                }
            }    
        }
    }
});

Route::get('/login', 'AuthController@index')->name('login');
Route::post('/process-login', 'AuthController@login');