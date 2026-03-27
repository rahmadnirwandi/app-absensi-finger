<?php

namespace App\Http\Controllers;

use App\Services\GlobalService;
use App\Services\RefKaryawanService;
use Illuminate\Http\Request;
use App\Services\ApprovalSettingLevel2Service;

class ApprovalSettingLevel2Controller extends Controller
{
    public $part_view, $url_index, $url_name, $title, $breadcrumbs, $globalService, $approvalSettingLevel2Service;
    protected $masterPengajuanService;
    public function __construct()
    {
        $router_name = (new \App\Http\Traits\GlobalFunction)->getRouterIndex();
        $this->part_view = $router_name->path_base;
        $this->url_index = $router_name->uri;
        $this->url_name = $router_name->router_name;
        $this->approvalSettingLevel2Service = new ApprovalSettingLevel2Service;

        $this->title = 'Approval Level 2';
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


        $list_data = $this->approvalSettingLevel2Service
            ->getAllLevel2($paramater)
            ->paginate($request->per_page ?? 10);
    
        $parameter_view = [
            'title' => $this->title,
            'breadcrumbs' => $this->breadcrumbs,
            'list_data' => $list_data
        ];

        return view('approval-setting.level2.index', $parameter_view);
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
            $this->approvalSettingLevel2Service->insert($data_req);

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
            'action_form' => 'approval-setting-level2/create',
        ];

        return view('approval-setting.level2.form', $parameter_view);
    }

    public function formUpdate(Request $request) {
         if(empty($request->data_sent)){
            return response()->json([], 404);
        }
        $data = json_decode($request->data_sent, true);

        $id_approval_mapping = !empty($data['id_approval_mapping']) ? $data['id_approval_mapping'] : '';

        $data = $this->approvalSettingLevel2Service->getApprovalMappings($id_approval_mapping);
        
        $parameter_modal = [
            'action_update' => '/approval-setting-level2/update',
            'data' => $data
        ];

        $returnHTML = view('approval-setting.level2.update', $parameter_modal)->render();
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
            $this->approvalSettingLevel2Service->update($data_req);

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
            $this->approvalSettingLevel2Service->delete($data_req);

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
