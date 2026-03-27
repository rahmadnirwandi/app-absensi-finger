<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

use App\Services\GlobalService;
use App\Services\RefUserInfoService;
use Illuminate\Support\Facades\Log;

class DataUserMesinController extends \App\Http\Controllers\MyAuthController
{

    public $part_view, $url_index, $url_name, $title, $breadcrumbs, $globalService;
    public $refUserInfoService;

    public function __construct()
    {
        $router_name = (new \App\Http\Traits\GlobalFunction)->getRouterIndex();
        $this->part_view = $router_name->path_base;
        $this->url_index = $router_name->uri;
        $this->url_name = $router_name->router_name;

        $this->title = 'Data User Mesin';
        $this->breadcrumbs = [
            ['title' => 'Mesin Absensi', 'url' => url('/') . "/sub-menu?type=5"],
            ['title' => $this->title, 'url' => url('/') . "/" . $this->url_index],
        ];

        $this->globalService = new GlobalService;
        $this->refUserInfoService = new RefUserInfoService;
    }

    function actionIndex(Request $request){

        $form_filter_text = !empty($request->form_filter_text) ? $request->form_filter_text : '';
        $filter_id_mesin = !empty($request->filter_id_mesin) ? $request->filter_id_mesin : '';
        $filter_id_jabatan = !empty($request->filter_id_jabatan) ? $request->filter_id_jabatan : '';
        $filter_id_departemen = !empty($request->filter_id_departemen) ? $request->filter_id_departemen : '';

        $paramater=[
            'search' => $form_filter_text,
        ];

        if(!empty($filter_id_jabatan)){
            $paramater['id_jabatan']=$filter_id_jabatan;
        }

        if(!empty($filter_id_departemen)){
            $paramater['id_departemen']=$filter_id_departemen;
        }
    
        $list_data = $this->refUserInfoService->getList($paramater, 1)->orderBy(DB::raw("CONVERT ( REPLACE (utama.id_user, '-', '' ), UNSIGNED INTEGER )"),'ASC')->paginate(!empty($request->per_page) ? $request->per_page : 30);
        
        $params_json=json_encode($request->all());
        $parameter_view = [
            'title' => $this->title,
            'breadcrumbs' => $this->breadcrumbs,
            'list_data' => $list_data,
            'params_json'=>$params_json,
        ];

        return view($this->part_view . '.index', $parameter_view);
    }

    private function form(Request $request)
    {
        $kode = !empty($request->data_sent) ? $request->data_sent : '';
        $id_user=$kode;
        $paramater = [
            'utama.id_user' => $id_user
        ];
        
        $model = $this->refUserInfoService->getList($paramater, 1)->first();
        $action_form = $this->part_view . '/update';

        $paramater_url_back_tmp=!empty($request->data_respon) ? $request->data_respon : '';
        
        $parameter_view = [
            'action_form' => $action_form,
            'model' => $model,
            'paramater_url_back_tmp'=>$paramater_url_back_tmp,
        ];

        return view($this->part_view . '.form', $parameter_view);
    }

    function actionUpdate(Request $request){
        if ($request->isMethod('get')) {
            $bagan_form = $this->form($request);

            $paramater_url_back_tmp=!empty($request->data_respon) ? $request->data_respon : '';
            $paramater_url_back_tmp=!empty($paramater_url_back_tmp) ? json_decode($paramater_url_back_tmp,1) : '';
            
            $paramater_url_back=[];
            if(!empty($paramater_url_back_tmp)){
                foreach($paramater_url_back_tmp as $key => $value){
                    $paramater_url_back[]=$key.'='.$value;
                }
            }
            if(!empty($paramater_url_back)){
                $paramater_url_back=implode('&',$paramater_url_back);
            }else{
                $paramater_url_back='';
            }

            $parameter_view = [
                'title' => $this->title,
                'breadcrumbs' => $this->breadcrumbs,
                'bagan_form' => $bagan_form,
                'url_back' => $this->url_name,
                'paramater_url_back'=>$paramater_url_back
            ];

            return view('layouts.index_bagan_form', $parameter_view);
        }

        if ($request->isMethod('post')) {
            return $this->proses($request);
        }
    }

    private function proses($request){
        $req = $request->all();
        $kode = !empty($req['data_sent']) ? $req['data_sent'] : '';
        $link_back_redirect = $this->url_name . '/update';
        DB::beginTransaction();
        $pesan = [];

        $param_respon = !empty($req['params_respon']) ? $req['params_respon'] : '';

        $link_back_param = [
            'data_sent' => $kode,
            'data_respon'=>$param_respon
        ];
        
        $message_default = [
            'success' => 'Data berhasil diubah',
            'error' => 'Data tidak berhasil diubah'
        ];

        try {
            $id_user = !empty($req['id_user']) ? $req['id_user'] : '';
            $id_karyawan_old = !empty($req['id_karyawan_old']) ? $req['id_karyawan_old'] : '';
            $id_karyawan = !empty($req['id_karyawan']) ? $req['id_karyawan'] : '';
            
            if($id_karyawan_old){
                $model = (new \App\Models\RefKaryawanUser)
                ->where('id_user', '=', $id_user)
                ->where('id_karyawan', '=', $id_karyawan_old)
                ->first();
            }
            if (empty($model)) {
                $model = (new \App\Models\RefKaryawanUser);
            }
            $data_save = $req;
            $model->set_model_with_data($data_save);
            $data_old=(object)(!empty($model->getOriginal()) ? $model->getOriginal() : []);
            $data_new=(object)(!empty($model->getAttributes()) ? $model->getAttributes() : []);

            $proses_delete=0;
            if(!empty($data_old->id_karyawan)){
                if(empty($data_new->id_karyawan)){
                    $proses_delete=1;
                }
            }

            $is_save=0;
            if(!empty($proses_delete)){
                if ($model->delete()) {
                    $is_save = 1;
                }
            }else{
                if ($model->save()) {
                    $is_save = 1;
                }    
            }
        
            if ($is_save) {
//                DB::commit();
//                $pesan = ['success', $message_default['success'], 2];

                DB::table('ref_karyawan_jadwal')
                    ->where('id_karyawan', $id_karyawan)
                    ->delete();

                if (!empty($id_karyawan)) {

                    DB::table('ref_karyawan_jadwal')->insert([
                        'id_karyawan'     => $id_karyawan,
                        'id_jenis_jadwal' => 1,
                    ]);
                }

                DB::commit();
                $pesan = ['success', $message_default['success'], 2];
            } else {
                DB::rollBack();
                $pesan = ['error', $message_default['error'], 3];
            }
        } catch (\Illuminate\Database\QueryException $e) {
            DB::rollBack();
            Log::info($e->getMessage());
            if ($e->errorInfo[1] == '1062') {
            }
            $pesan = ['error', $message_default['error'], 3];
        } catch (\Throwable $e) {
            DB::rollBack();
            $pesan = ['error', $message_default['error'], 3];
        }

        return redirect()->route($link_back_redirect, $link_back_param)->with([$pesan[0] => $pesan[1]]);
    }

    function actionDelete(Request $request){
        $kode = !empty($request->data_sent) ? $request->data_sent : '';
        $exp=explode('@',$kode);
        $id_user=!empty($exp[0]) ? $exp[0] : '';

        DB::beginTransaction();
        $pesan = [];
        $link_back_param = [];
        $message_default = [
            'success' => 'Data berhasil dihapus',
            'error' => 'Maaf data tidak berhasil dihapus'
        ];

        $kode = !empty($request->data_sent) ? $request->data_sent : null;

        try {
            $model = (new \App\Models\RefUserInfo)->where('id_user', '=', $id_user)->first();
            if (empty($model)) {
                return redirect()->route($this->url_name, $link_back_param)->with(['error', 'Data tidak ditemukan']);
            }

            $is_save = 0;
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
}