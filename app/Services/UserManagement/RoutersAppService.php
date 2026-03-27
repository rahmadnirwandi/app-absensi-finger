<?php

namespace App\Services\UserManagement;

use Illuminate\Support\Facades\DB;

use App\Models\UserManagement\UxuiAuthRoutes;

class RoutersAppService extends \App\Services\BaseService
{
    public $uxuiAuthRoutes;
    
    public function __construct(){
        $this->uxuiAuthRoutes = new UxuiAuthRoutes;
    }

    function insertRoutes($fields)
    {
        foreach ($fields as $key => $field) {
            $field == null ? $fields[$key] = "" : "";
        }
        return $this->uxuiAuthRoutes->insert($fields);
    }

    function generateRoutes(){
        $pesan = DB::transaction(function (){
            try {

                $ignore_url=$this->uxuiAuthRoutes->getIgnoreRoutes();
                $list_routes=(new \App\Classes\ListRoutes)->getDataAuth();
                $jlh_list=0;
                $jlh_save=0;

                $check_permission=$this->uxuiAuthRoutes->all();
                if(!empty($check_permission)){
                    DB::statement("SET foreign_key_checks=0");
                    $model=$this->uxuiAuthRoutes::truncate();
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

                                    $model_save=$this->insertRoutes($data_save);
                                    if($model_save){
                                        $jlh_save++;
                                    }
                                }
                            }
                        }
                    }
                }

                if($jlh_list==$jlh_save){
                    DB::commit();
                    $pesan=['success','berhasil generate',1];
                }else{
                    DB::rollBack();
                    $pesan=['error','generate router gagal',2];
                }

            } catch(\Illuminate\Database\QueryException $e){
                DB::rollBack();
                $pesan=['error','generate router error query',2];
            } catch (\Throwable $e) {
                DB::rollBack();
                $pesan=['error','error coding',2];
            }

            return $pesan;
        });

        return $pesan;
    }
}