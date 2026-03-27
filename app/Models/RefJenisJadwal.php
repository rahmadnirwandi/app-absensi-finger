<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class RefJenisJadwal extends \App\Models\MyModel
{
    public $table = 'ref_jenis_jadwal';
    public $timestamps = false;
    public $incrementing = true;

    function type_jenis_jadwal($id=null){
        $data=[1=>"Rutin",2=>"Shift"];
        if(isset($id)){
            return !empty($data[$id]) ? $data[$id] : '';
        }
        return $data;
    }

    function get_bgcolor(){
        $data=[
            "#87b1f0","#BEFFF7","#FFF2D8","#FF6969","#d9beb0","#FACBEA","#D2E0FB","#D7E5CA","#78D6C6","#B0D9B1","#F4E869","#82A0D8","#E4F1FF","#A2FF86","#E4A5FF"
        ];
        return $data;
    }
}
