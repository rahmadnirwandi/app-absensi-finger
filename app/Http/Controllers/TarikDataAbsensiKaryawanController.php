<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

use App\Services\GlobalService;
use App\Services\UserMesinTmpService;
use App\Services\RefDataAbsensiTmpService;

class TarikDataAbsensiKaryawanController extends \App\Http\Controllers\MyAuthController
{

    public $part_view, $url_index, $url_name, $title, $breadcrumbs, $globalService;
    public $userMesinTmpService,$refDataAbsensiTmpService;

    public function __construct()
    {
        $router_name = (new \App\Http\Traits\GlobalFunction)->getRouterIndex();
        $this->part_view = $router_name->path_base;
        $this->url_index = $router_name->uri;
        $this->url_name = $router_name->router_name;

        $this->title = 'Tarik Log Data Absensi';
        $this->breadcrumbs = [
            ['title' => 'Mesin Absensi', 'url' => url('/') . "/sub-menu?type=5"],
            ['title' => $this->title, 'url' => url('/') . "/" . $this->url_index],
        ];

        $this->globalService = new GlobalService;
        $this->userMesinTmpService = new UserMesinTmpService;
        $this->refDataAbsensiTmpService = new RefDataAbsensiTmpService;
    }

    function actionIndex(Request $request){

        $tanggal_filter_start=!empty($request->tanggal_filter_start) ? $request->tanggal_filter_start : date('Y-m-d');
        $tanggal_filter_end=!empty($request->tanggal_filter_end) ? $request->tanggal_filter_end : date('Y-m-d');

        $parameter_view = [
            'title' => $this->title,
            'breadcrumbs' => $this->breadcrumbs,
            'tanggal_filter_start'=>$tanggal_filter_start,
            'tanggal_filter_end'=>$tanggal_filter_end
        ];

        return view($this->part_view . '.index', $parameter_view);
    }

    private function get_data_mesin(){
        $list_data_tmp=(new \App\Models\RefMesinAbsensi)->where(['status_mesin'=>1])->orderByRaw("CONVERT ( REPLACE ( ip_address, '.', '' ), UNSIGNED INTEGER )",'ASC')->get();

        $list_data=[];
        foreach($list_data_tmp as $value){
            $data_sent=$value->getAttributes();

            $model_mesin_tujuan=(new \App\Services\MesinFinger($value->ip_address));
            $connect_tujuan=$model_mesin_tujuan->connect();
            $connect_tujuan=!empty($connect_tujuan[2]) ? $connect_tujuan[2] : '';
            if($connect_tujuan==2){
                $data_sent['status_text']='Tidak Connect';
                $data_sent['status']=0;
            }
            $data_sent['status_text']='Connect';
            $data_sent['status']=1;
            $data_sent=(object)$data_sent;
            $list_data[]=$data_sent;
        }

        return $list_data;
    }

    private function sent_error($message=''){
        $return=[
            'success' => false,
            'message'=>$message,
            'hasil'=>504,
        ];
        return response()->json($return);
    }

    private function tarik_data_mesin($params){
        ini_set("memory_limit","800M");
        set_time_limit(0);

        $proses_selesai=0;
        $hasil=504;
        $end_proses=1;
        $end_proses=$end_proses+1;
        $calcu=floor(100/$end_proses);
        $progres_bar=0;
        $exs_query=100;

        try {
            $urut_proses=!empty($params['urut_proses']) ? $params['urut_proses'] : 0;
            $id_mesin=!empty($params['key']) ? $params['key'] : 0;
            $tanggal_first=!empty($params['tanggal_first']) ? $params['tanggal_first'] : date('Y-m-d');
            $tanggal_proses_start=!empty($params['tanggal_proses_start']) ? $params['tanggal_proses_start'] : date('Y-m-d');
            $tanggal_max=!empty($params['tanggal_max']) ? $params['tanggal_max'] : date('Y-m-d');
            $start_query=!empty($params['start_query']) ? $params['start_query'] : 0;
            $end_query=!empty($params['end_query']) ? $params['end_query'] : $exs_query;

            $tanggal_proses_tmp=$tanggal_proses_start;
            $tgl1 = new \DateTime($tanggal_first);
            $tgl2 = new \DateTime($tanggal_max);
            $get_diff = $tgl2->diff($tgl1);
            $get_diff=$get_diff->d;
            $calcu=100;

            if(!empty($get_diff)){
                $calcu=floor(100/$get_diff);
            }
            
            if($urut_proses==1){
                $data_mesin=(new \App\Models\RefMesinAbsensi)->where(['id_mesin_absensi'=>$id_mesin])->first();

                if(empty($data_mesin)){
                    return $this->sent_error('proses'.$urut_proses.' 1');
                    die;
                }

                $mesin=(new \App\Services\MesinFinger($data_mesin->ip_address));
                
                // $get_data_log=$mesin->get_log_data_absensi();
                $paramter=[
                    'tgl_start'=>$tanggal_proses_start,
                    'tgl_end'=>$tanggal_proses_start,
                ];
                $get_data_log=$mesin->get_log_data_absensi_tad($paramter);

                $check_hasil=!empty($get_data_log[0]) ? $get_data_log[0] : '';
                $proses_gagal=0;

                if($check_hasil=='error'){
                    $proses_gagal++;
                    $message=$get_data_log[1];
                }else{
                    DB::beginTransaction();
                    $data = $get_data_log[1] ?? [];

                    $data = isset($data['PIN']) ? [$data] : $data;

                    if (empty($data)) {
                        $proses_gagal++;
                    } else {

                        $jml_save = 0;
                        $data_save = [];

                        foreach ($data as $value_item) {
                            $value_item = (object) $value_item;

                            $data_save[] = [
                                'id_mesin_absensi' => $id_mesin,
                                'id_user' => $value_item->PIN,
                                'waktu' => $value_item->DateTime,
                                'verified' => $value_item->Verified,
                                'status' => $value_item->Status,
                            ];
                        }

                        if (!empty($data_save)) {
                            $model_save = new \App\Models\RefDataAbsensiTmp;

                            if ($model_save::insertOrIgnore($data_save)) {
                                DB::commit();
                            } else {
                                DB::rollBack();
                            }
                        }
                    }
                }

                $lanjut_proses=0;
                $tanggal_proses_start = date('Y-m-d', strtotime('+1 days', strtotime($tanggal_proses_start))); 
                if(strtotime($tanggal_max)>=strtotime($tanggal_proses_start)){
                    $lanjut_proses=1;
                }

                $tgl1 = new \DateTime($tanggal_proses_start);
                $tgl2 = new \DateTime($tanggal_first);
                $get_diff = $tgl2->diff($tgl1);
                $get_diff=$get_diff->d;

                if($proses_gagal){
                    $status_mesin=1;
                    $urut_proses=$end_proses;
                    if(!empty($lanjut_proses)){
                        $status_mesin=0;
                        $urut_proses=1;
                        $progres_bar=$calcu*$get_diff;
                    }
                }else{
                    $status_mesin=0;
                    $message='';
                    $progres_bar=$calcu*$urut_proses;
                    $urut_proses_succ=$urut_proses+1;
                    $urut_proses_tmp=$urut_proses_succ;
                    $urut_proses=$urut_proses_tmp;
                    if(!empty($lanjut_proses)){
                        $urut_proses=1;
                    }
                    $progres_bar=$calcu*$get_diff;
                }

                $hasil=200;
                $start_query=0;
                $end_query=$exs_query;
            }

            if($urut_proses==$end_proses){
                $progres_bar=100;
                $urut_proses=$end_proses;
                $hasil=200;
                $proses_selesai=1;
            }

        } catch (\Illuminate\Database\QueryException $e) {
            return $this->sent_error('proses'.$urut_proses.' 5');
            die;
        } catch (\Throwable $e) {
            return $this->sent_error('proses'.$urut_proses.' 6');
            die;
        }

        return [
            'hasil'=>$hasil,
            'proses_selesai'=>$proses_selesai,
            'no_proses'=>$urut_proses,
            'progres_bar'=>$progres_bar,
            'start_query'=>$start_query,
            'end_query'=>$end_query,
            'message'=>!empty($message) ? $message : '',
            'status_mesin'=>!empty($status_mesin) ? $status_mesin : '',
            'id_mesin'=>$id_mesin,
            'tanggal_proses_start'=>!empty($tanggal_proses_start) ? $tanggal_proses_start : '',
            'tanggal_proses_tmp'=>!empty($tanggal_proses_tmp) ? $tanggal_proses_tmp : '',
        ];
    }

    function ajax(Request $request){

        $get_req = $request->all();

        if(!empty($get_req['action'])){
            if($get_req['action']=='get_data_mesin'){
                $list_data=$this->get_data_mesin();

                $parameter=[
                    'list_data'=>$list_data
                ];

                if($request->ajax()){
                    $returnHTML = view($this->part_view . '.get_data_mesin', $parameter)->render();
                    return response()->json(array('success' => true, 'html'=>$returnHTML));
                }
            }
        }

        if(!empty($get_req['action'])){
            if($get_req['action']=='get_data_log'){
                $hasil=$this->tarik_data_mesin($get_req);
                if($request->ajax()){
                    return response()->json($hasil);
                }
            }
        }

        return response()->json(array('error' => true, 'html'=>''));
    }
}