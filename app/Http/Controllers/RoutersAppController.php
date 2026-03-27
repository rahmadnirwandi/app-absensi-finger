<?php
namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Route;

use App\Classes\ListRoutes;
use App\Services\UserManagement\RoutersAppService;

class RoutersAppController extends Controller
{
    public $routersAppService;
    
    public function __construct() {
        $this->routersAppService = new RoutersAppService;
    }

    function actionIndex(Request $request)
    {
        $parameter_view=[];
        return view('user-management.index_routes',$parameter_view);
    }

    function actionGenerate(Request $request)
    {   
        DB::beginTransaction();
        $link_back='routers_app';
        $link_back_param=[];
        
        $message_default=[
            'success'=>'Data berhasil diubah',
            'error'=>'Maaf data tidak berhasil diubah'
        ];

        try {
            $ignore_url=$this->routersAppService->uxuiAuthRoutes->getIgnoreRoutes();

            $list_routes=(new ListRoutes)->getDataAuth();
            $jlh_list=0;
            $jlh_save=0;

            $check_permission=$this->routersAppService->uxuiAuthRoutes->all();
            if(!empty($check_permission)){
                DB::statement("SET foreign_key_checks=0");
                $model=$this->routersAppService->uxuiAuthRoutes::truncate();
                DB::statement("SET foreign_key_checks=1");
            }

            foreach($list_routes as $key_list => $value_list){
                if(!empty($value_list['item'])){
                    $item=$value_list['item'];
                    
                    foreach($item as $key => $value){
                        $value=(object)$value;

                        if(!in_array($value->url,$ignore_url)){
                            if(!empty($value->method)){
                                $jlh_list++;
                                $data_save=[
                                    'title'=>trim($value_list['title']),
                                    'url'=>trim($value->url),
                                    'controller'=>trim($value->controller),
                                    'type'=>trim($value->type),
                                ];

                                $model_save=$this->routersAppService->insertRoutes($data_save);
                                if($model_save){
                                    $jlh_save++;
                                }
                            }
                        }
                    }
                }
            }

            // ambil data dari routes
            // $list_routes = Route::getRoutes()->getRoutesByName();
            
            if($jlh_list==$jlh_save){
                DB::commit();
                $pesan=['success' => $message_default['success']];
            }else{
                DB::rollBack();
                $pesan=['error' => $message_default['error']];
            }
        } catch(\Illuminate\Database\QueryException $e){
            DB::rollBack();
            if($e->errorInfo[1] == '1062'){
                $pesan=['error' => $message_default['Maaf tidak dapat menyimpan data yang sama']];
            }
            $pesan=['error' => $message_default['error']];

        } catch (\Throwable $e) {
            DB::rollBack();
            $pesan=['error' => $message_default['error']];
        }

        return redirect()->route($link_back, $link_back_param)->with($pesan);


    }

}