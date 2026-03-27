<?php

namespace App\Services;

use App\Models\ListKabid;
use Illuminate\Support\Facades\DB;

class ListKabidService extends BaseService
{
    public $listKabid='';

    public function __construct(){
        parent::__construct();
        $this->listKabid = new ListKabid;
    }

    function getList($params=[],$type=''){
        $query = $this->listKabid
            ->select('ref_list_kabid.id_karyawan','ref_karyawan.nm_karyawan','ref_karyawan.nip','nm_jabatan','nm_ruangan', 'ref_departemen.nm_departemen', DB::raw("GROUP_CONCAT(ref_ruangan.nm_ruangan) as list_ruangan"), DB::raw("GROUP_CONCAT(ref_ruangan.id_ruangan) as list_ruang_id"))
            ->join('ref_karyawan','ref_karyawan.id_karyawan','=','ref_list_kabid.id_karyawan')
            ->join('ref_ruangan','ref_ruangan.id_ruangan','=','ref_list_kabid.ruangan')
            ->Leftjoin('ref_departemen','ref_departemen.id_departemen','=','ref_karyawan.id_departemen')
            ->Leftjoin('ref_jabatan','ref_jabatan.id_jabatan','=','ref_karyawan.id_jabatan')
            ->orderBy('ref_list_kabid.id_karyawan','ASC')
        ;

        $list_search=[
            'where_or'=>['ref_list_kabid.id_karyawan', 'ref_karyawan.nm_karyawan','ref_karyawan.alamat','nip'],
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
}