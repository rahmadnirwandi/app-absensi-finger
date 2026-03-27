<?php

namespace App\Services\UserManagement;

use Illuminate\Support\Facades\DB;

use App\Models\UserManagement\UxuiAuthPermission;
use Illuminate\Support\Str;

class PermissionGroupAppService extends \App\Services\BaseService
{
    public $uxuiAuthPermission;

    public function __construct(){
       $this->uxuiAuthPermission = new UxuiAuthPermission;
    }

    function updateAuthPermission($params,$set,$type='')
    {
        $query=$this->uxuiAuthPermission;
        foreach($params as $key => $value){
            if($type=='raw'){
                $query=$query->whereRaw($key.' = ? ', [$value]);
            }else{
                $query=$query->where($key,'=',$value);
            }
        }
        
        return $query->update($set);
    }

    public function updateDeleteAuthPermission($alias_group, $data){
        $delete_alias_group = $this->uxuiAuthPermission
             ->where("alias_group" , "=", $alias_group)
             ->delete();
        if($data["empty"] == "false") return $this->uxuiAuthPermission->insertOrIgnore($data["routes_data"]);
        return true;
    }
}