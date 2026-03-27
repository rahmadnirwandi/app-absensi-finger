<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class RefTemplateJadwalShiftDetail extends \App\Models\MyModel
{
    public $table = 'ref_template_jadwal_shift_detail';
    public $timestamps = false;

    function list_type_periode($id=null){
        $data=[1=>"Hari",2=>"Minggu",3=>'Bulan'];
        if(!empty($id)){
            return !empty($data[$id]) ? $data[$id] : '';
        }
        return $data;
    }

    function list_type_periode_system($id=null){
        $data=[1=>"day",2=>"week",3=>'month'];
        if(!empty($id)){
            return !empty($data[$id]) ? $data[$id] : '';
        }
        return $data;
    }

    function list_jml_periode($id=null){
        // $data=[1=>28,2=>29,3=>30,4=>31];
        $data=[1=>31];
        if(!empty($id)){
            return !empty($data[$id]) ? $data[$id] : '';
        }
        return $data;
    }

    function get_tgl_periode($id=null){
        $data=[28=>'2023-02-01',29=>'2024-02-01',30=>'2023-04-01',31=>'2023-05-01'];
        if(!empty($id)){
            return !empty($data[$id]) ? $data[$id] : '';
        }
        return $data;
    }
}
