<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

use App\Services\GlobalService;
use App\Services\RefTemplateJadwalShiftService;
use App\Http\Traits\GlobalFunction;

class TemplateJadwalShiftController extends \App\Http\Controllers\MyAuthController
{
    public $part_view, $url_index, $url_name, $title, $breadcrumbs, $globalService, $globalFunction;
    public $refTemplateJadwalShiftService;

    public function __construct()
    {
        $router_name = (new \App\Http\Traits\GlobalFunction)->getRouterIndex();
        $this->part_view = $router_name->path_base;
        $this->url_index = $router_name->uri;
        $this->url_name = $router_name->router_name;

        $this->title = 'Template Jadwal Shift';
        $this->breadcrumbs = [
            ['title' => 'Manajemen Absensi', 'url' => url('/') . "/sub-menu?type=6"],
            ['title' => $this->title, 'url' => url('/') . "/" . $this->url_index],
        ];

        $this->globalService = new GlobalService;
        $this->globalFunction = new GlobalFunction;
        $this->refTemplateJadwalShiftService = new RefTemplateJadwalShiftService;
    }

    function actionIndex(Request $request)
    {
        DB::statement("ALTER TABLE ".( new \App\Models\RefTemplateJadwalShift() )->table." AUTO_INCREMENT = 1");
        DB::statement("ALTER TABLE ".( new \App\Models\RefTemplateJadwalShiftDetail() )->table." AUTO_INCREMENT = 1");
        $form_filter_text = !empty($request->form_filter_text) ? $request->form_filter_text : '';

        $paramater=[
            'search' => $form_filter_text
        ];

        $list_data = $this->refTemplateJadwalShiftService->getList($paramater, 1)->orderBy('id_template_jadwal_shift','ASC')->paginate(!empty($request->per_page) ? $request->per_page : 15);

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
            'id_template_jadwal_shift' => $kode
        ];
        $model = $this->refTemplateJadwalShiftService->getList($paramater, 1)->first();
        
        if ($model) {
            $action_form = $this->part_view . '/update';
        } else {
            $action_form = $this->part_view . '/create';
        }

        $parameter_view = [
            'action_form' => $action_form,
            'model' => $model,
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
            'error' => !empty($kode) ? 'Data tidak berhasil diubah' : 'Data berhasil disimpan'
        ];
        
        try {
            $is_save = 0;

            $model = (new \App\Models\RefTemplateJadwalShift)->where('id_template_jadwal_shift', '=', $kode)->first();
            if (empty($model)) {
                $model = (new \App\Models\RefTemplateJadwalShift);
            }
            
            $data_save=[
                'nm_shift'=>!empty($req['nm_shift']) ? trim($req['nm_shift']) : ""
            ];

            $model->set_model_with_data($data_save);
            
            if($model->save()){
                if(empty($model->id_template_jadwal_shift)){
                    $id_template_jadwal_shift= DB::getPdo()->lastInsertId();
                }else{
                    $id_template_jadwal_shift=$model->id_template_jadwal_shift;
                }

                $model_detail = (new \App\Models\RefTemplateJadwalShiftDetail)->where('id_template_jadwal_shift', '=', $id_template_jadwal_shift)->first();
                if (empty($model_detail)) {
                    $model_detail = (new \App\Models\RefTemplateJadwalShiftDetail);
                }

                $tgl_mulai=!empty($req['tgl_mulai']) ? trim($req['tgl_mulai']) : date('Y-m-d');
                $filter_tahun_bulan=new \DateTime($tgl_mulai);
                $filter_tahun_bulan=$filter_tahun_bulan->format('Y-m');
                $get_tgl_per_bulan=(new \App\Http\Traits\AbsensiFunction)->get_tgl_per_bulan($filter_tahun_bulan);

                $start_day=$tgl_mulai;
                $end_day=$get_tgl_per_bulan->tgl_start_end[1];
                $date1 = new \DateTime($start_day);
                $date2 = new \DateTime($end_day);
                $interval = $date1->diff($date2);
                $jml_periode=$interval->days;
                
                $data_save_detail=[
                    'id_template_jadwal_shift'=>$id_template_jadwal_shift,
                    'tgl_mulai'=>$tgl_mulai,
                    'jml_periode'=>$jml_periode+1,
                    'type_periode'=>1
                ];

                $model_detail->set_model_with_data($data_save_detail);
            
                if($model_detail->save()){
                    $is_save=1;
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
                $model_detail = (new \App\Models\RefTemplateJadwalShiftLanjutan)->where('id_template_jadwal_shift', '=', $kode);
                $is_save = 1;
                if(!empty($model_detail->count('id_template_jadwal_shift'))){
                    $is_save = 0;
                    if($model_detail->delete()){
                        $is_save = 1;
                    }
                }
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