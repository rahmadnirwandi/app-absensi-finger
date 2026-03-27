<?php

namespace App\Services;

use Illuminate\Support\Facades\DB;

use App\Models\DataAbsensiKaryawan;
use App\Services\RefJadwalService;

class DataAbsensiKaryawanService extends BaseService
{
    public $dataAbsensiKaryawan,$refJadwalService;

    public function __construct(){
        parent::__construct();
        $this->dataAbsensiKaryawan = new DataAbsensiKaryawan;
        $this->refJadwalService = new RefJadwalService;
    }

    function get_data_by_jadwal_rutin($request){
        $form_filter_text=!empty($request->form_filter_text) ? $request->form_filter_text : '';
        $filter_date_start=!empty($request->filter_date_start) ? $request->filter_date_start : date('Y-m-d');
        $filter_date_end=!empty($request->filter_date_end) ? $request->filter_date_end : date('Y-m-d');

        $filter_id_jabatan=!empty($request->filter_id_jabatan) ? $request->filter_id_jabatan : '';
        $filter_id_departemen=!empty($request->filter_id_departemen) ? $request->filter_id_departemen : '';
        $filter_id_ruangan=!empty($request->filter_id_ruangan) ? $request->filter_id_ruangan : '';

        $filter_status_absensi=!empty($request->filter_status_absensi) ? $request->filter_status_absensi : '';
        $filter_cara_absensi=!empty($request->filter_cara_absensi) ? $request->filter_cara_absensi : '';

        $paramater_data_karyawan_rutin=[
            'search'=>$form_filter_text
        ];

        if(!empty($filter_id_jabatan)){
            $paramater_data_karyawan_rutin['id_jabatan']=$filter_id_jabatan;
        }

        if(!empty($filter_id_departemen)){
            $paramater_data_karyawan_rutin['id_departemen']=$filter_id_departemen;
        }

        $list_data_karyawan_rutin = (new \App\Services\DataPresensiRutinService)->getListKaryawan($paramater_data_karyawan_rutin, 1)->select(DB::raw('group_concat(id_karyawan) as id_karyawan'),DB::raw('group_concat(id_user) as id_user'))->first();
        
        $parameter_first=[
            'tanggal'=>[$filter_date_start,$filter_date_end],
            'list_id_karyawan'=>!empty($list_data_karyawan_rutin->id_karyawan) ? $list_data_karyawan_rutin->id_karyawan : '',
            'list_id_user'=>!empty($list_data_karyawan_rutin->id_user) ? $list_data_karyawan_rutin->id_user : '',
        ];

        // $list_absensi=(new \App\Services\RefDataAbsensiTmpService)->getAbsensiRutin($parameter_first,1)->get();
        // $list_absensi=(new \App\Services\RefDataAbsensiTmpService)->get_data_karyawan_absensi_rutin_query($parameter_first,1)->get();
        
        $list_absensi=[];
        

        $data_jadwal_rutin=[];
        $get_jadwal_rutin = $this->refJadwalService->getList(['ref_jadwal.id_jenis_jadwal'=>1], 1)->orderBy('kd_jadwal','ASC')->get();

        if(!empty($get_jadwal_rutin)){
            foreach($get_jadwal_rutin as $val){
                $data_jadwal_rutin[$val->id_jadwal]=(object)$val->getAttributes();
            }
        }

        $data_absensi=[];
        foreach($list_absensi as $value){
            $hasil=(new \App\Http\Traits\AbsensiFunction)->proses_absensi_rutin($get_jadwal_rutin,$value);
            $hasil=[];
            
            $tgl_filter=trim($value->tanggal);

            if(empty($data_absensi[$tgl_filter]['tgl'])){
                $data_absensi[$tgl_filter]['tgl']=$tgl_filter;
            }

            if(empty($data_absensi[$tgl_filter]['data'][$value->id_user]['data_karyawan'])){
                $paramter_data=(object)[
                    'id_user'=>!empty($value->id_user) ? $value->id_user : '',
                    'username'=>!empty($value->username) ? $value->username : '',
                    'id_karyawan'=>!empty($value->id_karyawan) ? $value->id_karyawan : '',
                    'nm_karyawan'=>!empty($value->nm_karyawan) ? $value->nm_karyawan : '',
                    'alamat'=>!empty($value->alamat) ? $value->alamat : '',
                    'nip'=>!empty($value->nip) ? $value->nip : '',
                    'id_jabatan'=>!empty($value->id_jabatan) ? $value->id_jabatan : '',
                    'nm_jabatan'=>!empty($value->nm_jabatan) ? $value->nm_jabatan : '',
                    'id_departemen'=>!empty($value->id_departemen) ? $value->id_departemen : '',
                    'nm_departemen'=>!empty($value->nm_departemen) ? $value->nm_departemen : '',
                    'id_ruangan'=>!empty($value->id_ruangan) ? $value->id_ruangan : '',
                    'nm_ruangan'=>!empty($value->nm_ruangan) ? $value->nm_ruangan : '',
                ];
                $data_absensi[$tgl_filter]['data'][$value->id_user]['data_karyawan']=$paramter_data;
            }

            if(empty($data_absensi[$tgl_filter]['data'][$value->id_user]['data_jadwal'])){
                $data_absensi[$tgl_filter]['data'][$value->id_user]['data_jadwal']=$data_jadwal_rutin;
            }

            if(empty($data_absensi[$tgl_filter]['data'][$value->id_user]['absensi'][$hasil->id_jadwal])){
                $data_absensi[$tgl_filter]['data'][$value->id_user]['absensi'][$hasil->id_jadwal]=$hasil;
            }

            if($hasil->id_jadwal==0){
                $data_absensi[$tgl_filter]['data'][$value->id_user]['absensi_luar_jadwal'][]=!empty($hasil->jam_absensi) ? $hasil->jam_absensi : '';
            }

            $data_absensi[$tgl_filter]['data'][$value->id_user]['absensi_log'][]=!empty($hasil->jam_absensi) ? $hasil->jam_absensi : '';

        }
        
        return $data_absensi;
    }
}