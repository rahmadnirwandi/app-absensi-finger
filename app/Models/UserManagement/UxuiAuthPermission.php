<?php

namespace App\Models\UserManagement;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class UxuiAuthPermission extends Model
{
    public $table = 'uxui_auth_permission';
    public $timestamps = false;

    function getPermission($params=null){
        $where=[];
        if(!empty($params['where'])){
            $list=$params['where'];
            foreach($list as $value){
                $nilai=(is_string($value[2])) ? "'".$value[2]."'" : $value[2];
                $where[]=$value[0].' '.$value[1].' '.$nilai;
            }
        }
        $where=!empty($where) ? "where ".implode(' and ',$where) : '';

        $uxui_auth_routes=(new \App\Models\UserManagement\UxuiAuthRoutes)->table;
        
        $query=DB::table(DB::raw(
            "(
                select url 
                from ".$this->table." 
                ".$where."
            ) ".$this->table))
            ->select($this->table.'.url','controller','type')
            ->join($uxui_auth_routes,$uxui_auth_routes.'.url','=',$this->table.'.url')
        ;

        return $query;
    }
}
