<?php

namespace App\Services\UserManagement;

use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;

use App\Models\UserManagement\UxuiAuthUsers;

class UserAksesAppService extends \App\Services\BaseService
{
    public $uxuiAuthUsers;
    
    public function __construct() {
        $this->uxuiAuthUsers = new UxuiAuthUsers;
    }

    public function insert($data)
    {
        foreach ($data as $key => $field) {
            $field == null ? $data[$key] = "" : "";
        }
        return $this->uxuiAuthUsers->insert($data);
    }

    function update($params, $set, $type = '')
    {
        $query = $this->uxuiAuthUsers;
        foreach ($params as $key => $value) {
            if ($type == 'raw') {
                $query = $query->whereRaw($key . ' = ? ', [$value]);
            } else {
                $query = $query->where($key, '=', $value);
            }
        }
        return $query->update($set);
    }

    function delete($params,$type='')
    {
        $query=$this->uxuiAuthUsers;
        foreach($params as $key => $value){
            if($type=='raw'){
                $query=$query->whereRaw($key.' = ? ', [$value]);
            }else{
                $query=$query->where($key,'=',$value);
            }
        }
        return $query->delete();
    }

    public function getList($params,$type_return=null){
        $query=DB::table(DB::raw('(select utama.id_user,id_user_,pasword_,if(kd_dokter is not null, nm_dokter, if(nip is not null, nama, null)) nama,
        if(kd_dokter is not null, \'dokter\', if(nip is not null, \'petugas\', null)) status,
        alias_group
         from
        (SELECT
            AES_DECRYPT( id_user, \'nur\' ) id_user_,
            AES_DECRYPT( password, \'windi\' ) pasword_,
            id_user,
            password
        FROM
            user
        )utama
            left join dokter dokter on dokter.kd_dokter=utama.id_user_
            left join petugas petugas on petugas.nip=utama.id_user_
            left join uxui_auth_users on uxui_auth_users.id=utama.id_user_) utama'))
        ;

        if($params){
            foreach($params as $key =>$value){

                if($key!='search' and $key!='raw'){
                    $type=is_numeric($value) ? '=' : 'like';
                    if(!empty($value)){
                        $query->where($key,$type,$value);
                    }
                }
            }

            if(!empty($params['raw'])){
                $where_raw=$params['raw'];
                foreach($where_raw as $key => $value){
                    $query->whereRaw($key.' = ? ', [$value]);
                }
            }

            if(!empty($params['search'])){
                $search=$params['search'];
                $query->where(function ($qb2) use ($search) {
                    $qb2->where('id_user_', 'LIKE', '%' . $search . '%')
                        ->orWhere('nama', 'LIKE', '%' . $search . '%')
                    ;
                });
            }
        }

        if(!empty($type_return)){
            return $query;
        }else{
            return $query->get();
        }

        return $query->get();
    }
}