<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

use App\Services\GlobalService;
use App\Services\RefJadwalService;
use App\Http\Traits\GlobalFunction;

class JadwalAbsensiController extends \App\Http\Controllers\MyAuthController
{
    public $part_view, $url_index, $url_name, $title, $breadcrumbs, $globalService, $globalFunction;
    public $refJadwalService;

    public function __construct()
    {
        $router_name = (new \App\Http\Traits\GlobalFunction)->getRouterIndex();
        $this->part_view = $router_name->path_base;
        $this->url_index = $router_name->uri;
        $this->url_name = $router_name->router_name;

        $this->title = 'Pengaturan Jadwal Presensi';
        $this->breadcrumbs = [
            ['title' => 'Manajemen Absensi', 'url' => url('/') . "/sub-menu?type=6"],
            ['title' => $this->title, 'url' => url('/') . "/" . $this->url_index],
        ];

        $this->globalService = new GlobalService;
        $this->globalFunction = new GlobalFunction;
        $this->refJadwalService = new RefJadwalService;
    }

    function actionIndex(Request $request)
    {
        DB::statement("ALTER TABLE ".( new \App\Models\RefJadwal() )->table." AUTO_INCREMENT = 1");
        $form_filter_text = !empty($request->form_filter_text) ? $request->form_filter_text : '';

        $paramater=[
            'search' => $form_filter_text
        ];

        $list_data = $this->refJadwalService->getList($paramater, 1)->orderBy('kd_jadwal','ASC')->paginate(!empty($request->per_page) ? $request->per_page : 400);

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
            'id_jadwal' => $kode
        ];
        $model = $this->refJadwalService->getList($paramater, 1)->first();
        if ($model) {
            $action_form = $this->part_view . '/update';
        } else {
            $action_form = $this->part_view . '/create';
        }

        $parameter_view = [
            'action_form' => $action_form,
            'model' => $model
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
            $model = (new \App\Models\RefJadwal)->where('id_jadwal', '=', $kode)->first();
            if (empty($model)) {
                $model = (new \App\Models\RefJadwal);
            }
            $data_save = $req;
            $data_save['alias'] = strtolower(str_replace(" ", "_", $this->globalFunction->remove_multiplespace($data_save['uraian'])));
            
            $toren_jam_cepat=!empty($data_save['toren_jam_cepat']) ? $data_save['toren_jam_cepat'] : '00:00';
            $status_toren_jam_cepat=0;
            if((new \App\Http\Traits\AbsensiFunction)->his_to_seconds($toren_jam_cepat)>0){
                $status_toren_jam_cepat=1;
            }
            $data_save['status_toren_jam_cepat']=$status_toren_jam_cepat;

            $toren_jam_telat=!empty($data_save['toren_jam_telat']) ? $data_save['toren_jam_telat'] : '00:00';
            $status_toren_jam_telat=0;
            if((new \App\Http\Traits\AbsensiFunction)->his_to_seconds($toren_jam_telat)>0){
                $status_toren_jam_telat=1;
            }
            $data_save['status_toren_jam_telat']=$status_toren_jam_telat;
            $data_save['status_jadwal']=!empty($data_save['status_jadwal']) ? $data_save['status_jadwal'] : 0;

            $model->set_model_with_data($data_save);

            $is_save = 0;

            if ($model->save()) {
                $is_save = 1;
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
            $model = (new \App\Models\RefJadwal)->where('id_jadwal', '=', $kode)->first();
            if (empty($model)) {
                return redirect()->route($this->url_name, $link_back_param)->with(['error', 'Data tidak ditemukan']);
            }

            $is_save = 0;
            if ($model->delete()) {
                $is_save = 1;
            }

            if ($is_save) {
                DB::commit();
                DB::statement("ALTER TABLE ".( new \App\Models\RefJadwal )->table." AUTO_INCREMENT = 1");
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