<?php

namespace App\Services;

use App\Models\RefRuangan;

class DataRuanganService extends BaseService
{
    public $refRuangan;
    
    public function __construct(){
        parent::__construct();
        $this->refRuangan = new RefRuangan();
    }

    function getList($params=[],$type=''){
        
        $query = $this->refRuangan
            ->select('ref_ruangan.*','nm_departemen')
            ->Leftjoin('ref_departemen','ref_departemen.id_departemen','=','ref_ruangan.id_departemen')
            ->orderBy('ref_ruangan.nm_ruangan','ASC')
        ;

        $list_search=[
            'where_or'=>['nm_ruangan','nm_departemen'],
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