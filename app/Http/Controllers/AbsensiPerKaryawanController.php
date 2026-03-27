<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

use App\Services\GlobalService;
use App\Services\DataAbsensiKaryawanService;

class AbsensiPerKaryawanController extends \App\Http\Controllers\MyAuthController
{

    public $part_view, $url_index, $url_name, $title, $breadcrumbs, $globalService;
    public $dataAbsensiKaryawanService;

    public function __construct()
    {
        $router_name = (new \App\Http\Traits\GlobalFunction)->getRouterIndex();
        $this->part_view = $router_name->path_base;
        $this->url_index = $router_name->uri;
        $this->url_name = $router_name->router_name;

        $this->title = 'Data Absensi';
        $this->breadcrumbs = [
            ['title' => $this->title, 'url' => url('/') . "/" . $this->url_index],
        ];

        $this->globalService = new GlobalService;
        $this->dataAbsensiKaryawanService = new DataAbsensiKaryawanService;
    }

    function actionIndex(Request $request){
    
        ini_set("memory_limit","800M");
        set_time_limit(0);

        $get_user=(new \App\Http\Traits\AuthFunction)->getUser();
        
        $list_data=[];
        if(!empty($get_user->data_user_sistem)){
            if(!empty($get_user->data_user_sistem->id_karyawan)){
                $list_data=$this->dataAbsensiKaryawanService->get_data_by_jadwal_rutin($request);
            }
        }

        $page = isset($request->page) ? $request->page : 1;
        $option=['path' => $request->url(), 'query' => $request->query()];
        $list_data = (new \App\Http\Traits\GlobalFunction)->paginate($list_data,5,$page,$option);

        $parameter_view = [
            'title' => $this->title,
            'breadcrumbs' => $this->breadcrumbs,
            'list_data'=>$list_data
        ];

        return view($this->part_view . '.index', $parameter_view);
    }
}