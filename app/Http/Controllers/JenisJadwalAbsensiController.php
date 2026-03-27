<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

use App\Services\GlobalService;
use App\Http\Traits\GlobalFunction;

class JenisJadwalAbsensiController extends \App\Http\Controllers\MyAuthController
{
    public $part_view, $url_index, $url_name, $title, $breadcrumbs, $globalService, $globalFunction;

    public function __construct()
    {
        $router_name = (new \App\Http\Traits\GlobalFunction)->getRouterIndex();
        $this->part_view = $router_name->path_base;
        $this->url_index = $router_name->uri;
        $this->url_name = $router_name->router_name;

        $this->title = 'Data Jadwal Kerja';
        $this->breadcrumbs = [
            ['title' => 'Manajemen Absensi', 'url' => url('/') . "/sub-menu?type=6"],
            ['title' => $this->title, 'url' => url('/') . "/" . $this->url_index],
        ];

        $this->globalService = new GlobalService;
        $this->globalFunction = new GlobalFunction;
    }

    function actionIndex(Request $request)
    {
        DB::statement("ALTER TABLE ".( new \App\Models\RefJenisJadwal() )->table." AUTO_INCREMENT = 1");
        $form_filter_text = !empty($request->form_filter_text) ? $request->form_filter_text : '';

        $paramater=[
            'search' => $form_filter_text
        ];

        $data_tmp_tmp=( new \App\Models\RefJenisJadwal() );
        $list_data=$data_tmp_tmp->set_where($data_tmp_tmp,$paramater)->paginate(!empty($request->per_page) ? $request->per_page : 15);

        $parameter_view = [
            'title' => $this->title,
            'breadcrumbs' => $this->breadcrumbs,
            'list_data' => $list_data
        ];

        return view($this->part_view . '.index', $parameter_view);
    }

    private function form(Request $request)
    {
        $kode = !empty($request->data_sent) ? $request->data_sent : '';
        $paramater = [
            'id_jenis_jadwal' => $kode
        ];
        $model = (new \App\Models\RefJenisJadwal())->where('id_jenis_jadwal', '=', $kode)->first();
        if ($model) {
            $action_form = $this->part_view . '/update';
        } else {
            $action_form = $this->part_view . '/create';
        }

        $type_jenis_jadwal = (new \App\Models\RefJenisJadwal())->type_jenis_jadwal();
        
        $get_bgcolor = (new \App\Models\RefJenisJadwal())->get_bgcolor();

        $get_bgcolor_use_tmp=(new \App\Models\RefJenisJadwal())->select('bg_color')->whereNotNull('bg_color')->where('bg_color','!=','')->groupBy("bg_color")->get();
        $get_bgcolor_use=collect($get_bgcolor_use_tmp)->map(function ($model) {

            if(!empty($model->bg_color)){
                return $model->bg_color;
            }
        })->toArray();

        $parameter_view = [
            'action_form' => $action_form,
            'model' => $model,
            'type_jenis_jadwal'=>$type_jenis_jadwal,
            'get_bgcolor'=>$get_bgcolor,
            'get_bgcolor_use'=>$get_bgcolor_use
        ];

        return view($this->part_view . '.form', $parameter_view);
    }

    function actionCreate(Request $request)
    {
        if ($request->isMethod('get')) {
            return $this->form($request);
        }
        if ($request->isMethod('post')) {
            return $this->proses($request);
        }
    }

    function actionUpdate(Request $request)
    {
        if ($request->isMethod('get')) {
            $bagan_form = $this->form($request);

            $parameter_view = [
                'title' => $this->title,
                'breadcrumbs' => $this->breadcrumbs,
                'bagan_form' => $bagan_form,
                'url_back' => $this->url_name
            ];

            return view('layouts.index_bagan_form', $parameter_view);
        }

        if ($request->isMethod('post')) {
            return $this->proses($request);
        }
    }

    private function proses($request)
    {
        $req = $request->all();
        DB::statement("ALTER TABLE ".( new \App\Models\RefJenisJadwal )->table." AUTO_INCREMENT = 1");
        DB::statement("ALTER TABLE ".( new \App\Models\RefJadwal )->table." AUTO_INCREMENT = 1");
        $kode = !empty($req['key_old']) ? $req['key_old'] : '';
        $action_is_create = (str_contains($request->getPathInfo(), $this->url_index . '/create')) ? 1 : 0;
        $link_back_redirect = ($action_is_create) ? $this->url_name : $this->url_name . '/update';
        DB::beginTransaction();
        $pesan = [];
        $link_back_param = [];
        if ($action_is_create) {
            $link_back_param = [];
        } else {
            $link_back_param = ['data_sent' => $kode];
        }
        $link_back_param = array_merge($link_back_param, $request->all());
        $message_default = [
            'success' => !empty($kode) ? 'Data berhasil diubah' : 'Data berhasil disimpan',
            'error' => !empty($kode) ? 'Data tidak berhasil diubah' : 'Data tidak berhasil disimpan'
        ];

        try {
            $model = (new \App\Models\RefJenisJadwal)->where('id_jenis_jadwal', '=', $kode)->first();
            if (empty($model)) {
                $model = (new \App\Models\RefJenisJadwal);
            }
            $data_save = $req;
            $hari_kerja_tmp=!empty($data_save['hari_kerja']) ? $data_save['hari_kerja'] : '';
            $hari_kerja='';
            if($hari_kerja_tmp){
                $hari_kerja=implode(',',$hari_kerja_tmp);
            }
            $data_save['hari_kerja']=$hari_kerja;

            $data_save['pulang_kerja_next_day']=!empty($data_save['pulang_kerja_next_day']) ? 1 : 0;
            $data_save['akhir_istirahat_next_day']=!empty($data_save['akhir_istirahat_next_day']) ? 1 : 0;

            if( empty($model->awal_istirahat) or empty($model->awal_istirahat) ){
                $data_save['akhir_istirahat_next_day']=0;
            }

            if(!empty($model->id_jenis_jadwal)){
                if($model->id_jenis_jadwal==1){
                    $data_save['bg_color']="#87b1f0";
                }
            }

            $model->set_model_with_data($data_save);
            
            if(!empty($model->id_jenis_jadwal)){
                if( $model->id_jenis_jadwal==1 ){
                    $model->type_jenis=1;
                }
            }
            
            $is_save = 0;

            if ($model->save()) {
                $get_ref_jadwal=(new \App\Models\RefJadwal)->referensi_data();
                $type_jadwal_istirahat=1;
                if( empty($model->awal_istirahat) or empty($model->awal_istirahat) ){
                    $type_jadwal_istirahat=0;
                    $data_delete = (new \App\Models\RefJadwal)->where('id_jenis_jadwal', '=', $kode)->whereIn('kd_jadwal', [2, 3])->delete();
                }
                
                if(!empty($get_ref_jadwal)){
                    foreach($get_ref_jadwal as $val_rj){
                        $check_istirahat=0;
                        
                        if(empty($type_jadwal_istirahat)){
                            if($val_rj->kd_jadwal==2 or $val_rj->kd_jadwal==3){
                                $check_istirahat=1;
                            }
                        }

                        if(empty($check_istirahat)){    
                            $model_jadwal = (new \App\Models\RefJadwal)->where('id_jenis_jadwal', '=', $kode)->where('kd_jadwal', '=', $val_rj->kd_jadwal)->first();
                            if (empty($model_jadwal)) {
                                $model_jadwal = (new \App\Models\RefJadwal);
                                $model_jadwal->kd_jadwal=$val_rj->kd_jadwal;
                                $model_jadwal->id_jenis_jadwal=$model->id_jenis_jadwal;
                                $model_jadwal->uraian=$val_rj->uraian;
                                $model_jadwal->alias=$val_rj->alias;
                                $model_jadwal->status_toren_jam_cepat=$val_rj->status_toren_jam_cepat;
                                $model_jadwal->status_toren_jam_telat=$val_rj->status_toren_jam_telat;
                                $model_jadwal->status_jadwal=$val_rj->status_jadwal;

                                if ($model_jadwal->save()) {
                                    $is_save++;
                                }
                            }else{
                                $is_save = 1;
                            }
                        }
                    }
                }else{
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
            $model = (new \App\Models\RefJenisJadwal)->where('id_jenis_jadwal', '=', $kode)->first();
            if (empty($model)) {
                return redirect()->route($this->url_name, $link_back_param)->with(['error', 'Data tidak ditemukan']);
            }

            $is_save = 0;
            if ($model->delete()) {
                $is_save = 1;
            }

            if ($is_save) {
                DB::commit();
                DB::statement("ALTER TABLE ".( new \App\Models\RefJenisJadwal )->table." AUTO_INCREMENT = 1");
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