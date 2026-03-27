<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class RefUserInfo extends \App\Models\MyModel
{
    public $table = 'ref_user_info';
    public $timestamps = false;

    function get_privilege($value=null){
        $data=['0'=>'User Biasa','14'=>'Super Admin'];
        if($value!=null or $value!='' or isset($value)){
            if(!empty($data[$value])){
                return $data[$value];
            }
        }
        return $data;
    }
}