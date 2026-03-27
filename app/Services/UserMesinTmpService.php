<?php

namespace App\Services;

use Illuminate\Support\Facades\DB;

use App\Models\UserMesinTmp;

class UserMesinTmpService extends BaseService
{
    public $userMesinTmp;
    
    public function __construct(){
        parent::__construct();
        $this->userMesinTmp = new UserMesinTmp;
    }

    function getList($params=[],$type=''){
        
        $query = $this->userMesinTmp
            ->select('user_mesin_tmp.*','ip_address','nm_mesin','lokasi_mesin',
                DB::raw("IF( ref_user_info.id_user, 1, 0 ) as db "),'ref_user_info.name as db_name',

                DB::raw("IF( ref_user_info.id_user, 0, IF( ruiid.id_user, 1, 0 ) ) as duplicate_id "),'ruiid.id_user as duplicate_id_id_user','ruiid.name as duplicate_id_name',
                DB::raw("IF( ref_user_info.id_user, 0, IF( ruiname.id_user, 1, 0 ) ) as duplicate_name "),'ruiname.id_user as duplicate_name_id_user','ruiname.name as duplicate_name_name',
            )
            ->leftJoin('ref_mesin_absensi','ref_mesin_absensi.id_mesin_absensi','=','user_mesin_tmp.id_mesin_absensi')
            ->leftJoin('ref_user_info', function ($join) {
                $join->on(DB::raw('trim( REPLACE( ref_user_info.id_user," ", "") )'), '=',DB::raw('trim( REPLACE( user_mesin_tmp.id_user," ", "") )'));
                $join->on(DB::raw('trim( lower(REPLACE( ref_user_info.name," ", "") ) )'), '=', DB::raw('trim( lower(REPLACE( user_mesin_tmp.name," ", "") ) )'));
            })
            ->leftJoin('ref_user_info as ruiid',DB::raw('trim( REPLACE( ruiid.id_user," ", "") )'),'=',DB::raw('trim( REPLACE( user_mesin_tmp.id_user," ", "") )') )
            ->leftJoin('ref_user_info as ruiname',DB::raw('trim( lower(REPLACE( ruiname.name," ", "") ) )'),'=',DB::raw('trim( lower(REPLACE( user_mesin_tmp.name," ", "") ) )') )
            ->orderBy('user_mesin_tmp.id_mesin_absensi','ASC')
            ->orderBy(DB::raw('if(ref_user_info.id_user,1,0)'),'ASC')
        ;

        $query=DB::table( DB::raw("( ".$query->toSql()." ) as utama") );

        $list_search=[
            'where_or'=>['id_user','name','group','db_name'],
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