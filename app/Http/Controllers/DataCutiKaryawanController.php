<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Services\GlobalService;
use App\Services\RefKaryawanService;
use App\Services\DataCutiKaryawanService;

class DataCutiKaryawanController extends Controller
{
    public $part_view, $url_index, $url_name, $title, $breadcrumbs, $globalService, $dataCutiKaryawanService;
    protected $masterPengajuanService;
    public function __construct()
    {
        $router_name = (new \App\Http\Traits\GlobalFunction)->getRouterIndex();
        $this->part_view = $router_name->path_base;
        $this->url_index = $router_name->uri;
        $this->url_name = $router_name->router_name;
        $this->dataCutiKaryawanService = new DataCutiKaryawanService;

        $this->title = 'Data Cuti Karyawan';
        $this->breadcrumbs = [
            ['title' => 'Manajemen Absensi', 'url' => url('/') . "/sub-menu?type=6"],
            ['title' => $this->title, 'url' => url('/') . "/" . $this->url_index],
        ];

        $this->globalService = new GlobalService;
        $this->refKaryawanService = new RefKaryawanService;
    }

    function actionIndex(Request $request){
        $form_filter_text = $request->form_filter_text ?? '';

        $paramater = [
            'search' => $form_filter_text,
        ];

        $list_data = $this->dataCutiKaryawanService
            ->getListData($paramater)
            ->paginate($request->per_page ?? 10);

    
        $parameter_view = [
            'title' => $this->title,
            'breadcrumbs' => $this->breadcrumbs,
            'list_data' => $list_data
        ];

        return view('data-cuti-karyawan.index', $parameter_view);
    }

    public function formCreate(Request $request) {
        
        $parameter_view = [
            'action_form' => 'data-cuti-karyawan/create',
        ];

        return view('data-cuti-karyawan.form', $parameter_view);
    }

    public function actionCreate(Request $request)
    {
        if ($request->isMethod('get')) {
            return $this->formCreate($request);
        }
        if ($request->isMethod('post')) {
            return $this->prosesCreate($request);
        }
    }

    public function prosesCreate(Request $request) {
        $data_req = $request->all();

        $message_default = [
            'success' => 'Stok cuti Karyawan berhasil disimpan ',
            'error' =>'Stok cuti Karyawan tidak berhasil disimpan',
        ];


        $link_back_redirect = $this->url_name;
        $link_back_param = [];
        $result = $this->dataCutiKaryawanService->insert($data_req);



        if($result) {
            $pesan = ['success', $message_default['success'], 2];
        }else {
            $pesan = ['error', $message_default['error'], 3];
        }    

        return redirect()->route($link_back_redirect, $link_back_param)->with([$pesan[0] => $pesan[1]]);
            
    }

    public function actionUpdate(Request $request)
    {
        
        if($request->isMethod('get')){
            return $this->formUpdate($request);
        }
        if($request->isMethod('post')){
            return $this->prosesUpdate($request);
        }
    }

    public function formUpdate(Request $request)
    {
        if(empty($request->data_sent)){
            return response()->json([], 404);
        }
        $data = json_decode($request->data_sent, true);

        $id_cuti_karyawan = !empty($data['id']) ? $data['id'] : '';

        $data_jenis_cuti = $this->dataCutiKaryawanService->getDataCutiKaryawan($id_cuti_karyawan);

        $parameter_modal = [
            'data' => $data_jenis_cuti,
            'action_form' => 'data-cuti-karyawan/update',
        ];

        $returnHTML = view('data-cuti-karyawan.update', $parameter_modal)->render();
        return response()->json(array('success' => true, 'html'=>$returnHTML));
    }

    private function prosesUpdate($request)
    {
        $data_req = $request->all();

        $message_default = [
            'success' => 'Stok cuti Karyawan berhasil diubah',
            'error' =>'Stok cuti Karyawan tidak berhasil diubah',
        ];

        $link_back_redirect = $this->url_name;
        $link_back_param = [];
        $result = $this->dataCutiKaryawanService->update($data_req);

        if($result) {
            $pesan = ['success', $message_default['success'], 2];
        }else {
            $pesan = ['error', $message_default['error'], 3];
        }    

        return redirect()->route($link_back_redirect, $link_back_param)->with([$pesan[0] => $pesan[1]]);
    
    }

    public function actionDelete(Request $request) {
        $data_req = $request->all();

        $message_default = [
            'success' => 'Stok cuti Karyawa berhasil dihapus',
            'error' =>'Stok cuti Karyawa tidak berhasil dihapus',
        ];


        $link_back_redirect = $this->url_name;
        $link_back_param = [];
        $result = $this->dataCutiKaryawanService->delete($data_req);

        if($result) {
            $pesan = ['success', $message_default['success'], 2];
        }else {
            $pesan = ['error', $message_default['error'], 3];
        }    

        return redirect()->route($link_back_redirect, $link_back_param)->with([$pesan[0] => $pesan[1]]);
    }

    public function formCreateAll(Request $request) {
        
        $parameter_view = [
            'action_form' => 'data-cuti-karyawan/create-all',
        ];

        return view('data-cuti-karyawan.form-create-all', $parameter_view);
    }

    public function actionCreateAll(Request $request)
    {
        if ($request->isMethod('get')) {
            return $this->formCreateAll($request);
        }
        if ($request->isMethod('post')) {
            return $this->prosesCreateAll($request);
        }
    }

    public function prosesCreateAll(Request $request) {
        $data_req = $request->all();

        $message_default = [
            'success' => 'Stok cuti semua Karyawan berhasil disimpan ',
            'error' =>'Stok cuti semua Karyawan tidak berhasil disimpan',
        ];


        $link_back_redirect = $this->url_name;
        $link_back_param = [];
        $result = $this->dataCutiKaryawanService->insertSemuaKaryawan($data_req);



        if($result) {
            $pesan = ['success', $message_default['success'], 2];
        }else {
            $pesan = ['error', $message_default['error'], 3];
        }    

        return redirect()->route($link_back_redirect, $link_back_param)->with([$pesan[0] => $pesan[1]]);
            
    }

}
