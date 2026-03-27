<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

use App\Services\GlobalService;
use App\Services\RefKaryawanService;

class DataJadwalKaryawanController extends \App\Http\Controllers\MyAuthController
{
    public $part_view, $url_index, $url_name, $title, $breadcrumbs, $globalService;
    public $refKaryawanService;

    public function __construct()
    {
        $router_name = (new \App\Http\Traits\GlobalFunction)->getRouterIndex();
        $this->part_view = $router_name->path_base;
        $this->url_index = $router_name->uri;
        $this->url_name = $router_name->router_name;

        $this->title = 'Data Jadwal Karyawan';
        $this->breadcrumbs = [
            ['title' => 'Data Karyawan', 'url' => url('/') . "/sub-menu?type=4"],
            ['title' => $this->title, 'url' => url('/') . "/" . $this->url_index],
        ];

        $this->globalService = new GlobalService;
        $this->refKaryawanService = new RefKaryawanService;
    }

    function actionIndex(Request $request)
    {

        $form_filter_text = !empty($request->form_filter_text) ? $request->form_filter_text : '';
        $filter_id_jabatan = !empty($request->filter_id_jabatan) ? $request->filter_id_jabatan : '';
        $filter_id_departemen = !empty($request->filter_id_departemen) ? $request->filter_id_departemen : '';
        $filter_id_ruangan = !empty($request->filter_id_ruangan) ? $request->filter_id_ruangan : '';
        $form_jenis_jadwal = !empty($request->form_jenis_jadwal) ? $request->form_jenis_jadwal : '';
        $form_connect_mesin = !empty($request->form_connect_mesin) ? $request->form_connect_mesin : '';

        if($request->isMethod('post')){
            return $this->proses_jadwal($request);
        }

        $paramater=[
            'search' => $form_filter_text
        ];

        if($filter_id_jabatan){
            $paramater['ref_karyawan.id_jabatan']=$filter_id_jabatan;
        }

        if($filter_id_departemen){
            $paramater['ref_karyawan.id_departemen']=$filter_id_departemen;
        }

        if($filter_id_ruangan){
            $paramater['ref_karyawan.id_ruangan']=$filter_id_ruangan;
        }

        if($form_jenis_jadwal){
            if($form_jenis_jadwal=='non'){
                $paramater['where_raw']=[ 
                    'ref_jenis_jadwal.id_jenis_jadwal'=>['is null',1],
                    'ref_karyawan_jadwal_shift.id_template_jadwal_shift'=>['is null',1],
                 ];
            }else{
                $exp=explode('@',$form_jenis_jadwal);
                $type_jadwal=!empty($exp[0]) ? $exp[0] : 0;
                $id_jenis_jadwal=!empty($exp[1]) ? $exp[1] : 0;
                if($type_jadwal==1){
                    $paramater['ref_jenis_jadwal.id_jenis_jadwal']=$id_jenis_jadwal;
                }else if($type_jadwal==2){
                    $paramater['ref_karyawan_jadwal_shift.id_template_jadwal_shift']=$id_jenis_jadwal;
                }
            }
        }

        if($form_connect_mesin){
            if($form_connect_mesin==1){
                $paramater['where_raw']=[ 'ref_karyawan_user.id_user'=>['is not null',1] ];
            }else{
                $paramater['where_raw']=[ 'ref_karyawan_user.id_user'=>['is null',1] ];
            }
        }

        $list_data = $this->refKaryawanService->getListKaryawanJadwal($paramater, 1)->paginate(!empty($request->per_page) ? $request->per_page : 15);

        $data_jadwal=[];
        $data_jadwal_rutin=( new \App\Models\RefJenisJadwal() )->where('id_jenis_jadwal','=',1)->first();
        
        if(!empty($data_jadwal_rutin)){
            $data_jadwal[]=[
                'key'=>$data_jadwal_rutin->id_jenis_jadwal,
                'value'=>$data_jadwal_rutin->id_jenis_jadwal,
                'text'=>$data_jadwal_rutin->nm_jenis_jadwal,
            ];
        }

        $data_jadwal_shift=( new \App\Models\RefTemplateJadwalShift() )->get();
        if(!empty($data_jadwal_shift)){
            foreach($data_jadwal_shift as $value){
                $data_jadwal[]=[
                    'key'=>2,
                    'value'=>$value->id_template_jadwal_shift,
                    'text'=>$value->nm_shift,
                ];
            }
        }

        $data_jadwal_tmp=$data_jadwal;

        $data_jadwal_json=[];
        $data_jadwal_json[]=[
            'key'=>0,
            'value'=>0,
            'text'=>'-',
        ];

        foreach($data_jadwal_tmp as $value){
            $value=(object)$value;

            $data_jadwal_json[]=[
                // 'key'=>$value->key,
                // 'value'=>$value->value,
                'value'=>$value->key.'@'.$value->value,
                'text'=>$value->text,
            ];
        }

        if(!empty($data_jadwal_json)){
            $data_jadwal_json=json_encode($data_jadwal_json);
        }

        $parameter_view = [
            'title' => $this->title,
            'breadcrumbs' => $this->breadcrumbs,
            'list_data' => $list_data,
            'data_jadwal'=>$data_jadwal,
            'data_jadwal_json'=>$data_jadwal_json,
        ];

        return view($this->part_view . '.index', $parameter_view);
    }

    private function proses_jadwal($request){
        $req = $request->all();
        $id_karyawan = !empty($req['pk']) ? $req['pk'] : '';
        $params = !empty($req['value']) ? $req['value'] : '';
        DB::beginTransaction();
        $pesan = [];

        $message_default = [
            'success' => 'Data berhasil diubah',
            'error' => 'Data tidak berhasil diubah'
        ];

        if ($request->ajax()) {
            try {
                $is_save=0;
                $exp=explode('@',$params);
                $type_jadwal=!empty($exp[0]) ? $exp[0] : 0;
                $id_jenis_jadwal=!empty($exp[1]) ? $exp[1] : 0;

                if(empty($type_jadwal) ){
                    
                    $model_rutin=( new \App\Models\RefKaryawanJadwalRutin() )->where('id_karyawan','=',$id_karyawan)->first();
                    if($model_rutin){
                        if ($model_rutin->delete()) {
                            $is_save = 1;
                        }
                    }

                    $model_shift=( new \App\Models\RefKaryawanJadwalShift() )->where('id_karyawan','=',$id_karyawan)->first();
                    if($model_shift){
                        if ($model_shift->delete()) {
                            $is_save = 1;
                        }
                    }

                }else if($type_jadwal==1){
                    $model_shift=( new \App\Models\RefKaryawanJadwalShift() )->where('id_karyawan','=',$id_karyawan)->first();
                    if($model_shift){
                        $model_shift->delete();
                    }

                    $model=( new \App\Models\RefKaryawanJadwalRutin() )->where('id_karyawan','=',$id_karyawan)->first();
                    if(empty($model)){
                        $model=new \App\Models\RefKaryawanJadwalRutin();
                        $model->id_karyawan=$id_karyawan;
                    }

                    $model->id_jenis_jadwal=$id_jenis_jadwal;

                    if ($model->save()) {
                        $is_save = 1;
                    }
                
                }else if($type_jadwal==2){
                    $model_rutin=( new \App\Models\RefKaryawanJadwalRutin() )->where('id_karyawan','=',$id_karyawan)->first();
                    if($model_rutin){
                        $model_rutin->delete();
                    }

                    $model=( new \App\Models\RefKaryawanJadwalShift() )->where('id_karyawan','=',$id_karyawan)->first();
                    if(empty($model)){
                        $model=new \App\Models\RefKaryawanJadwalShift();
                        $model->id_karyawan=$id_karyawan;
                    }
                    $model->id_template_jadwal_shift=$id_jenis_jadwal;

                    if ($model->save()) {
                        $is_save = 1;
                    }
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

            return response()->json($pesan);
        }
    }
}