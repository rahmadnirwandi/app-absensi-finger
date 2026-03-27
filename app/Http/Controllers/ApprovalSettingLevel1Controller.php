<?php

namespace App\Http\Controllers;

use App\Services\GlobalService;
use App\Services\RefKaryawanService;
use Illuminate\Http\Request;
use App\Services\ApprovalSettingLevel1Service;

class ApprovalSettingLevel1Controller extends Controller
{
    public $part_view, $url_index, $url_name, $title, $breadcrumbs, $globalService, $approvalSettingLevel1Service;
    protected $masterPengajuanService;
    public function __construct()
    {
        $router_name = (new \App\Http\Traits\GlobalFunction)->getRouterIndex();
        $this->part_view = $router_name->path_base;
        $this->url_index = $router_name->uri;
        $this->url_name = $router_name->router_name;
        $this->approvalSettingLevel1Service = new ApprovalSettingLevel1Service;

        $this->title = 'Approval Level 1';
        $this->breadcrumbs = [
            ['title' => 'Data Karyawan', 'url' => url('/') . "/sub-menu?type=4"],
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


        $list_data = $this->approvalSettingLevel1Service
            ->getAllLevel1($paramater)
            ->paginate($request->per_page ?? 10);
    
        $parameter_view = [
            'title' => $this->title,
            'breadcrumbs' => $this->breadcrumbs,
            'list_data' => $list_data
        ];

        return view('approval-setting.level1.index', $parameter_view);
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
            'success' => 'Approval Berhasil Disimpan',
            'error'   => 'Approval Tidak Berhasil Disimpan',
        ];

        try {
            $this->approvalSettingLevel1Service->insert($data_req);

            return redirect()
                ->route($this->url_name)
                ->with('success', $message_default['success']);

        } catch (\Exception $e) {

            return redirect()
                ->route($this->url_name)
                ->with('error', $e->getMessage());
        }
    }

    public function formCreate(Request $request) {
    
        $parameter_view = [
            'action_form' => 'approval-setting-level1/create',
        ];

        return view('approval-setting.level1.form', $parameter_view);
    }

    public function formUpdate(Request $request) {
         if(empty($request->data_sent)){
            return response()->json([], 404);
        }
        $data = json_decode($request->data_sent, true);

        $id_approval_mapping = !empty($data['id_approval_mapping']) ? $data['id_approval_mapping'] : '';

        $data = $this->approvalSettingLevel1Service->getApprovalMappings($id_approval_mapping);
        
        $parameter_modal = [
            'action_update' => '/approval-setting-level1/update',
            'data' => $data
        ];

        $returnHTML = view('approval-setting.level1.update', $parameter_modal)->render();
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
            'success' => 'Approval Berhasil Diubah',
            'error'   => 'Approval tidak berhasil diubah',
        ];

        try {
            $this->approvalSettingLevel1Service->update($data_req);

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
            'success' => 'Approval Berhasil dihapus',
            'error' =>'Approval tidak berhasil dihapus',
        ];
        
        try {
            $this->approvalSettingLevel1Service->delete($data_req);

            return redirect()
                ->route($this->url_name)
                ->with('success', $message_default['success']);

        } catch (\Exception $e) {

            return redirect()
                ->route($this->url_name)
                ->with('error', $e->getMessage());
        }
    }
}
