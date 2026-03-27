<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Services\GlobalService;
use App\Services\RefKaryawanService;
use App\Services\JenisCutiService;

class JenisCutiController extends Controller
{
    public $part_view, $url_index, $url_name, $title, $breadcrumbs, $globalService, $jenisCutiService;
    protected $masterPengajuanService;
    public function __construct()
    {
        $router_name = (new \App\Http\Traits\GlobalFunction)->getRouterIndex();
        $this->part_view = $router_name->path_base;
        $this->url_index = $router_name->uri;
        $this->url_name = $router_name->router_name;
        $this->jenisCutiService = new JenisCutiService;

        $this->title = 'Jenis Cuti';
        $this->breadcrumbs = [
            ['title' => 'Manajemen Absensi', 'url' => url('/') . "/sub-menu?type=6"],
            ['title' => $this->title, 'url' => url('/') . "/" . $this->url_index],
        ];

        $this->globalService = new GlobalService;
        $this->refKaryawanService = new RefKaryawanService;
    }

    function actionIndex(Request $request){
        $user_auth = (new \App\Http\Traits\AuthFunction)->getUser();
        $role = $user_auth->group_user[0] ?? null;

        $form_filter_text = $request->form_filter_text ?? '';

        $paramater = [
            'search' => $form_filter_text,
        ];

        $list_data = $this->jenisCutiService
            ->getListData($paramater)
            ->paginate($request->per_page ?? 10);

    
        $parameter_view = [
            'title' => $this->title,
            'breadcrumbs' => $this->breadcrumbs,
            'list_data' => $list_data
        ];

        return view('jenis-cuti.index', $parameter_view);
    }

    public function form(Request $request) {
        
        $parameter_view = [
            'action_form' => 'jenis-cuti/create',
        ];

        return view('jenis-cuti.form', $parameter_view);
    }

    public function actionCreate(Request $request)
    {
        if ($request->isMethod('get')) {
            return $this->form($request);
        }
        if ($request->isMethod('post')) {
            return $this->proses($request);
        }
    }

    public function proses(Request $request) {
        $data_req = $request->all();

        $message_default = [
            'success' => 'Jenis cuti berhasil disimpan ',
            'error' =>'Jenis Cuti tidak berhasil disimpan',
        ];


        $link_back_redirect = $this->url_name;
        $link_back_param = [];
        $result = $this->jenisCutiService->insert($data_req);



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

        $id_jenis_cuti = !empty($data['id']) ? $data['id'] : '';

        $data_jenis_cuti = $this->jenisCutiService->getJenisCuti($id_jenis_cuti);

        $parameter_modal = [
            'data' => $data_jenis_cuti
        ];

        $returnHTML = view('jenis-cuti.update', $parameter_modal)->render();
        return response()->json(array('success' => true, 'html'=>$returnHTML));
    }

    private function prosesUpdate($request)
    {
        $data_req = $request->all();

        $message_default = [
            'success' => 'Jenis cuti berhasil diubah',
            'error' =>'Jenis cuti tidak berhasil diubah',
        ];

        $link_back_redirect = $this->url_name;
        $link_back_param = [];
        $result = $this->jenisCutiService->update($data_req);

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
            'success' => 'Jenis cuti berhasil dihapus',
            'error' =>'Jenis cuti tidak berhasil dihapus',
        ];


        $link_back_redirect = $this->url_name;
        $link_back_param = [];
        $result = $this->jenisCutiService->delete($data_req);

        if($result) {
            $pesan = ['success', $message_default['success'], 2];
        }else {
            $pesan = ['error', $message_default['error'], 3];
        }    

        return redirect()->route($link_back_redirect, $link_back_param)->with([$pesan[0] => $pesan[1]]);
    }

    public function ajax(Request $request)
    {
        return response()->json(
            $this->jenisCutiService->getListJenisCuti($request->search)
        );
    }


}
