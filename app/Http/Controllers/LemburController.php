<?php

namespace App\Http\Controllers;

use App\Services\GlobalService;
use App\Services\RefKaryawanService;
use Illuminate\Http\Request;
use App\Services\LemburService;
use App\Services\MasterPengajuanService;

class LemburController extends Controller
{
    public $part_view, $url_index, $url_name, $title, $breadcrumbs, $globalService, $lemburService;
    protected $masterPengajuanService;
    public function __construct()
    {
        $router_name = (new \App\Http\Traits\GlobalFunction)->getRouterIndex();
        $this->part_view = $router_name->path_base;
        $this->url_index = $router_name->uri;
        $this->url_name = $router_name->router_name;
        $this->lemburService = new LemburService();
        $this->masterPengajuanService = new MasterPengajuanService;

        $this->title = 'Lembur';
        $this->breadcrumbs = [
            ['title' => 'Pengajuan', 'url' => url('/') . "/sub-menu?type=7"],
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
            'is_super_admin' => false
        ];

        if ($role === 'group_super_admin') {

            $paramater['is_super_admin'] = true;

        } else {

            $get_karyawan = $this->masterPengajuanService
                ->getKaryawan($user_auth->id_user);

            $paramater['id_karyawan'] = $get_karyawan->id_karyawan;
            $paramater['id_ruangan']  = $get_karyawan->id_ruangan;
            $paramater['id_jabatan']  = $get_karyawan->id_jabatan;
        }

        $list_data = $this->lemburService
            ->getListData($paramater)
            ->paginate($request->per_page ?? 10);
    
        $parameter_view = [
            'title' => $this->title,
            'breadcrumbs' => $this->breadcrumbs,
            'list_data' => $list_data
        ];

        return view('pengajuan.lembur.index', $parameter_view);
    }

    public function formCreate(Request $request) {
         $user_auth=(new \App\Http\Traits\AuthFunction)->getUser();
        $role = $user_auth->group_user[0] ?? null;

        if($role != 'group_super_admin' ) {
            $user_auth=(new \App\Http\Traits\AuthFunction)->getUser()->data_user_sistem;
            $get_karyawan = $this->lemburService->getKaryawan($user_auth->id_karyawan);
        }
        
        $parameter_view = [
            'action_form' => 'lembur/create',
            'model' => !empty($get_karyawan) ? $get_karyawan : [],
        ];

        return view('pengajuan.lembur.form', $parameter_view);
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

   public function prosesCreate(Request $request)
    {
        $data_req = $request->all();

        $message_default = [
            'success' => 'Lembur berhasil diajukan',
            'error'   => 'Lembur tidak berhasil diajukan',
        ];

        try {
            $this->lemburService->insert($data_req);

            return redirect()
                ->route($this->url_name)
                ->with('success', $message_default['success']);

        } catch (\Exception $e) {

            return redirect()
                ->route($this->url_name)
                ->with('error', $e->getMessage());
        }
    }

    public function formUpdate(Request $request) {
         if(empty($request->data_sent)){
            return response()->json([], 404);
        }
        $data = json_decode($request->data_sent, true);

        $id_lembur = !empty($data['id_pengajuan']) ? $data['id_pengajuan'] : '';

        $data_cuti = $this->lemburService->getLembur($id_lembur);

        $parameter_modal = [
            'action_update' => '/lembur/update',
            'data' => $data_cuti
        ];

        $returnHTML = view('pengajuan.lembur.update', $parameter_modal)->render();
        return response()->json(array('success' => true, 'html'=>$returnHTML));
    }

    public function actionUpdate(Request $request)
    {
        if ($request->isMethod('get')) {
            return $this->formUpdate($request);
        }
        if ($request->isMethod('post')) {
            return $this->prosesUpdate($request);
        }
    }

   public function prosesUpdate(Request $request)
    {
        $data_req = $request->all();

        $message_default = [
            'success' => 'Pengajuan lembur berhasil diubah',
            'error'   => 'Pengajuan lembur tidak berhasil diubah',
        ];

        try {
            $this->lemburService->update($data_req);

            return redirect()
                ->route($this->url_name)
                ->with('success', $message_default['success']);

        } catch (\Exception $e) {

            return redirect()
                ->route($this->url_name)
                ->with('error', $e->getMessage());
        }
    }

    public function actionDelete(Request $request) {
        $data_req = $request->all();

        $message_default = [
            'success' => 'Pengajuan lembur berhasil dihapus',
            'error' =>'Pengajuan lembur tidak berhasil dihapus',
        ];
        
        try {
            $this->lemburService->delete($data_req);

            return redirect()
                ->route($this->url_name)
                ->with('success', $message_default['success']);

        } catch (\Exception $e) {

            return redirect()
                ->route($this->url_name)
                ->with('error', $e->getMessage());
        }
    }

    public function actionIndexApproved(Request $request) {

        $user_auth = (new \App\Http\Traits\AuthFunction)->getUser();
        $role = $user_auth->group_user[0] ?? null;

        $form_filter_text = $request->form_filter_text ?? '';

        $paramater = [
            'search' => $form_filter_text,
            'is_super_admin' => false
        ];

        if ($role === 'group_super_admin') {

            $paramater['is_super_admin'] = true;

        } else {

            $get_karyawan = $this->masterPengajuanService
                ->getKaryawan($user_auth->id_user);

            $paramater['id_karyawan'] = $get_karyawan->id_karyawan;
            $paramater['id_ruangan']  = $get_karyawan->id_ruangan;
            $paramater['id_jabatan']  = $get_karyawan->id_jabatan;
        }

        $list_data = $this->lemburService
            ->getListDataApprove($paramater)
            ->paginate($request->per_page ?? 10);
    
        $parameter_view = [
            'title' => $this->title,
            'breadcrumbs' => $this->breadcrumbs,
            'list_data' => $list_data
        ];

        return view('pengajuan.lembur.approved.index', $parameter_view);
    }

    public function actionIndexRejected(Request $request) {

        $user_auth = (new \App\Http\Traits\AuthFunction)->getUser();
        $role = $user_auth->group_user[0] ?? null;

        $form_filter_text = $request->form_filter_text ?? '';

        $paramater = [
            'search' => $form_filter_text,
            'is_super_admin' => false
        ];

        if ($role === 'group_super_admin') {

            $paramater['is_super_admin'] = true;

        } else {

            $get_karyawan = $this->masterPengajuanService
                ->getKaryawan($user_auth->id_user);

            $paramater['id_karyawan'] = $get_karyawan->id_karyawan;
            $paramater['id_ruangan']  = $get_karyawan->id_ruangan;
            $paramater['id_jabatan']  = $get_karyawan->id_jabatan;
        }

        $list_data = $this->lemburService
            ->getListDataReject($paramater)
            ->paginate($request->per_page ?? 10);
    
        $parameter_view = [
            'title' => $this->title,
            'breadcrumbs' => $this->breadcrumbs,
            'list_data' => $list_data
        ];

        return view('pengajuan.lembur.rejected.index', $parameter_view);
    }
}
