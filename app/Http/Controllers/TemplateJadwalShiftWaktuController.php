<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

use App\Services\GlobalService;
use App\Services\RefTemplateJadwalShiftService;
use App\Http\Traits\GlobalFunction;

class TemplateJadwalShiftWaktuController extends \App\Http\Controllers\MyAuthController
{
    public $part_view, $url_index, $url_name, $title, $breadcrumbs, $globalService, $globalFunction;
    public $refTemplateJadwalShiftService;

    public function __construct()
    {
        $router_name = (new \App\Http\Traits\GlobalFunction)->getRouterIndex();
        $this->part_view = $router_name->path_base;
        $this->url_index = $router_name->uri;
        $this->url_name = $router_name->router_name;

        $this->title = 'Atur Watku Template Jadwal Shift';
        $this->breadcrumbs = [
            ['title' => 'Manajemen Absensi', 'url' => url('/') . "/sub-menu?type=6"],
            ['title' => $this->title, 'url' => url('/') . "/" . $this->url_index],
        ];

        $this->globalService = new GlobalService;
        $this->globalFunction = new GlobalFunction;
        $this->refTemplateJadwalShiftService = new RefTemplateJadwalShiftService;
    }

    public function get_data_grafik($id_template_jadwal_shift_detail){
        $data_jadwal=( new \App\Models\RefJenisJadwal() )->where(['type_jenis'=>2])->get();
        $data_jadwal_tmp=[];
        if($data_jadwal){
            foreach($data_jadwal as $value){
                $data_jadwal_tmp[$value->id_jenis_jadwal]=(object)$value->getAttributes();
            }
        }

        $model = (new \App\Models\RefTemplateJadwalShiftWaktu)->where('id_template_jadwal_shift_detail', '=', $id_template_jadwal_shift_detail)->get();
        $grafik_data=[];
        if($model){
            foreach($model as $value){
                $item_hari=!empty($value->tgl) ? explode(',',$value->tgl) : [];
                if($item_hari){
                    if(!empty($data_jadwal_tmp[$value->id_jenis_jadwal])){
                        $nilai=$data_jadwal_tmp[$value->id_jenis_jadwal];
                        foreach($item_hari as $hari){
                            $check_hari=$hari;
                            if($check_hari<0){
                                $check_hari=0;
                            }
                            $grafik_data[$check_hari][$value->id_jenis_jadwal]=[
                                'nm_shift'=>$nilai->nm_jenis_jadwal,
                                'start'=>$nilai->masuk_kerja,
                                'end'=>$nilai->pulang_kerja,
                                'besok'=>$nilai->pulang_kerja_next_day,
                                'bgcolor'=>$nilai->bg_color,
                                
                            ];
                        }
                    }else if($value->type==2){
                        foreach($item_hari as $hari){
                            $check_hari=$hari;
                            if($check_hari<0){
                                $check_hari=0;
                            }
                            
                            $grafik_data[$check_hari][$value->id_jenis_jadwal]=[
                                'nm_shift'=>'Libur',
                                'start'=>0,
                                'end'=>0,
                                'besok'=>0,
                                'bgcolor'=>'#e1dede',
                                'type_jadwal'=>2
                            ];
                        }
                    }
                }
            }
        }
        ksort($grafik_data);

        return $grafik_data;
    }

    function actionIndex(Request $request)
    {
        $id_template_shift=!empty($request->get('data_sent')) ? $request->get('data_sent') : 0;
        $id_template_jadwal_shift_detail_tmp=!empty($request->get('data_key')) ? $request->get('data_key') : 0;
        
        $paramater = [
            'id_template_jadwal_shift' => $id_template_shift
        ];

        $item_template_shift = (new \App\Services\RefTemplateJadwalShiftService)->getList($paramater, 1)->first();
        $get_list_template_shift_detail = (new \App\Models\RefTemplateJadwalShiftDetail)->where('id_template_jadwal_shift','=',$id_template_shift)->orderby('jml_periode','ASC')->get();
        if($get_list_template_shift_detail){
            $id_template_shift_detail=!empty($get_list_template_shift_detail[0]) ? $get_list_template_shift_detail[0]->id_template_jadwal_shift_detail : '';
        }
        if(!empty($id_template_jadwal_shift_detail_tmp)){
            $id_template_shift_detail=$id_template_jadwal_shift_detail_tmp;
        }

        $get_template_shift_detail = (new \App\Models\RefTemplateJadwalShiftDetail)->where('id_template_jadwal_shift_detail','=',$id_template_shift_detail)->first();
        
        $grafik_data=$this->get_data_grafik($id_template_shift_detail);

        $paramater=[
            'id_template_jadwal_shift'=>$id_template_shift,
            'type_fungsi'=>'bulan'
        ];
        
        $list_shift_save=(new \App\Services\DataPresensiService)->setListShiftFirstDatabase($paramater);
        
        $parameter_view = [
            'title' => $this->title,
            'breadcrumbs' => $this->breadcrumbs,
            'url_back_index' => 'template-jadwal-shift',
            'item_template_shift'=>$item_template_shift,
            'get_list_template_shift_detail'=>$get_list_template_shift_detail,
            'get_template_shift_detail'=>$get_template_shift_detail,
            'grafik_data'=>$grafik_data,
        ];

        return view($this->part_view . '.index', $parameter_view);
    }

    private function form(Request $request)
    {
        $kode = !empty($request->id_template_jadwal_shift_detail) ? $request->id_template_jadwal_shift_detail : '';
        $get_template_shift_detail = (new \App\Models\RefTemplateJadwalShiftDetail)->where('id_template_jadwal_shift_detail','=',$kode)->first();
        
        $data_jadwal=( new \App\Models\RefJenisJadwal() )->where(['type_jenis'=>2])->get();

        $model = (new \App\Models\RefTemplateJadwalShiftWaktu)->where('id_template_jadwal_shift_detail', '=', $kode)->get();
        $list_data=[];
        if($model){
            foreach($model as $value){
                $item_hari=!empty($value->tgl) ? explode(',',$value->tgl) : [];
                $list_data[$value->id_jenis_jadwal]=[
                    'item'=>$item_hari
                ];
            }
        }
        
        $list_data_json=!empty($list_data) ? json_encode($list_data) : '';
        
        $action_form = $this->part_view . '/update';

        $paramater = [
            'id_template_jadwal_shift' => $kode
        ];
        $item_template_shift = (new \App\Services\RefTemplateJadwalShiftService)->getList($paramater, 1)->first();

        $grafik_data=$this->get_data_grafik($kode);

        $parameter_view = [
            'action_form' => $action_form,
            'model' => $model,
            'item_template_shift'=>$item_template_shift,
            'data_jadwal'=>$data_jadwal,
            'list_data_json'=>$list_data_json,
            'get_template_shift_detail'=>$get_template_shift_detail,
            'grafik_data'=>$grafik_data
        ];

        return view($this->part_view . '.form', $parameter_view);
    }

    function actionUpdate(Request $request)
    {
        if ($request->isMethod('get')) {
            return $this->form($request);
        }
        if ($request->isMethod('post')) {
            return $this->proses($request);
        }
    }

    private function proses($request)
    {
        $req = $request->all();
        $kode = !empty($req['key_old']) ? $req['key_old'] : '';
        $link_back_redirect = $this->url_name;
        DB::beginTransaction();
        $pesan = [];
        $link_back_param = ['data_sent' => $kode];
        $link_back_param = array_merge($link_back_param, $request->all());
        $message_default = [
            'success' => !empty($kode) ? 'Data berhasil diubah' : 'Data berhasil disimpan',
            'error' => !empty($kode) ? 'Data tidak berhasil diubah' : 'Data berhasil disimpan'
        ];
        
        try {
            $id_template_jadwal_shift_detail=$kode;
            $list_data=!empty($req['list_tgl_terpilih']) ? $req['list_tgl_terpilih'] : "";
            $list_data = (array)json_decode($list_data);
            
            $get_template_shift_detail = (new \App\Models\RefTemplateJadwalShiftDetail)->where('id_template_jadwal_shift_detail','=',$id_template_jadwal_shift_detail)->first();
            $link_back_param = [
                'data_sent' => $get_template_shift_detail->id_template_jadwal_shift,
                'data_key' => $get_template_shift_detail->id_template_jadwal_shift_detail
            ];
            $link_back_param = array_merge($link_back_param, $request->all());
            if ($list_data) {
                (new \App\Models\RefTemplateJadwalShiftWaktu)->where('id_template_jadwal_shift_detail', '=', $id_template_jadwal_shift_detail)->delete();
                $jml_save = 0;
                foreach ($list_data  as $key => $value) {
                    $hari=!empty($value->item) ? array_unique($value->item) : [];
                    $type_jadwal=!empty($value->type_jadwal) ? $value->type_jadwal : '';
                    if(!empty($key)){
                        $type_jadwal=1;
                    }else{
                        $type_jadwal=2;
                    }
                    if(!empty($hari)){
                        $data_save=[
                            'id_template_jadwal_shift_detail'=>$id_template_jadwal_shift_detail,
                            'id_jenis_jadwal'=>$key,
                            'type'=>$type_jadwal,
                            'tgl'=>implode(',',$hari),
                        ];
                        $model = (new \App\Models\RefTemplateJadwalShiftWaktu);
                        $model->set_model_with_data($data_save);
                        if ($model->save()) {
                            $jml_save++;
                        }
                    }else{
                        $jml_save=1;    
                    }
                }

                if ($jml_save >= 1) {
                    $is_save = 1;
                }
            }

            if ($is_save) {
                DB::commit();
                $link_back_param = $this->clear_request($link_back_param, $request);
                $pesan = ['success', $message_default['success'], 2];
            } else {
                DB::rollBack();
                $pesan = ['error', $message_default['error'], 3];
            }
        } catch (\Illuminate\Database\QueryException $e) {
            DB::rollBack();
            if ($e->errorInfo[1] == '1062') {
            }
            $pesan = ['error', $message_default['error'], 3];
        } catch (\Throwable $e) {
            DB::rollBack();
            $pesan = ['error', $message_default['error'], 3];
        }

        return redirect()->route($link_back_redirect, $link_back_param)->with([$pesan[0] => $pesan[1]]);
    }

    function actionDelete(Request $request)
    {
        DB::beginTransaction();
        $pesan = [];
        $link_back_param = [];
        $message_default = [
            'success' => 'Data berhasil dihapus',
            'error' => 'Maaf data tidak berhasil dihapus'
        ];

        $kode = !empty($request->data_sent) ? $request->data_sent : null;

        try {
            $model = (new \App\Models\RefTemplateJadwalShift)->where('id_template_jadwal_shift', '=', $kode)->first();
            if (empty($model)) {
                return redirect()->route($this->url_name, $link_back_param)->with(['error', 'Data tidak ditemukan']);
            }

            $is_save = 0;
            if ($model->delete()) {
                $is_save = 1;
            }

            if ($is_save) {
                DB::commit();
                DB::statement("ALTER TABLE ".( new \App\Models\RefTemplateJadwalShift )->table." AUTO_INCREMENT = 1");
                $pesan = ['success', $message_default['success'], 2];
            } else {
                DB::rollBack();
                $pesan = ['error', $message_default['error'], 3];
            }
        } catch (\Illuminate\Database\QueryException $e) {
            DB::rollBack();
            if ($e->errorInfo[1] == '1062') {
            }
            $pesan = ['error', $message_default['error'], 3];
        } catch (\Throwable $e) {
            DB::rollBack();
            $pesan = ['error', $message_default['error'], 3];
        }

        return redirect()->route($this->url_name, $link_back_param)->with([$pesan[0] => $pesan[1]]);
    }
}