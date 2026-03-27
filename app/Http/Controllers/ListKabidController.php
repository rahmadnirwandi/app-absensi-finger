<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

use App\Services\GlobalService;
use App\Http\Traits\GlobalFunction;
use App\Services\ListKabidService;

class ListKabidController extends \App\Http\Controllers\MyAuthController
{
    public $part_view, $url_index, $url_name, $title, $breadcrumbs, $globalService, $globalFunction, $listKabidService;

    public function __construct()
    {
        $router_name = (new \App\Http\Traits\GlobalFunction)->getRouterIndex();
        $this->part_view = $router_name->path_base;
        $this->url_index = $router_name->uri;
        $this->url_name = $router_name->router_name;

        $this->title = 'List Kabid';
        $this->breadcrumbs = [
            ['title' => 'Data Karyawan', 'url' => url('/') . "/sub-menu?type=4"],
            ['title' => $this->title, 'url' => url('/') . "/" . $this->url_index],
        ];

        $this->globalService = new GlobalService;
        $this->globalFunction = new GlobalFunction;
        $this->listKabidService = new ListKabidService;
    }

    function actionIndex(Request $request)
    {
        $form_filter_text = !empty($request->form_filter_text) ? $request->form_filter_text : '';

        $paramater=[
            'search' => !empty($form_filter_text) ? $form_filter_text : '',
        ];

        $list_data=(new \App\Services\ListKabidService())->getList($paramater,1)->groupBy('ref_list_kabid.id_karyawan')->paginate(!empty($request->per_page) ? $request->per_page : 15);

        $parameter_view = [
            'title' => $this->title,
            'breadcrumbs' => $this->breadcrumbs,
            'list_data' => $list_data,
        ];

        return view($this->part_view . '.index', $parameter_view);
    }

    private function form(Request $request)
    {
        $kode = !empty($request->data_sent) ? $request->data_sent : "";
        $paramater = [
            'ref_list_kabid.id_karyawan' => $kode
        ];
        $model = $this->listKabidService->getList($paramater, 1)->first();
        if (!empty($model->id_karyawan)) {
            $action_form = $this->part_view . '/update';

            $list_item = $this->listKabidService->getList($paramater, 1)->select('ref_list_kabid.ruangan', 'ref_ruangan.nm_ruangan')->get();
            $item_list_terpilih = [];
            if ($list_item) {
                foreach ($list_item as $value) {
                    $item_list_terpilih[$value->ruangan] = [
                        'value' => 1,
                        'data' => [
                            '',
                            !empty($value->nm_ruangan) ? $value->nm_ruangan : '',
                        ]
                    ];
                }
            }
            if ($item_list_terpilih) {
                $item_list_terpilih = json_encode($item_list_terpilih);
            }
        } else {
            $action_form = $this->part_view . '/create';
        }
        $parameter_view = [
            'action_form' => $action_form,
            'model' => $model,
            'item_list_terpilih' => !empty($item_list_terpilih) ? $item_list_terpilih : ''
        ];
        $get_request = $request->all();
        if (!empty($get_request['_token'])) {
            unset($get_request['_token']);
        }
        $parameter_view = array_merge($parameter_view, $get_request);
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
            $data_save = $req;
            $nip = !empty($request->id_karyawan) ? $request->id_karyawan : '';
            if (empty($nip)) {
                return redirect()->route($link_back_redirect, $link_back_param)->with(['error' => 'NIP tidak boleh kosong']);
            }

            $is_save = 0;

            $item_list_terpilih = !empty($request->item_list_terpilih) ? $request->item_list_terpilih : '';
            $item_list_terpilih = json_decode($item_list_terpilih);
            $item_list_terpilih = (array)$item_list_terpilih;
            if ($item_list_terpilih) {
                if (empty($kode)) {
                    $kode = $nip;
                }
                (new \App\Models\ListKabid())->where('id_karyawan', '=', $kode)->delete();
                $jml_save = 0;
                foreach ($item_list_terpilih  as $key => $value) {
                    $data_save = [
                        'id_karyawan' => $kode,
                        'ruangan' => $key
                    ];
                    $model = (new \App\Models\ListKabid());
                    $model->set_model_with_data($data_save);
                    if ($model->save()) {
                        $jml_save++;
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
            $model = (new \App\Models\ListKabid())->where('id_karyawan', '=', $kode)->first();
            if (empty($model)) {
                return redirect()->route($this->url_name, $link_back_param)->with(['error', 'Data tidak ditemukan']);
            }

            $is_save = 0;
            $model = (new \App\Models\ListKabid())->where('id_karyawan', '=', $kode);
            if ($model->delete()) {
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
            }
            $pesan = ['error', $message_default['error'], 3];
        } catch (\Throwable $e) {
            DB::rollBack();
            $pesan = ['error', $message_default['error'], 3];
        }

        return redirect()->route($this->url_name, $link_back_param)->with([$pesan[0] => $pesan[1]]);
    }

    function getListKabidMasterForm(Request $request)
    {
        $hasil_data = [];
        try {

            $filter_kd_jenis = '';
            $form_filter_text = '';
            $search = !empty($request->search) ? $request->search : '';
            $filter = !empty($search['value']) ? $search['value'] : '';
            if ($filter) {
                $filter = json_decode($filter);
                if (!empty($filter)) {
                    foreach ($filter as $value) {
                        if (trim(strtolower($value->name)) == 'filter_id_departemen') {
                            $filter_id_departemen = $value->value;
                        }
                        if (trim(strtolower($value->name)) == 'form_filter_text') {
                            $form_filter_text = $value->value;
                        }
                    }
                }
            }

            $start_page = !empty($request->start) ? $request->start : 0;
            $end_page = !empty($request->end) ? $request->end : 10;

            $paramater = [
                'search' => $form_filter_text
            ];

            if (!empty($filter_id_departemen)) {
                $paramater['ref_ruangan.id_departemen'] = $filter_id_departemen;
            }

            $data_tmp_tmp = (new \App\Models\RefRuangan);
            $data_tmp_tmp = $data_tmp_tmp->set_where($data_tmp_tmp, $paramater, ['where_or' => ['id_departemen', 'nm_departemen']]);

            $data_tmp = $data_tmp_tmp->offset($start_page)->limit($end_page)->get();
            $data_count = $data_tmp_tmp->count();
            if (!empty($data_tmp)) {
                foreach ($data_tmp as $value) {

                    $data = [
                        "<input class='form-check-input hover-pointer checked_b' style='border-radius: 0px;' type='checkbox' data-kode='" . $value->id_ruangan . "'>",
                        !empty($value->nm_ruangan) ? $value->nm_ruangan : '',
                    ];
                    $hasil_data[] = $data;
                }
            }
        } catch (\Throwable $e) {
            $hasil_data = [];
        }

        return [
            'data' => !empty($hasil_data) ? $hasil_data : [],
            'recordsTotal' => !empty($data_count) ? $data_count : 0,
            'recordsFiltered' => !empty($data_count) ? $data_count : 0,
        ];
    }

    function ajax(Request $request)
    {
        $get_req = $request->all();
        if (!empty($get_req['action'])) {
            if ($get_req['action'] == 'list_kabid_master_form') {
                $hasil = $this->getListKabidMasterForm($request);
                if ($request->ajax()) {
                    $return = '';
                    if ($hasil) {
                        $return = json_encode($hasil);
                    }
                    return $return;
                }
                return $hasil;
            }
        }
    }
}