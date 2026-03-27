<?php

namespace App\Services;

use Illuminate\Support\Facades\DB;

use App\Models\UserPresensi;

class UserPresensiService extends BaseService
{
    public $userPresensi;
    
    public function __construct(){
        parent::__construct();
        $this->userPresensi = new UserPresensi;
    }

    function getList($params=[],$type=''){
        
        $query = $this->userPresensi
        ->select('*')
        ->join('ref_mesin_absensi', 'ref_mesin_absensi.id_mesin_absensi', '=', 'user_presensi.id_mesin_absensi')
        ->leftJoin('ref_karyawan', 'ref_karyawan.id_karyawan', '=', 'user_presensi.id_user');

        $list_search=[
            'where_or'=>['name','nm_karyawan','nip','utama.id_user'],
        ];

        $list_search=[
            'where_or'=>['user_presensi.id_user'],
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