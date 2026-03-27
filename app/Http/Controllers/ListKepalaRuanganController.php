<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

use App\Services\GlobalService;
use App\Http\Traits\GlobalFunction;
use App\Services\ListKepalaRuanganService;

class ListKepalaRuanganController extends Controller
{
    public $part_view, $url_index, $url_name, $title, $breadcrumbs, $globalService, $globalFunction, $listKepalaRuanganService;

    public function __construct()
    {
        $router_name = (new \App\Http\Traits\GlobalFunction)->getRouterIndex();
        $this->part_view = $router_name->path_base;
        $this->url_index = $router_name->uri;
        $this->url_name = $router_name->router_name;

        $this->title = 'List Kepala Ruangan';
        $this->breadcrumbs = [
            ['title' => 'Data Karyawan', 'url' => url('/') . "/sub-menu?type=4"],
            ['title' => $this->title, 'url' => url('/') . "/" . $this->url_index],
        ];

        $this->globalService = new GlobalService;
        $this->globalFunction = new GlobalFunction;
        $this->listKepalaRuanganService = new ListKepalaRuanganService;
    }

    function actionIndex(Request $request)
    {
        $form_filter_text = !empty($request->form_filter_text) ? $request->form_filter_text : '';
        $filter_id_departemen = !empty($request->filter_id_departemen) ? $request->filter_id_departemen : '';
        $filter_id_ruangan = !empty($request->filter_id_ruangan) ? $request->filter_id_ruangan : '';

        $paramater=[
            'search' => !empty($form_filter_text) ? $form_filter_text : '',
        ];

        if($filter_id_departemen){
            $paramater['ref_ruangan.id_departemen']=$filter_id_departemen;
        }

        if($filter_id_ruangan){
            $paramater['ref_ruangan.id_ruangan']=$filter_id_ruangan;
        }

        $list_data=(new \App\Services\ListKepalaRuanganService())->getList($paramater,1)->paginate(!empty($request->per_page) ? $request->per_page : 15);

        $parameter_view = [
            'title' => $this->title,
            'breadcrumbs' => $this->breadcrumbs,
            'list_data' => $list_data,
        ];

        return view($this->part_view . '.index', $parameter_view);
    }

    public function actionSetting(Request $request)
    {
        if($request->isMethod('get')){
            return $this->form($request);
        }
        if($request->isMethod('post')){
            return $this->proses($request);
        }
        if($request->isMethod('delete')){
            return $this->delete($request);
        }
    }

    public function form(Request $request)
    {
        if(empty($request->data_sent)){
            return response()->json([], 404);
        }
        $data = json_decode($request->data_sent, true);

        $nm_departemen = !empty($data['nm_departemen']) ? $data['nm_departemen'] : '';
        $id_ruangan = !empty($data['id_ruangan']) ? $data['id_ruangan'] : '';
        $nm_ruangan = !empty($data['nm_ruangan']) ? $data['nm_ruangan'] : '';

        $actionForm = url("/".$this->url_index."/".'setting');
        $parameter_modal = [
            'actionForm' => $actionForm,
            'nm_departemen' => $nm_departemen,
            'id_ruangan' => $id_ruangan,
            'nm_ruangan' => $nm_ruangan
        ];

        $returnHTML = view($this->part_view ."./form", $parameter_modal)->render();
        return response()->json(array('success' => true, 'html'=>$returnHTML));
    }

    private function proses($request)
    {
        $req = $request->all();
        $kode = !empty($req['id_karyawan']) ? $req['id_karyawan'] : '';
        $link_back_redirect = $this->url_name;
        DB::beginTransaction();
        $pesan = [];
        $link_back_param = [];
        $link_back_param = array_merge($link_back_param, $request->all());
        $message_default = [
            'success' => !empty($kode) ? 'Data berhasil diubah' : 'Data berhasil disimpan',
            'error' => !empty($kode) ? 'Data tidak berhasil diubah' : 'Data berhasil disimpan'
        ];

        try {
            $model = (new \App\Models\ListKepalaRuangan)->where('id_karyawan', '=', $kode)->first();
            if ($model) {
                return redirect()->route($link_back_redirect, $link_back_param)->with(['error' => 'Karyawan ini sudah menjadi Kepala Ruangan']);
            }
            $model = (new \App\Models\ListKepalaRuangan);
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
            }
            $pesan = ['error', $message_default['error'], 3];
        } catch (\Throwable $e) {
            DB::rollBack();
            $pesan = ['error', $message_default['error'], 3];
        }

        return redirect()->route($link_back_redirect, $link_back_param)->with([$pesan[0] => $pesan[1]]);
    }

    function delete(Request $request)
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
            $model = (new \App\Models\ListKepalaRuangan())->where('id_karyawan', '=', $kode)->first();
            if (empty($model)) {
                return redirect()->route($this->url_name, $link_back_param)->with(['error', 'Data tidak ditemukan']);
            }

            $is_save = 0;
            $model = (new \App\Models\ListKepalaRuangan())->where('id_karyawan', '=', $kode);
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

    public function ajax(Request $request){
        $search = $request->search;
        $paramater=[
            'search' => !empty($search) ? $search : '',
        ];
        $data = (new \App\Services\RefKaryawanService)->getList($paramater, 1)->limit(10)->get();
        return response()->json($data,200);
    }
}