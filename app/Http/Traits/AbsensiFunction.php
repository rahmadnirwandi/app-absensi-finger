<?php

namespace App\Http\Traits;

use Hamcrest\Arrays\IsArray;
use Hamcrest\Type\IsObject;

trait AbsensiTraits {

    public function hitung_selisih_waktu($awal,$akhir){
        $diff  = $akhir - $awal;

        $jam   = floor($diff / (60 * 60));
        $menit = $diff - ( $jam * (60 * 60) );
        $detik = $diff % 60;

        return (object)[
            'jam'=>$jam,
            'menit'=>floor( $menit / 60 ),
            'detik'=>$detik,
        ];
    }

    public function hitung_waktu_by_seccond($waktu){
        $jam   = floor($waktu / (60 * 60));
        $menit = $waktu - ( $jam * (60 * 60) );
        $detik = $waktu % 60;

        return (object)[
            'jam'=>$jam,
            'menit'=>floor( $menit / 60 ),
            'detik'=>$detik,
        ];
    }

    public function change_format_waktu_indo($waktu_sec,$format=''){
        $waktu_sec_tmp=$this->hitung_waktu_by_seccond($waktu_sec);
        $text='';
        if(!empty($format)){
            $text=implode($format,(array)$waktu_sec_tmp);
        }else{
            $text.=(!empty($waktu_sec_tmp->jam) ? $waktu_sec_tmp->jam : 0).' Jam, ';
            $text.=(!empty($waktu_sec_tmp->menit) ? $waktu_sec_tmp->menit : 0).' Menit, ';
            $text.=(!empty($waktu_sec_tmp->detik) ? $waktu_sec_tmp->detik : 0).' Detik ';
        }

        return $text;
    }

    public function set_format_waktu_indo($waktu_sec){
        $_sec=$this->his_to_seconds($waktu_sec);
        return $this->change_format_waktu_indo($_sec);
    }

    public function his_to_seconds($time){
        $str_time = preg_replace("/^([\d]{1,2})\:([\d]{2})$/", "00:$1:$2", $time);
        sscanf($str_time, "%d:%d:%d", $hours, $minutes, $seconds);
        return  $hours * 3600 + $minutes * 60 + $seconds;
    }

    function get_tgl_khusus_with_data($params=null) {

        $tgl_awal=$params['tgl_awal'];
        $tgl_akhir=$params['tgl_akhir'];
        $data_sent=!empty($params['data_sent']) ? $params['data_sent'] : '';
        $list_libur_kerja=!empty($params['list_libur_kerja']) ? $params['list_libur_kerja'] : [] ;
        $list_libur_nasional=!empty($params['list_libur_nasional']) ? $params['list_libur_nasional'] : [] ;

        $jml=0;
        $hasil_data=[];
        if(is_numeric($tgl_akhir)){
            $jml=$tgl_akhir;
            $tgl_akhir='';
        }else{
            $start_date = new \DateTime($tgl_awal);
            $end_date = new \DateTime($tgl_akhir);

            $interval = $start_date->diff($end_date);
            $jml=$interval->days;
        }
        $jml_hari=1;
        $hasil_data[$tgl_awal]=!empty($data_sent) ? $data_sent : '';
        if(!empty($list_libur_kerja[$tgl_awal])){
            $jml_hari--;
        }else if(!empty($list_libur_nasional[$tgl_awal])){
            $jml_hari--;
        }

        if(!empty($jml)){
            $indate = new \DateTime($tgl_awal);
            for ($i=1; $i <= $jml; $i++) {
                $indate->format('Y-m-d');
                $indate->modify('+1 day');
                $hasil = $indate->format('Y-m-d');
                $hasil_data[$hasil]=!empty($data_sent) ? $data_sent : '';
                $jml_hari++;

                if(!empty($list_libur_kerja[$hasil])){
                    $jml_hari--;
                }else if(!empty($list_libur_nasional[$hasil])){
                    $jml_hari--;
                }
            }
        }
        return [
            'hasil_data'=>$hasil_data,
            'jml_hari'=>$jml_hari
        ];
    }

    function get_tgl_per_bulan($tahun_bulan) {
        $get_data=explode('-',$tahun_bulan);
        $tahun=!empty($get_data[0]) ? $get_data[0] : date('Y');
        $bulan=!empty($get_data[1]) ? $get_data[1] : date('m');

        $jumlahHari = cal_days_in_month(CAL_GREGORIAN, $bulan, $tahun);

        $get_date=[];
        $get_date_star_end=[];
        for ($tanggal = 1; $tanggal <= $jumlahHari; $tanggal++) {
            // Format tanggal dalam bentuk YYYY-MM-DD
            $tanggalFormat = sprintf('%04d-%02d-%02d', $tahun, $bulan, $tanggal);
            $get_date[]=$tanggalFormat;
            if($tanggal==1){
                $get_date_star_end[]=$tanggalFormat;
            }

            if($tanggal==$jumlahHari){
                $get_date_star_end[]=$tanggalFormat;
            }
        }

        return (object)[
            'list_tgl'=>$get_date,
            'tgl_start_end'=>$get_date_star_end,
        ];

    }

    public function index_rumus_jadwal($user_presensi,$jadwal_presensi){
        if(!empty($user_presensi)){
            $pre_prensensi=!empty($jadwal_presensi) ? $jadwal_presensi : [] ;
            $pre_awal=!empty($pre_prensensi->jam_awal) ? $pre_prensensi->jam_awal : '00:00:00';
            $pre_akhir=!empty($pre_prensensi->jam_akhir) ? $pre_prensensi->jam_akhir : '00:00:00';
            $pre_status_toren_cepat=!empty($pre_prensensi->status_toren_jam_cepat) ? $pre_prensensi->status_toren_jam_cepat : 0;
            $pre_toren_cepat=!empty($pre_prensensi->toren_jam_cepat) ? $pre_prensensi->toren_jam_cepat : '00:00:00';
            $pre_status_toren_telat=!empty($pre_prensensi->status_toren_jam_telat) ? $pre_prensensi->status_toren_jam_telat : 0;
            $pre_toren_telat=!empty($pre_prensensi->toren_jam_telat) ? $pre_prensensi->toren_jam_telat : '00:00:00';

            $pre_awal_sec=$this->his_to_seconds($pre_awal);
            $pre_akhir_sec=$this->his_to_seconds($pre_akhir);
            $pre_toren_cepat_sec=$this->his_to_seconds($pre_toren_cepat);
            $pre_toren_telat_sec=$this->his_to_seconds($pre_toren_telat);

            $pre_cepat_sec=0;
            $pre_telat_sec=0;

            if($pre_status_toren_cepat==1){
                $pre_cepat_sec=$pre_awal_sec-$pre_toren_cepat_sec;
            }

            if($pre_status_toren_telat==1){
                $pre_telat_sec=$pre_akhir_sec+$pre_toren_telat_sec;
            }

            $user_presensi_sec=$this->his_to_seconds($user_presensi);
            $status_presensi='';
            $selisih_waktu_sec=0;
            $selisih_waktu='';
            $type_waktu='a';
            if($pre_status_toren_cepat){
                if( ( $user_presensi_sec >= $pre_cepat_sec ) and ( $user_presensi_sec < $pre_awal_sec  ) ){
                    $type_waktu="-";
                    $index='-';
                    $status_presensi=$index.$jadwal_presensi->kd_jadwal;
                    $selisih_waktu_sec=$pre_awal_sec-$user_presensi_sec;
                    $selisih_waktu=$this->hitung_waktu_by_seccond($selisih_waktu_sec);
                }
            }

            if( ( $user_presensi_sec >= $pre_awal_sec ) and ( $user_presensi_sec <= $pre_akhir_sec  ) ){
                $type_waktu='h';
                $index='';
                $status_presensi=$index.$jadwal_presensi->kd_jadwal;
                $selisih_waktu_sec=0;
                $selisih_waktu=$this->hitung_waktu_by_seccond($selisih_waktu_sec);
            }

            if($pre_status_toren_telat){
                if( ( $user_presensi_sec > $pre_akhir_sec ) and ( $user_presensi_sec <= $pre_telat_sec  ) ){
                    $type_waktu='+';
                    $index='+';
                    $status_presensi=$index.$jadwal_presensi->kd_jadwal;
                    $selisih_waktu_sec=$pre_telat_sec-$user_presensi_sec;
                    $selisih_waktu=$this->hitung_waktu_by_seccond($selisih_waktu_sec);
                }
            }

            return (object)[
                'status_presensi'=>$status_presensi,
                'selisih_waktu_sec'=>$selisih_waktu_sec,
                'selisih_waktu'=>$selisih_waktu,
                'type_waktu'=>$type_waktu,
            ];
        }
    }

    public function get_presensi_by_jadwal_mesin($jadwal_kerja,$list_presensi){

        $get_jadwal=[];
        $get_jadwal_tmp=!empty($jadwal_kerja) ? (array)json_decode($jadwal_kerja) : '';
        if($get_jadwal_tmp){
            $lib_jadwal=[
                'kd_jadwal',
                'uraian',
                'alias',
                'jam_awal',
                'jam_akhir',
                'status_toren_jam_cepat',
                'toren_jam_cepat',
                'status_toren_jam_telat',
                'toren_jam_telat',
                'status_jadwal'
            ];
            foreach($get_jadwal_tmp as $key => $val){
                $tmp=[];
                for($i=0; $i<count($lib_jadwal); $i++){
                    $tmp[$lib_jadwal[$i]]=!empty($val[$i]) ? $val[$i] : '';
                }
                $get_jadwal[$key]=(object)$tmp;
                if($tmp['status_jadwal']==0){
                    unset($get_jadwal[$key]);
                }
            }
        }

        $hasil=[];
        if($list_presensi){
            foreach($list_presensi as $data_pre){
                $user_presensi=$data_pre;
                if(!empty($get_jadwal)){
                    foreach($get_jadwal as $key_jp => $val_jp){
                        $check=$this->index_rumus_jadwal($user_presensi,$val_jp);
                        $check_status=!empty($check->status_presensi) ? $check->status_presensi : '';
                        // if($check_status){
                        //     //abaikan jika presensi sudah ketemu dengan jadwal
                        //     if(empty($hasil[$key_jp])){
                        //         $hasil[$key_jp]=(object)[
                        //             'hasil_check'=>$check,
                        //             'user_presensi'=>$user_presensi
                        //         ];
                        //     }
                        // }
                        if($check_status){

                            if(empty($hasil[$key_jp])){
                                $hasil[$key_jp] = (object)[
                                    'hasil_check'=>$check,
                                    'user_presensi'=>$user_presensi
                                ];

                                break; // 🔥 WAJIB TAMBAHKAN INI
                            }
                        }
                    }
                }
            }
        }

        return (object)[
            'hasil_presensi'=>$hasil,
            'jadwal_mesin'=>$get_jadwal
        ];
    }

    public function check_bolean_array($array_1,$array_2){
        $check_3=0;
        $hasil=0;

        $get_index_kombinasi=[];
        foreach($array_1 as $key => $value){
            if(!empty($value)){
                $get_index_kombinasi[]=$key;
            }
        }

        $check_ready=0;
        foreach($get_index_kombinasi as $value){
            $val_1=!empty($array_1[$value]) ? $array_1[$value] : '';
            $val_2=!empty($array_2[$value]) ? $array_2[$value] : '';
            if($val_1==$val_2){
                $check_ready++;
            }else{
                $check_ready--;
            }
        }
        if($check_ready==count($get_index_kombinasi)){
            return 1;
        }
        return 0;
    }

    public function set_list_log_text($list_log,$max_log_per,$type='raw'){
        $pre_tmp=explode(',',$list_log);
        $max_pre_baris=$max_log_per;
        $first=1;
        $h_tmp=[];
        foreach($pre_tmp as $key_pre => $val_pre){
            if(( $key_pre+1) > $max_pre_baris ){
                $first++;
                $max_pre_baris=$max_pre_baris+$max_pre_baris;
            }
            $h_tmp[$first][]=$val_pre;
        }
        if($type=='array'){
            return $h_tmp;
        }

        $html='';
        if($type=='raw'){
            foreach($h_tmp as $key_pre => $val_pre){
                $tmp_=implode(', ',$val_pre);
                $html.="<div>".$tmp_."</div>";
            }
        }
        return $html;
    }

    public function get_list_data_presensi($type=''){
        $list_status=[
            1=>[
                'text'=>"Hadir",
                'alias'=>"H",
            ],
            2=>[
                'text'=>"Terlambat",
                'alias'=>"T",
            ],
            3=>[
                'text'=>"Tidak Absen Masuk",
                'alias'=>"TAM",
            ],
            4=>[
                'text'=>"Tidak Absen Pulang",
                'alias'=>"TAP",
            ],
            5=>[
                'text'=>"Pulang Cepat",
                'alias'=>"P",
            ],
            6=>[
                'text'=>"Alpa",
                'alias'=>"A",
            ],
        ];

        $data_type=[];
        if($type==1){
            $data_type[1]=[
                'text'=>"Cepat Hadir",
                'alias'=>"HC",
            ];
            $data_type[2]=[
                'text'=>"Tepat Waktu",
                'alias'=>"H",
            ];
            $data_type[3]=[
                'text'=>"Terlambat",
                'alias'=>"T",
            ];
            $data_type['a']=[
                'text'=>"Tidak Absen Masuk",
                'alias'=>"A",
            ];
        }

        if($type==2){
            $data_type[1]=[
                'text'=>"Cepat Istirahat",
                'alias'=>"CI",
            ];
            $data_type[2]=[
                'text'=>"Tepat Waktu",
                'alias'=>"H",
            ];
            $data_type[3]=[
                'text'=>"Terlambat",
                'alias'=>"T",
            ];
            $data_type['a']=[
                'text'=>"Tidak Absen Istirahat",
                'alias'=>"A",
            ];
        }

        if($type==3){
            /* Masuk setelah istirahat */
        }

        if($type==4){
            $data_type[1]=[
                'text'=>"Pulang Cepat",
                'alias'=>"P",
            ];
            $data_type[2]=[
                'text'=>"Tepat Waktu",
                'alias'=>"H",
            ];
            $data_type[3]=[
                'text'=>"Terlambat",
                'alias'=>"T",
            ];
            $data_type['a']=[
                'text'=>"Tidak Absen Pulang",
                'alias'=>"A",
            ];
        }

        if(!empty($type)){
            return $data_type;
        }

        return $list_status;
    }

    public function get_list_simbol_text(){

        $list_simbol_text=[];
        $list_simbol_tmp=$this->get_list_data_presensi();
        if(!empty($list_simbol_tmp)){
            foreach($list_simbol_tmp as $item){
                $item=(object)$item;
                $list_simbol_text[]='('.$item->alias.') = '.$item->text;
            }
        }
        $text='';
        if(!empty($list_simbol_text)){
            $text=implode(', ',$list_simbol_text);
        }
        return $text;
    }

    function getJadwalRutin(){
        $data_jadwal_presensi=(new \App\Http\Traits\PresensiHitungRutinFunction)->getWaktuKerja(['id_jenis_jadwal'=>1])->first();
        if($data_jadwal_presensi){
            $waktu_masuk=(new \App\Http\Traits\AbsensiFunction)->his_to_seconds($data_jadwal_presensi->masuk_kerja);
            $waktu_pulang=(new \App\Http\Traits\AbsensiFunction)->his_to_seconds($data_jadwal_presensi->pulang_kerja);

            $mulai_istirahat=(new \App\Http\Traits\AbsensiFunction)->his_to_seconds($data_jadwal_presensi->awal_istirahat);
            $akhir_istirahat=(new \App\Http\Traits\AbsensiFunction)->his_to_seconds($data_jadwal_presensi->akhir_istirahat);

            $waktu_masuk_pulang=$waktu_pulang-$waktu_masuk;
            if(!empty($data_jadwal_presensi->pulang_kerja_next_day)){
                $waktu_masuk_pulang=$waktu_masuk-$waktu_pulang;
            }

            $total_istirahat=$akhir_istirahat-$mulai_istirahat;
            if(!empty($data_jadwal_presensi->akhir_istirahat_next_day)){
                $total_istirahat=$mulai_istirahat-$akhir_istirahat;
            }

            $total_waktu_kerja_sec=$waktu_masuk_pulang-$total_istirahat;
            $total_waktu_kerja=(new \App\Http\Traits\AbsensiFunction)->hitung_waktu_by_seccond($total_waktu_kerja_sec);

            return (object)[
                'data_jadwal_presensi'=>$data_jadwal_presensi,
                'total_waktu_kerja_sec'=>$total_waktu_kerja_sec,
                'total_waktu_kerja'=>$total_waktu_kerja,
            ];
        }
    }


//    function proses_absensi_rutin($get_jadwal_rutin,$data){
//        /*
//            Lebih Cepat = 4
//            Tepat Waktu = 1
//            Terlambat = 2
//            Di luar Jadwal = 3
//        */
//        ini_set("memory_limit","800M");
//        set_time_limit(0);
//
//        $onejam= 3600;
//        $onemenit= 60;
//        $hitung_plus_mulai=$onejam*0 + $onemenit*30;
//        $hitung_plus_akhir=$onejam*0 + $onemenit*30;
//
//        $get_jadwal_phitung=(new \App\Models\RefJadwalPhitung)->where(['type'=>'Rutin'])->get();
//        if($get_jadwal_phitung){
//            foreach($get_jadwal_phitung as $get_data){
//                if($get_data->jenis=="Mulai"){
//                    $hitung_plus_mulai=($onejam*$get_data->jam)+($onemenit*$get_data->menit);
//                }
//
//                if($get_data->jenis=="Akhir"){
//                    $hitung_plus_akhir=($onejam*$get_data->jam)+($onemenit*$get_data->menit);
//                }
//            }
//        }
//
//        $check_nilai_jadwal=[];
//        if(!empty($get_jadwal_rutin)){
//
//            foreach($get_jadwal_rutin as $val_jadwal){
//                $jam_mulai=$val_jadwal->jam_awal;
//                $jam_akhir=$val_jadwal->jam_akhir;
//                $jam_absensi=$data->jam;
//
//                $jadwal_masuk_str = strtotime($jam_mulai);
//                $jadwal_tutup_str = strtotime($jam_akhir);
//                $absensi_str = strtotime($jam_absensi);
//
//                $jam_mulai_kurang_str = $jadwal_masuk_str - $hitung_plus_mulai;
//                $jam_akhir_tambah_str = $jadwal_tutup_str + $hitung_plus_akhir;
//
//                if(($jam_mulai_kurang_str <= $absensi_str) and ($jadwal_masuk_str > $absensi_str)){
//                    /* Lebih Cepat */
//                    $type_status=4;
//                    $selisih_waktu=(array)$this->hitung_selisih_waktu($absensi_str,$jadwal_masuk_str);
//                }elseif( ($jadwal_masuk_str <= $absensi_str) and ($jadwal_tutup_str >= $absensi_str) ){
//                    /* Tepat Waktu */
//                    $type_status=1;
//                    $selisih_waktu=(array)$this->hitung_selisih_waktu($absensi_str,$jadwal_tutup_str);
//                }elseif( ( $absensi_str > $jadwal_tutup_str ) and ( $absensi_str <= $jam_akhir_tambah_str ) ){
//                    /* Terlambat */
//                    $type_status=2;
//                    $selisih_waktu=(array)$this->hitung_selisih_waktu($jadwal_tutup_str,$absensi_str);
//                }else{
//                    /* Di Luar Jadwal */
//                    $type_status=3;
//                }
//
//                // if(($jadwal_masuk_str <= $absensi_str) and ($jadwal_tutup_str >= $absensi_str)){
//                //     $type_status=1;
//                //     $selisih_waktu=(array)$this->hitung_selisih_waktu($absensi_str,$jadwal_tutup_str);
//                // }elseif($absensi_str > $jadwal_tutup_str ){
//                //     $type_status=2;
//                //     $selisih_waktu=(array)$this->hitung_selisih_waktu($jadwal_tutup_str,$absensi_str);
//                // }else{
//                //     $type_status=3;
//                // }
//
//                $check_nilai_jadwal[$type_status]=[
//                    'id_mesin_absensi'=>$data->id_mesin_absensi,
//                    'nm_mesin'=>$data->nm_mesin,
//                    'lokasi_mesin'=>$data->lokasi_mesin,
//                    'verified_mesin'=>$data->verified_mesin,
//                    'id_jenis_jadwal'=>$val_jadwal->id_jenis_jadwal,
//                    'nm_jenis_jadwal'=>$val_jadwal->nm_jenis_jadwal,
//                    'id_jadwal'=>$val_jadwal->id_jadwal,
//                    'nm_jadwal'=>$val_jadwal->uraian,
//                    'jam_mulai'=>$jam_mulai,
//                    'jam_akhir'=>$jam_akhir,
//                    'jam_absensi'=>$jam_absensi,
//                    'selisih_waktu'=>!empty($selisih_waktu) ? $selisih_waktu : ''
//                ];
//            }
//        }
//
//        $hasil_absensi=[];
//        if(!empty($check_nilai_jadwal[4])){
//            $index_me=4;
//            $hasil_absensi=$check_nilai_jadwal[$index_me];
//            $hasil_absensi_tmp=[
//                'hasil_status_absensi'=>$index_me,
//                'hasil_status_absensi_text'=>'Lebih Cepat',
//            ];
//            $hasil_absensi=array_merge($hasil_absensi,$hasil_absensi_tmp);
//        }elseif(!empty($check_nilai_jadwal[1])){
//            $index_me=1;
//            $hasil_absensi=$check_nilai_jadwal[$index_me];
//            $hasil_absensi_tmp=[
//                'hasil_status_absensi'=>$index_me,
//                'hasil_status_absensi_text'=>'Tidak Telat',
//            ];
//            $hasil_absensi=array_merge($hasil_absensi,$hasil_absensi_tmp);
//        }elseif(!empty($check_nilai_jadwal[2])){
//            $index_me=2;
//            $hasil_absensi=$check_nilai_jadwal[$index_me];
//            $hasil_absensi_tmp=[
//                'hasil_status_absensi'=>$index_me,
//                'hasil_status_absensi_text'=>'Telat',
//            ];
//            $hasil_absensi=array_merge($hasil_absensi,$hasil_absensi_tmp);
//        }else{
//            $index_me=3;
//            $hasil_absensi=$check_nilai_jadwal[$index_me];
//            $hasil_absensi['id_jadwal']=0;
//            $hasil_absensi['nm_jadwal']='';
//            $hasil_absensi['jam_mulai']='';
//            $hasil_absensi['jam_akhir']='';
//            $hasil_absensi_tmp=[
//                'hasil_status_absensi'=>$index_me,
//                'hasil_status_absensi_text'=>'Di Luar Jadwal',
//            ];
//            $hasil_absensi=array_merge($hasil_absensi,$hasil_absensi_tmp);
//        }
//
//        return (object)$hasil_absensi;
//    }
//
//    function get_total_jam_kerja_default(){
//        $get_jrutin_tkerja=(new \App\Models\RefJadwalRutinTkerja)->where(['type'=>'+'])->first();
//        $jam_mulai_kerja=!empty($get_jrutin_tkerja->jam_awal) ? $get_jrutin_tkerja->jam_awal : 0;
//        $jam_selesai_kerja=!empty($get_jrutin_tkerja->jam_akhir) ? $get_jrutin_tkerja->jam_akhir : 0;
//
//        $jam_mulai_kerja_str = strtotime($jam_mulai_kerja);
//        $jam_selesai_kerja_str = strtotime($jam_selesai_kerja);
//        $selisih_waktu_kerja=$this->hitung_selisih_waktu($jam_mulai_kerja_str,$jam_selesai_kerja_str);
//        $selisih_waktu_kerja_detik=( !empty($selisih_waktu_kerja->jam) ? $selisih_waktu_kerja->jam*3600 : 0 ) + ( !empty($selisih_waktu_kerja->menit) ? $selisih_waktu_kerja->menit*60 : 0 ) + ( !empty($selisih_waktu_kerja->detik) ? $selisih_waktu_kerja->detik : 0 ) ;
//
//        $waktu_kurang=0;
//        $waktu_kurang_text='';
//        $get_jrutin_tkerja_kurang=(new \App\Models\RefJadwalRutinTkerja)->where(['type'=>'-'])->get();
//        if($get_jrutin_tkerja_kurang){
//            $hasil_jam=0;
//            $hasil_menit=0;
//            $hasil_detik=0;
//            foreach($get_jrutin_tkerja_kurang as $get_data){
//                $jam_mulai_kerja=!empty($get_data->jam_awal) ? $get_data->jam_awal : 0;
//                $jam_selesai_kerja=!empty($get_data->jam_akhir) ? $get_data->jam_akhir : 0;
//
//                $jam_mulai_kerja_str = strtotime($jam_mulai_kerja);
//                $jam_selesai_kerja_str = strtotime($jam_selesai_kerja);
//
//                $selisih_waktu=$this->hitung_selisih_waktu($jam_mulai_kerja_str,$jam_selesai_kerja_str);
//
//                $hasil_jam+=!empty($selisih_waktu->jam) ? $selisih_waktu->jam : 0;
//                $hasil_menit+=!empty($selisih_waktu->menit) ? $selisih_waktu->menit : 0;
//                $hasil_detik+=!empty($selisih_waktu->detik) ? $selisih_waktu->detik : 0;
//            }
//
//            $jam_to_second=!empty($hasil_jam) ? $hasil_jam*3600 : 0;
//            $menit_to_second=!empty($hasil_menit) ? $hasil_menit*60 : 0;
//            $second_tmp=!empty($hasil_detik) ? $hasil_detik : 0;
//            $waktu_kurang=$jam_to_second+$menit_to_second+$second_tmp;
//        }
//
//        $selisih_waktu_kerja_detik=($selisih_waktu_kerja_detik>0) ? $selisih_waktu_kerja_detik : 0;
//        $waktu_kurang=($waktu_kurang>0) ? $waktu_kurang : 0;
//        $total_kerja=$selisih_waktu_kerja_detik-$waktu_kurang;
//
//        return (object)[
//            'date'=>gmdate("H:i:s", $total_kerja),
//            'strtime'=>$total_kerja,
//        ];
//    }
//
//    function get_total_jam_kerja_rutin($data_absensi,$jadwal_sistem=[]){
//
//        $total_point=0;
//        if($data_absensi){
//            $data_masuk=!empty($data_absensi[1]) ? $data_absensi[1] : [];
//            $data_istirahat=!empty($data_absensi[2]) ? $data_absensi[2] : [];
//            $data_pulang=!empty($data_absensi[3]) ? $data_absensi[3] : [];
//
//            $keterangan_tmp=[];
//            if(!empty($data_masuk->hasil_status_absensi)){
//                if($data_masuk->hasil_status_absensi==1){
//                    $waktu_masuk=$data_masuk->jam_absensi;
//                    $total_point++;
//                }
//
//                if($data_masuk->hasil_status_absensi==2){
//                    $waktu_masuk=$data_masuk->jam_absensi;
//                    $keterangan_tmp[]="Terlambat Masuk";
//                }
//
//                if($data_masuk->hasil_status_absensi==4){
//
//                }
//            }
//
//            if(!empty($data_istirahat->hasil_status_absensi)){
//                if($data_istirahat->hasil_status_absensi==1){
//                    $total_point++;
//                }
//
//                if($data_istirahat->hasil_status_absensi==2){
//
//                }
//
//                if($data_istirahat->hasil_status_absensi==4){
//
//                }
//            }
//
//            if(!empty($data_pulang->hasil_status_absensi)){
//                if($data_pulang->hasil_status_absensi==1){
//                    $waktu_pulang=$data_pulang->jam_absensi;
//                    $total_point++;
//                }
//
//                if($data_pulang->hasil_status_absensi==2){
//                    $waktu_pulang=$data_pulang->jam_absensi;
//                }
//
//                if($data_pulang->hasil_status_absensi==4){
//                    $waktu_pulang=$data_pulang->jam_absensi;
//                    $keterangan_tmp[]="Pulang Cepat";
//                }
//            }
//
//            if(empty($data_masuk)){
//                $keterangan_tmp[]="Tidak Absen Masuk";
//            }
//
//            if(empty($data_istirahat)){
//                $keterangan_tmp[]="Tidak Absen Istirahat";
//            }
//
//            if(empty($data_pulang)){
//                $keterangan_tmp[]="Tidak Absen Pulang";
//            }
//
//            $total_waktu_kerja=0;
//            $total_waktu_kerja_text='';
//            if(!empty($waktu_masuk) && !empty($waktu_pulang)){
//                $waktu_masuk=$this->his_to_seconds($waktu_masuk);
//                $waktu_pulang=$this->his_to_seconds($waktu_pulang);
//                $total_waktu_kerja=$waktu_pulang-$waktu_masuk;
//                $total_waktu_kerja=$this->hitung_waktu_by_seccond($total_waktu_kerja);
//                $total_waktu_kerja_text=$total_waktu_kerja->jam.' jam'.', '.$total_waktu_kerja->menit.' Menit'.', '.$total_waktu_kerja->detik.' Detik';
//            }
//
//            if($total_point==0){
//                $total_waktu_kerja=0;
//                $total_waktu_kerja_text=0;
//                $keterangan='Absen';
//            }
//            if($total_point==3){
//                $keterangan='Hadir';
//            }
//            if($total_point>0 && $total_point<3){
//                if(!empty($keterangan_tmp)){
//                    $keterangan=implode(',',$keterangan_tmp);
//                }
//            }
//        }
//
//        return (object)[
//            'total_waktu_kerja'=>!empty($total_waktu_kerja) ? $total_waktu_kerja : [],
//            'total_waktu_kerja_text'=>!empty($total_waktu_kerja_text) ? $total_waktu_kerja_text : [],
//            'total_point'=>!empty($total_point) ? $total_point : 0,
//            'keterangan'=>!empty($keterangan) ? $keterangan : '',
//        ];
//    }
}

class AbsensiFunction {
    use absensiTraits;
}

?>