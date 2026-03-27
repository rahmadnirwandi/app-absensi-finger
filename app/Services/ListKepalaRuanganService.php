<?php

namespace App\Services;

use App\Models\RefRuangan;
use App\Models\RefKaryawan;

class ListKepalaRuanganService extends BaseService
{
    public $refRuangan, $refKaryawan;
    
    public function __construct(){
        parent::__construct();
        $this->refRuangan = new RefRuangan();
        $this->refKaryawan = new RefKaryawan;
    }

    function getList($params=[],$type=''){
        
        $query = $this->refRuangan
            ->select('ref_ruangan.*','nm_departemen','ref_list_kepala_ruangan.id_karyawan','ref_karyawan.nm_karyawan')
            ->leftJoin('ref_departemen','ref_departemen.id_departemen','=','ref_ruangan.id_departemen')
            ->leftJoin('ref_list_kepala_ruangan','ref_list_kepala_ruangan.ruangan','=','ref_ruangan.id_ruangan')
            ->leftJoin('ref_karyawan','ref_karyawan.id_karyawan','=','ref_list_kepala_ruangan.id_karyawan')
            ->orderBy('ref_ruangan.nm_ruangan','ASC')
        ;

        $list_search=[
            'where_or'=>['nm_ruangan','nm_departemen','ref_karyawan.nm_karyawan'],
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