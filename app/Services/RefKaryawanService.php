<?php

namespace App\Services;

use App\Models\RefKaryawan;
use App\Models\RefUserInfoDetail;
use Illuminate\Support\Facades\DB;

class RefKaryawanService extends BaseService
{
    public $refKaryawan='';
    public $refUserInfoDetail='';

    public function __construct(){
        parent::__construct();
        $this->refKaryawan = new RefKaryawan;
        $this->refUserInfoDetail = new RefUserInfoDetail;
    }

    function getList($params=[],$type=''){
        
        $query = $this->refKaryawan
            ->select('ref_karyawan.*','nm_jabatan','nm_departemen','nm_ruangan','nm_status_karyawan')
            ->Leftjoin('ref_jabatan','ref_jabatan.id_jabatan','=','ref_karyawan.id_jabatan')
            ->Leftjoin('ref_departemen','ref_departemen.id_departemen','=','ref_karyawan.id_departemen')
            ->Leftjoin('ref_ruangan','ref_ruangan.id_ruangan','=','ref_karyawan.id_ruangan')
            ->Leftjoin('ref_status_karyawan','ref_status_karyawan.id_status_karyawan','=','ref_karyawan.id_status_karyawan')
            ->orderBy('nm_karyawan','ASC')
        ;

        $list_search=[
            'where_or'=>['nm_karyawan','alamat','nip','nm_jabatan','nm_departemen'],
        ];

        if($params){
            $query=(new \App\Models\MyModel)->set_where($query,$params,$list_search);
        }
        if(empty($type)){
            return $query->get();
        }else{
            return $query;
        }
    }

    function getList_karyawan($params){
        $query = $this->refKaryawan
        ->select('ref_karyawan.*','nm_jabatan','nm_departemen')
        ->Leftjoin('ref_jabatan','ref_jabatan.id_jabatan','=','ref_karyawan.id_jabatan')
        ->Leftjoin('ref_departemen','ref_departemen.id_departemen','=','ref_karyawan.id_departemen')
        ->where('ref_karyawan.id_karyawan', '=', $params)
        ->get();

        return $query;
    }
    function getList_finger($params){
        $query = $this->refUserInfoDetail;
        return $query;
    }

    function getListKaryawanJadwal($params=[],$type=''){
        
        $query = $this->refKaryawan
            ->select('ref_karyawan.*','nm_jabatan','nm_departemen','nm_ruangan','ref_jenis_jadwal.id_jenis_jadwal','ref_karyawan_jadwal_shift.id_template_jadwal_shift','nm_jenis_jadwal','ref_karyawan_user.id_user',DB::raw("IF( ref_karyawan_user.id_user, 1, 0 ) as status_akun_mesin "))
            ->Leftjoin('ref_jabatan','ref_jabatan.id_jabatan','=','ref_karyawan.id_jabatan')
            ->Leftjoin('ref_departemen','ref_departemen.id_departemen','=','ref_karyawan.id_departemen')
            ->Leftjoin('ref_ruangan','ref_ruangan.id_ruangan','=','ref_karyawan.id_ruangan')
            ->Leftjoin('ref_karyawan_jadwal','ref_karyawan_jadwal.id_karyawan','=','ref_karyawan.id_karyawan')
            ->Leftjoin('ref_jenis_jadwal','ref_jenis_jadwal.id_jenis_jadwal','=','ref_karyawan_jadwal.id_jenis_jadwal')
            ->Leftjoin('ref_karyawan_user','ref_karyawan_user.id_karyawan','=','ref_karyawan.id_karyawan')
            ->Leftjoin('ref_karyawan_jadwal_shift','ref_karyawan_jadwal_shift.id_karyawan','=','ref_karyawan.id_karyawan')
            ->orderBy('nm_karyawan','ASC')
        ;

        $list_search=[
            'where_or'=>['nm_karyawan','alamat','nip','nm_jabatan','nm_departemen'],
        ];

        if($params){
            $query=(new \App\Models\MyModel)->set_where($query,$params,$list_search);
        }
        if(empty($type)){
            return $query->get();
        }else{
            return $query;
        }
    }

    public function getRoleKaryawan($id_user) {
        $query = DB::table('uxui_auth_users as au')
                ->select('ag.name')
                ->leftJoin('uxui_auth_group as ag', 'au.alias_group', '=', 'ag.alias')
                ->where('au.id_user', '=', $id_user)
                ->first();

        return $query;
    }
}