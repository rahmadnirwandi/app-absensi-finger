<?php

namespace App\Http\Controllers;

use App\Services\GlobalService;
use App\Services\RefKaryawanService;
use Illuminate\Http\Request;
use App\Services\ApproverCutiService;
use App\Services\MasterPengajuanService;


class ApproverCutiController  extends Controller {
    public $part_view, $url_index, $url_name, $title, $breadcrumbs, $globalService, $approverCutiService;
    protected $masterPengajuanService;
    public function __construct()
    {
        $router_name = (new \App\Http\Traits\GlobalFunction)->getRouterIndex();
        $this->part_view = $router_name->path_base;
        $this->url_index = $router_name->uri;
        $this->url_name = $router_name->router_name;
        $this->approverCutiService = new ApproverCutiService;
        $this->masterPengajuanService = new MasterPengajuanService;

        $this->title = 'Cuti';
        $this->breadcrumbs = [
            ['title' => 'Persetujuan', 'url' => url('/') . "/sub-menu?type=8"],
            ['title' => $this->title, 'url' => url('/') . "/" . $this->url_index],
        ];

        $this->globalService = new GlobalService;
        $this->refKaryawanService = new RefKaryawanService;
    }

    public function actionIndex(Request $request)
    {
        $user_auth = (new \App\Http\Traits\AuthFunction)->getUser();
        $role = $user_auth->group_user[0] ?? null;

        $form_filter_text = $request->form_filter_text ?? '';

        $paramater = [
            'search' => $form_filter_text,
            'is_super_admin' => false
        ];

        $user_level = 0;

        if ($role === 'group_super_admin') {

            $paramater['is_super_admin'] = true;

        } else {

            $get_karyawan = $this->masterPengajuanService
                ->getKaryawan($user_auth->id_user);

            $paramater['id_karyawan'] = $get_karyawan->id_karyawan;
            $paramater['id_ruangan']  = $get_karyawan->id_ruangan;
            $paramater['id_jabatan']  = $get_karyawan->id_jabatan;

            $get_user_lv1 = $this->approverCutiService->userLevel1($get_karyawan->id_jabatan, $get_karyawan->id_ruangan);
            $get_user_lv2 = $this->approverCutiService->userLevel2($get_karyawan->id_jabatan, $get_karyawan->id_ruangan);

            $user_level = $get_user_lv2 ? $get_user_lv2->level : $get_user_lv1->level;
        }

        $list_data = $this->approverCutiService
            ->getListData($paramater)
            ->paginate($request->per_page ?? 10);
        
        return view('persetujuan.cuti.index', [
            'title' => $this->title,
            'breadcrumbs' => $this->breadcrumbs,
            'list_data' => $list_data,
            'user_level' => $user_level ?? 0
        ]);
    }

    public function actionApproved(Request $request) {
        $user_auth = (new \App\Http\Traits\AuthFunction)->getUser();
        $role = $user_auth->group_user[0] ?? null;
        $data_req = $request->all();

        $paramater = [
            'data_req' => $data_req,
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

        $message_default = [
            'success' => 'Cuti berhasil Approve',
            'error' =>'Cuti tidak berhasil Approve',
        ];

        try {
            $this->approverCutiService->approved($paramater);

            return redirect()
                ->route($this->url_name)
                ->with('success', $message_default['success']);

        } catch (\Exception $e) {

            return redirect()
                ->route($this->url_name)
                ->with('error', $e->getMessage());
        }
    }

    public function actionRejected(Request $request) {
        $user_auth = (new \App\Http\Traits\AuthFunction)->getUser();
        $role = $user_auth->group_user[0] ?? null;
        $data_req = $request->all();

        $paramater = [
            'data_req' => $data_req,
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

        $message_default = [
            'success' => 'Cuti berhasil Approve',
            'error' =>'Cuti tidak berhasil Approve',
        ];


        $link_back_redirect = $this->url_name;
        $link_back_param = [];
        $result = $this->approverCutiService->rejected($paramater);

        if($result) {
            $pesan = ['success', $message_default['success'], 2];
        }else {
            $pesan = ['error', $message_default['error'], 3];
        }    

        return redirect()->route($link_back_redirect, $link_back_param)->with([$pesan[0] => $pesan[1]]);
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

        if($paramater['is_super_admin'] == true) {
            $list_data = [];
        } else {
             $list_data = $this->approverCutiService
            ->getListDataByStatus($paramater, 'approved')
            ->paginate($request->per_page ?? 10);
        }
    
        $parameter_view = [
            'title' => $this->title,
            'breadcrumbs' => $this->breadcrumbs,
            'list_data' => $list_data
        ];

        return view('persetujuan.cuti.approved.index', $parameter_view);
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

        $list_data = $this->approverCutiService
            ->getListDataByStatus($paramater, 'rejected')
            ->paginate($request->per_page ?? 10);
    
        $parameter_view = [
            'title' => $this->title,
            'breadcrumbs' => $this->breadcrumbs,
            'list_data' => $list_data
        ];

        return view('persetujuan.cuti.rejected.index', $parameter_view);
    }

}