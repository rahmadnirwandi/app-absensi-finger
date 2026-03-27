<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

use App\Services\GlobalService;
use PhpOffice\PhpSpreadsheet\Calculation\Web\Service;
use Log;

class AbsensiKaryawanController extends \App\Http\Controllers\MyAuthController
{

    public $part_view, $url_index, $url_name, $title, $breadcrumbs, $globalService;

    public function __construct()
    {
        $router_name = (new \App\Http\Traits\GlobalFunction)->getRouterIndex();
        $this->part_view = $router_name->path_base;
        $this->url_index = $router_name->uri;
        $this->url_name = $router_name->router_name;

        $this->title = 'Data Absensi';
        $this->breadcrumbs = [
            ['title' => 'Manajemen Absensi', 'url' => url('/') . "/sub-menu?type=5"],
            ['title' => $this->title, 'url' => url('/') . "/" . $this->url_index],
        ];

        $this->globalService = new GlobalService;
    }

    function actionIndex(Request $request){
        ini_set("memory_limit","800M");
        set_time_limit(0);

        $get_presensi_masuk=(new \App\Http\Traits\AbsensiFunction)->get_list_data_presensi(1);
        $get_presensi_istirahat=(new \App\Http\Traits\AbsensiFunction)->get_list_data_presensi(2);
        $get_presensi_pulang=(new \App\Http\Traits\AbsensiFunction)->get_list_data_presensi(4);

        $paramter_search=$request->all();
        
        $list_data_tmp=(new \App\Services\DataPresensiRutinService)->getDataRumus3($paramter_search);
        
        $list_data=!empty($list_data_tmp->list_data) ? $list_data_tmp->list_data : [];
        $hasil_data=json_encode($list_data);
        
        $parameter_view = [
            'title' => $this->title,
            'breadcrumbs' => $this->breadcrumbs,
            'get_presensi_masuk'=>$get_presensi_masuk,
            'get_presensi_istirahat'=>$get_presensi_istirahat,
            'get_presensi_pulang'=>$get_presensi_pulang,
            'hasil_data'=>$hasil_data,
        ];

        return view($this->part_view . '.index', $parameter_view);
    }

    function getListData(Request $request){
        ini_set("memory_limit","800M");
        set_time_limit(0);

        if($request->ajax()){
            $get_req = $request->all();
            $list_data=!empty($get_req['list_data']) ? $get_req['list_data'] : '';
            $page=!empty($get_req['page']) ? $get_req['page'] : 1;
            $list_data=!empty($list_data) ? json_decode($list_data) : [];
            $list_data=(array)$list_data;
            $list_data=array_values($list_data);
            $list_data = (new \App\Http\Traits\GlobalFunction)->paginate($list_data,15,$page);
            return view($this->part_view . '.columns_ajax', compact('list_data'))->render();
        }
    }

    function ajax(Request $request){
        ini_set("memory_limit","800M");
        set_time_limit(0);
        
        $get_req = $request->all();
        if (!empty($get_req['action'])) {
            if ($get_req['action'] == 'get_list_data') {
                return $this->getListData($request);
            }
        }
    }
}