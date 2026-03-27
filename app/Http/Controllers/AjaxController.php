<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Services\GlobalService;

class AjaxController extends Controller
{
    public $globalService;
    
    public function __construct() {
        $this->globalService = new GlobalService;
    }

    function getListJabatan($request){
        $list_data_tmp=(new \App\Models\RefJabatan)->get();
        
        $list_data=[];
        foreach($list_data_tmp as $value){
            $list_data[]=[
                'kode'=>[
                    'data-item'=>$value->id_jabatan.'@'.$value->nm_jabatan,
                    'value'=>$value->nm_jabatan
                ]
            ];
        }

        $table=[
            'header'=>[
               'title'=> ['Jabatan'],
               'parameter'=>[' class="w-25" ']
            ],
        ];

        $parameter_view=[
            'table'=>$table,
            'list_data'=>!empty($list_data) ? $list_data : ''
        ];

        return [
            'success' => true,
            'html'=>view('ajax.columns_ajax',$parameter_view)->render(),
        ];
    }

    function getListDepartemen($request){
        $list_data_tmp=(new \App\Models\RefDepartemen)->get();
        
        $list_data=[];
        foreach($list_data_tmp as $value){
            $list_data[]=[
                'kode'=>[
                    'data-item'=>$value->id_departemen.'@'.$value->nm_departemen,
                    'value'=>$value->nm_departemen
                ]
            ];
        }
    
        $table=[
            'header'=>[
               'title'=> ['Departemen/Bidang'],
               'parameter'=>[' class="w-25" ']
            ],
        ];
    
        $parameter_view=[
            'table'=>$table,
            'list_data'=>!empty($list_data) ? $list_data : ''
        ];
    
        return [
            'success' => true,
            'html'=>view('ajax.columns_ajax',$parameter_view)->render(),
        ];
    }

    function getListRuangan($request){
        
        $data_sent = !empty($request->data_sent) ? $request->data_sent : '';
        $exp=explode('@',$data_sent);
        $id_departemen=$exp[0];

        $paramater_search=[];
        if($id_departemen){
            $paramater_search['ref_ruangan.id_departemen']=$id_departemen;
        }
        
        $list_data_tmp=(new \App\Services\DataRuanganService)->getList($paramater_search, 1)->get();
        
        $list_data=[];
        foreach($list_data_tmp as $value){
            $list_data[]=[
                $value->nm_departemen,
                'kode'=>[
                    'data-item'=>$value->id_ruangan.'@'.$value->nm_ruangan,
                    'value'=>$value->nm_ruangan
                ]
            ];
        }
    
        $table=[
            'header'=>[
               'title'=> ['Departemen/Bidang','Ruangan'],
               'parameter'=>[' class="w-25" ']
            ],
        ];
    
        $parameter_view=[
            'table'=>$table,
            'list_data'=>!empty($list_data) ? $list_data : ''
        ];
    
        return [
            'success' => true,
            'html'=>view('ajax.columns_ajax',$parameter_view)->render(),
        ];
    }

    function getListStatusKaryawan($request){
        $list_data_tmp=(new \App\Models\RefStatusKaryawan)->get();
        
        $list_data=[];
        foreach($list_data_tmp as $value){
            $list_data[]=[
                'kode'=>[
                    'data-item'=>$value->id_status_karyawan.'@'.$value->nm_status_karyawan,
                    'value'=>$value->nm_status_karyawan
                ]
            ];
        }
    
        $table=[
            'header'=>[
               'title'=> ['Status Karyawan'],
               'parameter'=>[' class="w-25" ']
            ],
        ];
    
        $parameter_view=[
            'table'=>$table,
            'list_data'=>!empty($list_data) ? $list_data : ''
        ];
    
        return [
            'success' => true,
            'html'=>view('ajax.columns_ajax',$parameter_view)->render(),
        ];
    }

    function getListMesihAbsensi($request){
        $list_data_tmp=(new \App\Models\RefMesinAbsensi)->get();
        
        $list_data=[];
        foreach($list_data_tmp as $value){
            $list_data[]=[
                'kode'=>[
                    'data-item'=>$value->id_mesin_absensi.'@'.$value->ip_address.'@'.$value->comm_key.'@'.$value->nm_mesin.'@'.$value->lokasi_mesin,
                    'value'=>$value->ip_address
                ],
                $value->nm_mesin,
                $value->lokasi_mesin,
            ];
        }
    
        $table=[
            'header'=>[
               'title'=> ['Ip Mesin','Nama Mesin','Lokasi Mesin'],
               'parameter'=>[' class="w-15" ',' class="w-25" ',' class="w-30" ']
            ],
        ];
    
        $parameter_view=[
            'table'=>$table,
            'list_data'=>!empty($list_data) ? $list_data : ''
        ];
    
        return [
            'success' => true,
            'html'=>view('ajax.columns_ajax',$parameter_view)->render(),
        ];
    }
    
    function getListKaryawan($request){
        $list_data_tmp = (new \App\Services\RefKaryawanService)->getList([], 1)->get();
        $list_data=[];
        foreach($list_data_tmp as $value){
            $list_data[]=[
                $value->nip,
                'kode'=>[
                    'data-item'=>$value->id_karyawan.'@'.$value->nm_karyawan.'@'.$value->nip.'@'.$value->id_jabatan.'@'.$value->nm_jabatan.'@'.$value->id_departemen.'@'.$value->nm_departemen,
                    'value'=>$value->nm_karyawan
                ],
                $value->nm_jabatan,
                $value->nm_departemen,
                $value->nm_ruangan,
            ];
        }
    
        $table=[
            'header'=>[
               'title'=> ['NIP','Nama','Jabatan','Departemen','Ruangan'],
               'parameter'=>[' class="w-5" ',' class="w-5" ',' class="w-25" ',' class="w-25" ',' class="w-25" ']
            ],
        ];
    
        $parameter_view=[
            'table'=>$table,
            'list_data'=>!empty($list_data) ? $list_data : ''
        ];
    
        return [
            'success' => true,
            'html'=>view('ajax.columns_ajax',$parameter_view)->render(),
        ];
    }

    function getJenisJadwal($request){
        $list_data_tmp=(new \App\Models\RefJenisJadwal)->get();
        
        $list_data=[];
        foreach($list_data_tmp as $value){
            $list_data[]=[
                'kode'=>[
                    'data-item'=>$value->id_jenis_jadwal.'@'.$value->nm_jenis_jadwal,
                    'value'=>$value->nm_jenis_jadwal
                ],
            ];
        }
    
        $table=[
            'header'=>[
               'title'=> ['Nama Jenis Jadwal'],
               'parameter'=>[' class="w-30" ']
            ],
        ];
    
        $parameter_view=[
            'table'=>$table,
            'list_data'=>!empty($list_data) ? $list_data : ''
        ];
    
        return [
            'success' => true,
            'html'=>view('ajax.columns_ajax',$parameter_view)->render(),
        ];
    }

    function getListJenisCuti($request){
        $list_data_tmp = (new \App\Services\JenisCutiService())->getList([], 1)->get();

        $list_data=[];
        foreach($list_data_tmp as $value){
            $list_data[]=[
                'kode'=>[
                    'data-item'=>$value->id.'@'.$value->nama.'@'.$value->jumlah,
                    'value'=>$value->nama
                ],
                $value->jumlah,
            ];
        }
    
        $table=[
            'header'=>[
               'title'=> ['Nama','Jumlah Cuti'],
               'parameter'=>[' class="w-5" ',' class="w-5" ',' class="w-25" ',' class="w-25" ',' class="w-25" ',]
            ],
        ];
    
        $parameter_view=[
            'table'=>$table,
            'list_data'=>!empty($list_data) ? $list_data : ''
        ];
    
        return [
            'success' => true,
            'html'=>view('ajax.columns_ajax',$parameter_view)->render(),
        ];
    }

    function getListJenisCutiKaryawan($request){
        
        $get_user = (new \App\Http\Traits\AuthFunction())->getUser();

        if(!empty($get_user->data_user_sistem)) {
            $karyawan = $get_user->data_user_sistem;
            $list_data_tmp = (new \App\Services\JenisCutiService())->getListJenisCutiKaryawan([], 1, $karyawan->id_karyawan )->get();
        }else {
            $list_data_tmp = (new \App\Services\JenisCutiService())->getList([], 1)->get();
        }
    
        $list_data=[];
        foreach($list_data_tmp as $value){
            $list_data[]=[
                'kode'=>[
                    'data-item'=>$value->id.'@'.$value->nama.'@'.$value->sisa,
                    'value'=>$value->nama
                ],
                $value->sisa,
            ];
        }
    
        $table=[
            'header'=>[
               'title'=> ['Nama','Stok Cuti'],
               'parameter'=>[' class="w-5" ',' class="w-5" ',' class="w-25" ',' class="w-25" ',' class="w-25" ',]
            ],
        ];
    
        $parameter_view=[
            'table'=>$table,
            'list_data'=>!empty($list_data) ? $list_data : ''
        ];
    
        return [
            'success' => true,
            'html'=>view('ajax.columns_ajax',$parameter_view)->render(),
        ];
    }

    function ajax(Request $request){
        $get_req = $request->all();
        $hasil='';
        if(!empty($get_req['action'])){

            if($get_req['action']=='get_list_jabatan'){
                $hasil=$this->getListJabatan($request);
            }
            
            if($get_req['action']=='get_list_departemen'){
                $hasil=$this->getListDepartemen($request);
            }

            if($get_req['action']=='get_list_ruangan'){
                $hasil=$this->getListRuangan($request);
            }

            if($get_req['action']=='get_list_status_karyawan'){
                $hasil=$this->getListStatusKaryawan($request);
            }

            if($get_req['action']=='get_list_mesih_absensi'){
                $hasil=$this->getListMesihAbsensi($request);
            }

            if($get_req['action']=='get_list_karyawan'){
                $hasil=$this->getListKaryawan($request);
            }

            if($get_req['action']=='get_jenis_jadwal'){
                $hasil=$this->getJenisJadwal($request);
            }

            if($get_req['action']=='get_jenis_cuti'){
                $hasil=$this->getListJenisCuti($request);
            }

            if($get_req['action']=='get_jenis_cuti_karyawan'){
                $hasil=$this->getListJenisCutiKaryawan($request);
            }

            if($request->ajax()){
                return response()->json($hasil);
            }
            
        }
    }
}