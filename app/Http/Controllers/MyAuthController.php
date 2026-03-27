<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

class MyAuthController extends \Illuminate\Routing\Controller
{
    public $tes='';
    public function callAction($method, $parameters)
    {
        $this_url=Route::getCurrentRoute()->uri;
        $this_url=( substr($this_url,0,1) =='/') ? $this_url : '/'.$this_url;
        
        $return_akses=0;

        $get_ignore_url=(new \App\Models\UserManagement\UxuiAuthRoutes)->getIgnoreRoutes();
        if(in_array($this_url,$get_ignore_url)){
            $return_akses=1;
        }

        if($return_akses==0){
            $jml_ktm=0;
            $get_ignore_sub_url=(new \App\Models\UserManagement\UxuiAuthRoutes)->getIgnoreSubRoutes();
            $this_url_tmp=$this_url;
            $this_url_tmp=substr( $this_url_tmp,1,strlen($this_url_tmp) );
            $this_url_tmp=explode('/',$this_url_tmp);
            foreach($this_url_tmp as $value){
                if(in_array($value,$get_ignore_sub_url)){
                    $jml_ktm++;
                }
            }
            if($jml_ktm>=1){
                $return_akses=1;
            }
        }

        if($return_akses==0){
            $get_user=(new \App\Http\Traits\AuthFunction)->getUser();
            if(in_array($this_url,$get_user->auth_user)){
                $return_akses=1;
            }   
        }

        // if($return_akses==0){
        //     $ignore_type=['ajax'];
        //     $type=\App\Models\UserManagement\UxuiAuthRoutes::select('type')->where('url','=',"/".$this_url)->first();
        //     $type=!empty($type->type) ? $type->type : '';
        //     if(in_array($type,$ignore_type)){
        //         $return_akses=1;
        //     }
        // }

        if($return_akses==1){
            return parent::callAction($method, $parameters);
        }else{
            return redirect('/')->with(['error' => 'Akses di tolak']);
        }        
    }

    public function clear_request($params,$request){
        if($request->all()){
            foreach ($request->all() as $key => $value){
                if(array_key_exists($key,$params)){
                    unset($params[$key]);
                }
            }
        }
        
        return $params;
    }
}
