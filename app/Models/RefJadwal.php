<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class RefJadwal extends \App\Models\MyModel
{
    public $table = 'ref_jadwal';
    public $timestamps = false;

    function referensi_data(){
        $data=[];
        $data[]=(object)[    
            'kd_jadwal'=>1,
            'uraian'=>'Masuk',
            'alias'=>'masuk',
            'status_toren_jam_cepat'=>0,
            'status_toren_jam_telat'=>0,
            'status_jadwal'=>1
        ];

        $data[]=(object)[    
            'kd_jadwal'=>2,
            'uraian'=>'Istirahat',
            'alias'=>'istirahat',
            'status_toren_jam_cepat'=>0,
            'status_toren_jam_telat'=>0,
            'status_jadwal'=>1
        ];

        $data[]=(object)[    
            'kd_jadwal'=>3,
            'uraian'=>'Masuk Setelah Istirahat',
            'alias'=>'masuk_setelah_istirahat',
            'status_toren_jam_cepat'=>0,
            'status_toren_jam_telat'=>0,
            'status_jadwal'=>0
        ];

        $data[]=(object)[    
            'kd_jadwal'=>4,
            'uraian'=>'Pulang',
            'alias'=>'pulang',
            'status_toren_jam_cepat'=>0,
            'status_toren_jam_telat'=>0,
            'status_jadwal'=>1
        ];
        return $data;
    }
}
