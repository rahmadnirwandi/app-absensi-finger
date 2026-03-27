<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

use App\Services\GlobalService;
use App\Http\Traits\GlobalFunction;

class HariLiburUmumController extends \App\Http\Controllers\MyAuthController
{
    public $part_view, $url_index, $url_name, $title, $breadcrumbs, $globalService, $globalFunction;

    public function __construct()
    {
        $router_name = (new \App\Http\Traits\GlobalFunction)->getRouterIndex();
        $this->part_view = $router_name->path_base;
        $this->url_index = $router_name->uri;
        $this->url_name = $router_name->router_name;

        $this->title = 'Hari Libur Umum';
        $this->breadcrumbs = [
            ['title' => 'Manajemen Absensi', 'url' => url('/') . "/sub-menu?type=6"],
            ['title' => $this->title, 'url' => url('/') . "/" . $this->url_index],
        ];

        $this->globalService = new GlobalService;
        $this->globalFunction = new GlobalFunction;
    }

    function actionIndex(Request $request)
    {
        $form_filter_text = !empty($request->form_filter_text) ? $request->form_filter_text : '';

        $form_filter_text=!empty($request->form_filter_text) ? $request->form_filter_text : '';
        $filter_tahun_bulan=!empty($request->filter_tahun_bulan) ? $request->filter_tahun_bulan : date('Y-m');

        $get_tgl_per_bulan=(new \App\Http\Traits\AbsensiFunction)->get_tgl_per_bulan($filter_tahun_bulan);
        $list_tgl=!empty($get_tgl_per_bulan->list_tgl) ? $get_tgl_per_bulan->list_tgl : [];
        $filter_tgl=!empty($get_tgl_per_bulan->tgl_start_end) ? $get_tgl_per_bulan->tgl_start_end : [];
        $filter_tgl[0]=!empty($filter_tgl[0]) ? $filter_tgl[0] : date('Y-m-d');
        $filter_tgl[1]=!empty($filter_tgl[1]) ? $filter_tgl[1] : date('Y-m-d');

        $paramater_where=[
            'search' => $form_filter_text,
            'tanggal'=>[$filter_tgl[0],$filter_tgl[1]],
        ];

        $list_hari_libur=(new \App\Services\DataPresensiService)->get_data_hari_libur($paramater_where);

        $parameter_view = [
            'title' => $this->title,
            'breadcrumbs' => $this->breadcrumbs,
            'list_hari_libur' => $list_hari_libur,
            'list_tgl'=>$list_tgl
        ];

        return view($this->part_view . '.index', $parameter_view);
    }

    private function form(Request $request)
    {
        $kode = !empty($request->data_sent) ? $request->data_sent : '';
        $paramater = [
            'tanggal' => $kode
        ];
        $model = (new \App\Models\RefHariLiburUmum())->where('tanggal', '=', $kode)->first();
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
        // $link_back_redirect = ($action_is_create) ? $this->url_name : $this->url_name . '/update';
        $link_back_redirect = $this->url_name . '';
        DB::beginTransaction();
        $pesan = [];
        $link_back_param = [];
        if ($action_is_create) {
            $link_back_param = [];
        } else {
            $link_back_param = ['data_sent' => $kode];
        }
        $params_reg=$request->all();
        $params_reg_tgl=!empty($params_reg['tanggal']) ? $params_reg['tanggal'] : date('Y-m-d');
        $params_reg_tahun_bulan_tmp=new \DateTime($params_reg_tgl);
        $params_reg_tahun_bulan=$params_reg_tahun_bulan_tmp->format('Y-m');
        $params_reg['filter_tahun_bulan']=$params_reg_tahun_bulan;
        $link_back_param = array_merge($link_back_param,$params_reg );
        $message_default = [
            'success' => !empty($kode) ? 'Data berhasil diubah' : 'Data berhasil disimpan',
            'error' => !empty($kode) ? 'Data tidak berhasil diubah' : 'Data tidak berhasil disimpan'
        ];

        try {

            $model = (new \App\Models\RefHariLiburUmum)->where('tanggal', '=', $kode)->first();
            if (empty($model)) {
                $model = (new \App\Models\RefHariLiburUmum);
            }
            $data_save = $req;
            $model->set_model_with_data($data_save);
            $is_save = 0;

            if ($model->save()) {
                $is_save = 1;
            }

            if ($is_save) {
                DB::commit();
                $pesan = ['success', $message_default['success'], 2];
            } else {
                DB::rollBack();
                $pesan = ['error', $message_default['error'], 3];
            }
        } catch (\Illuminate\Database\QueryException $e) {
            DB::rollBack();
            if ($e->errorInfo[1] == '1062') {
                $pesan = ['error', 'Data Sudah Input Sebelumnya', 3];
            }else{
                $pesan = ['error', $message_default['error'], 3];
            }
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
            $model = (new \App\Models\RefHariLiburUmum)->where('tanggal', '=', $kode)->first();
            if (empty($model)) {
                return redirect()->route($this->url_name, $link_back_param)->with(['error', 'Data tidak ditemukan']);
            }

            $is_save = 0;
            if ($model->delete()) {
                $is_save = 1;
            }

            if ($is_save) {
                DB::commit();
                DB::statement("ALTER TABLE ".( new \App\Models\RefHariLiburUmum )->table." AUTO_INCREMENT = 1");
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