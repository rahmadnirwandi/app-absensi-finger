<?php

namespace App\Http\Traits;

use Illuminate\Support\Facades\DB;

use App\Http\Traits\AbsensiFunction;

trait PresensiHitungRutinTraits {

    public $absensif;
    public function __construct()
    {
        $this->absensif = new AbsensiFunction;
    }

    public function getWaktuKerja($params)
    {
        DB::statement("SET SESSION group_concat_max_len = 15000;");

        $id_jenis_jadwal = !empty($params['id_jenis_jadwal']) ? $params['id_jenis_jadwal'] : 0;

        // Step 1: Get data jenis jadwal
        $jenisJadwal = DB::table('ref_jenis_jadwal')
            ->where('id_jenis_jadwal', $id_jenis_jadwal)
            ->first();

        if (!$jenisJadwal) {
            return collect();
        }

        // Step 2: Get data jadwal
        $jadwalData = DB::table('ref_jadwal')
            ->where('id_jenis_jadwal', $id_jenis_jadwal)
            ->select([
                'kd_jadwal',
                'uraian',
                'alias',
                DB::raw('TIME_FORMAT(jam_awal, "%H:%i:%s") as jam_awal'),
                DB::raw('TIME_FORMAT(jam_akhir, "%H:%i:%s") as jam_akhir'),
                'status_toren_jam_cepat',
                DB::raw('TIME_FORMAT(toren_jam_cepat, "%H:%i:%s") as toren_jam_cepat'),
                'status_toren_jam_telat',
                DB::raw('TIME_FORMAT(toren_jam_telat, "%H:%i:%s") as toren_jam_telat'),
                'status_jadwal',
            ])
            ->get();

        // Step 3: Build data_jadwal sebagai JSON object
        $data_jadwal = [];
        foreach ($jadwalData as $jadwal) {
            $data_jadwal[$jadwal->kd_jadwal] = [
                $jadwal->kd_jadwal,
                $jadwal->uraian,
                $jadwal->alias,
                $jadwal->jam_awal,
                $jadwal->jam_akhir,
                $jadwal->status_toren_jam_cepat,
                $jadwal->toren_jam_cepat,
                $jadwal->status_toren_jam_telat,
                $jadwal->toren_jam_telat,
                $jadwal->status_jadwal,
            ];
        }

        // Step 4: Gabungkan hasil
        $result = (object) array_merge(
            (array) $jenisJadwal,
            ['data_jadwal' => json_encode($data_jadwal)]
        );

        // Return sebagai collection agar konsisten dengan query builder
        return collect([$result]);
    }

    public function getWaktuKerjaByTanggal($params)
    {
        DB::statement("SET SESSION group_concat_max_len = 15000;");

        $id_karyawan = !empty($params['id_karyawan']) ? $params['id_karyawan'] : 0;
        $tanggal     = !empty($params['tanggal']) ? $params['tanggal'] : null;

        if (!$id_karyawan || !$tanggal) {
            return collect();
        }


        $kalender = DB::table('uxui_kalender_kerja')
            ->where('id_karyawan', $id_karyawan)
            ->where('tanggal', $tanggal)
            ->first();

        if (!$kalender) {
            return collect();
        }

        $id_jenis_jadwal = $kalender->id_jenis_jadwal;

        $jenisJadwal = DB::table('ref_jenis_jadwal')
            ->where('id_jenis_jadwal', $id_jenis_jadwal)
            ->first();

        if (!$jenisJadwal) {
            return collect();
        }

        $jadwalData = DB::table('ref_jadwal')
            ->where('id_jenis_jadwal', $id_jenis_jadwal)
            ->select([
                'kd_jadwal',
                'uraian',
                'alias',
                DB::raw('TIME_FORMAT(jam_awal, "%H:%i:%s") as jam_awal'),
                DB::raw('TIME_FORMAT(jam_akhir, "%H:%i:%s") as jam_akhir'),
                'status_toren_jam_cepat',
                DB::raw('TIME_FORMAT(toren_jam_cepat, "%H:%i:%s") as toren_jam_cepat'),
                'status_toren_jam_telat',
                DB::raw('TIME_FORMAT(toren_jam_telat, "%H:%i:%s") as toren_jam_telat'),
                'status_jadwal',
            ])
            ->get();

        $data_jadwal = [];

        foreach ($jadwalData as $jadwal) {
            $data_jadwal[$jadwal->kd_jadwal] = [
                $jadwal->kd_jadwal,
                $jadwal->uraian,
                $jadwal->alias,
                $jadwal->jam_awal,
                $jadwal->jam_akhir,
                $jadwal->status_toren_jam_cepat,
                $jadwal->toren_jam_cepat,
                $jadwal->status_toren_jam_telat,
                $jadwal->toren_jam_telat,
                $jadwal->status_jadwal,
            ];
        }

        $result = (object) array_merge(
            (array) $jenisJadwal,
            ['data_jadwal' => json_encode($data_jadwal)]
        );

        return collect([$result]);
    }

    public function get_jadwal_rutin(){
        $data_jadwal_kerja_tmp=(new \App\Http\Traits\PresensiHitungRutinFunction)->getWaktuKerja(['id_jenis_jadwal'=>1])->first();

        if($data_jadwal_kerja_tmp){

            $masuk_kerja=!empty($data_jadwal_kerja_tmp->masuk_kerja) ? $data_jadwal_kerja_tmp->masuk_kerja : '';
            $pulang_kerja=!empty($data_jadwal_kerja_tmp->pulang_kerja) ? $data_jadwal_kerja_tmp->pulang_kerja : '';
            $awal_istirahat=!empty($data_jadwal_kerja_tmp->awal_istirahat) ? $data_jadwal_kerja_tmp->awal_istirahat : '';
            $akhir_istirahat=!empty($data_jadwal_kerja_tmp->akhir_istirahat) ? $data_jadwal_kerja_tmp->akhir_istirahat : '';

            $masuk_kerja_sec=$this->absensif->his_to_seconds($masuk_kerja);
            $pulang_kerja_sec=$this->absensif->his_to_seconds($pulang_kerja);
            $awal_istirahat_sec=$this->absensif->his_to_seconds($awal_istirahat);
            $akhir_istirahat_sec=$this->absensif->his_to_seconds($akhir_istirahat);

            $total_istirahat_sec=$akhir_istirahat_sec-$awal_istirahat_sec;
            $total_kerja_default_sec=($pulang_kerja_sec-$masuk_kerja_sec)-$total_istirahat_sec;
            $data_jadwal_kerja_tmp=(array)$data_jadwal_kerja_tmp;
            $data_jadwal_kerja_tmp['total_kerja_sec']=$total_kerja_default_sec;
            $data_jadwal_kerja_tmp['total_kerja']=gmdate("H:i:s", $total_kerja_default_sec);
            if(!empty($data_jadwal_kerja_tmp['data_jadwal'])){
                unset($data_jadwal_kerja_tmp['data_jadwal']);
            }
            $data_jadwal_kerja_tmp=(object)$data_jadwal_kerja_tmp;
        }

        return $data_jadwal_kerja_tmp;
    }

    public function rumus_3_jadwal($hasil_presensi,$data_jadwal_kerja){
        $list_kd_jadwal=[1,2,4];

        $list_status=$this->absensif->get_list_data_presensi();

        if($hasil_presensi and $data_jadwal_kerja){
            $masuk_kerja=!empty($data_jadwal_kerja->masuk_kerja) ? $data_jadwal_kerja->masuk_kerja : '';
            $pulang_kerja=!empty($data_jadwal_kerja->pulang_kerja) ? $data_jadwal_kerja->pulang_kerja : '';
            $awal_istirahat=!empty($data_jadwal_kerja->awal_istirahat) ? $data_jadwal_kerja->awal_istirahat : '';
            $akhir_istirahat=!empty($data_jadwal_kerja->akhir_istirahat) ? $data_jadwal_kerja->akhir_istirahat : '';

            $masuk_kerja_sec=$this->absensif->his_to_seconds($masuk_kerja);
            $pulang_kerja_sec=$this->absensif->his_to_seconds($pulang_kerja);
            $awal_istirahat_sec=$this->absensif->his_to_seconds($awal_istirahat);
            $akhir_istirahat_sec=$this->absensif->his_to_seconds($akhir_istirahat);

            $total_istirahat_sec=$akhir_istirahat_sec-$awal_istirahat_sec;
            $total_kerja_default_sec=($pulang_kerja_sec-$masuk_kerja_sec)-$total_istirahat_sec;

            $user_presensi_masuk=!empty($hasil_presensi[1]) ? $hasil_presensi[1] : [];
            $user_presensi_masuk=!empty($user_presensi_masuk->user_presensi) ? $user_presensi_masuk->user_presensi : '00:00:00';
            $user_presensi_masuk_sec=$this->absensif->his_to_seconds($user_presensi_masuk);

            $user_presensi_istirahat=!empty($hasil_presensi[2]) ? $hasil_presensi[2] : [];
            $user_presensi_istirahat=!empty($user_presensi_istirahat->user_presensi) ? $user_presensi_istirahat->user_presensi : '00:00:00';
            $user_presensi_istirahat_sec=$this->absensif->his_to_seconds($user_presensi_istirahat);

            $user_presensi_pulang=!empty($hasil_presensi[4]) ? $hasil_presensi[4] : [];
            $user_presensi_pulang=!empty($user_presensi_pulang->user_presensi) ? $user_presensi_pulang->user_presensi : '00:00:00';
            $user_presensi_pulang_sec=$this->absensif->his_to_seconds($user_presensi_pulang);

            $list_hasil_user_prensensi=[];

            foreach($list_kd_jadwal as $kd_jadwal){
                $hasil_check='a';
                if(!empty($hasil_presensi[$kd_jadwal])){
                    $gd=$hasil_presensi[$kd_jadwal];
                    $gd=!empty($gd->hasil_check) ? $gd->hasil_check : '';

                    //abaikan jika gak ada data
                    if(!empty($gd->status_presensi)){
                        $value_check=str_replace($kd_jadwal,'',$gd->status_presensi);
                        $value_check=!empty($value_check) ? $value_check : 'h';

                        if($value_check=='-'){
                            $hasil_check=1;
                        }elseif($value_check=='h'){
                            $hasil_check=2;
                        }elseif($value_check=='+'){
                            $hasil_check=3;
                        }
                    }
                }
                $list_hasil_user_prensensi[]=$hasil_check;
            }


            $list_rumus=[
                'list_alpa'=>[
                    ['a',2,'a'],['a','a',2],['a','a','a'],['a','a',3],['a','a',1],['a',3,'a'],['a',1,'a'],
                    [2,'a','a'],[3,'a','a'],[1,'a','a']
                ],
                'list_normal'=>[
                    [2,2,2],[2,2,3],[2,'a',2],[2,'a',3],[2,3,2],[2,3,3],[2,1,2],[2,1,3],[1,2,2],[1,2,3],[1,'a',2],[1,'a',3],[1,3,2],[1,3,3],[1,1,2],[1,1,3],
                ],
                'list_p1'=>[
                    [3,2,2],[3,2,3],[3,'a',2],[3,'a',3],[3,3,2],[3,3,3],[3,1,2],[3,1,3],
                ],
                'list_p2'=>[
                    ['a',2,2],['a',2,3],['a',3,2],['a',3,3],['a',1,2],['a',1,3],
                ],
                'list_p3'=>[
                    [2,2,'a'],[2,3,'a'],[2,1,'a'],[1,2,'a'],[1,3,'a'],[1,1,'a'],
                ],
                'list_p4'=>[
                    [3,2,'a'],[3,3,'a'],[3,1,'a'],
                ],
                'list_p5'=>[
                    [2,2,1],[2,'a',1],[2,3,1],[2,1,1],[1,2,1],[1,'a',1],[1,3,1],[1,1,1],
                ],
                'list_p6'=>[
                    [3,2,1],[3,'a',1],[3,3,1],[3,1,1],
                ],
                'list_p7'=>[
                    ['a',2,1],['a',3,1],['a',1,1],
                ],
            ];

            $get_hasil_rumus=[];
            foreach($list_rumus as $key => $val){
                $check_ada=0;
                foreach($val as $key_c => $val_c){
                    if($val_c===$list_hasil_user_prensensi){
                        $check_ada++;
                    }
                }
                if($check_ada){
                    $get_hasil_rumus[$key]=$check_ada;
                }
            }

            if(!empty($get_hasil_rumus['list_alpa'])){
                /*
                    masuk= Alpa,
                    istirahat= alpa,hadir,telat,cepat,
                    pulang= alpa,hadir,telat,cepat,
                */
                $mulai_kerja_sec=0;
                $pulang_kerja_sec=0;
                $total_kerja_sec=0;
                $status_kerja_text=[
                    'text'=>$list_status[6]['text'],
                    'alias'=>$list_status[6]['alias'],
                ];

            }elseif(!empty($get_hasil_rumus['list_normal'])){
                /*
                    masuk= hadir,cepat,
                    istirahat= alpa,hadir,telat,cepat,
                    pulang= hadir,telat,
                */
                $mulai_kerja_sec=$masuk_kerja_sec;
                $pulang_kerja_sec=$pulang_kerja_sec;
                $total_kerja_sec=$total_kerja_default_sec;
                $status_kerja_text=[
                    'text'=>$list_status[1]['text'],
                    'alias'=>$list_status[1]['alias'],
                ];
            }elseif(!empty($get_hasil_rumus['list_p1'])){
                /*
                    masuk= telat,
                    istirahat= alpa,hadir,telat,cepat,
                    pulang= hadir,telat,
                */
                $mulai_kerja_sec=$user_presensi_masuk_sec;
                $pulang_kerja_sec=$pulang_kerja_sec;
                $total_kerja_sec=($pulang_kerja_sec-$mulai_kerja_sec)-$total_istirahat_sec;
                $status_kerja_text=[
                    'text'=>$list_status[2]['text'],
                    'alias'=>$list_status[2]['alias'],
                ];
            }elseif(!empty($get_hasil_rumus['list_p2'])){
                /*
                    masuk= alpa,
                    istirahat= hadir,telat,cepat,
                    pulang= hadir,telat,
                */
                $mulai_kerja_sec=$akhir_istirahat_sec;
                $pulang_kerja_sec=$pulang_kerja_sec;
                $total_kerja_sec=$pulang_kerja_sec-$mulai_kerja_sec;
                $status_kerja_text=[
                    'text'=>$list_status[3]['text'],
                    'alias'=>$list_status[3]['alias'],
                ];
            }elseif(!empty($get_hasil_rumus['list_p3'])){
                /*
                    masuk= hadir,cepat,
                    istirahat= hadir,telat,cepat,
                    pulang= alpa,
                */
                $mulai_kerja_sec=$masuk_kerja_sec;
                $pulang_kerja_sec=$awal_istirahat_sec;
                $total_kerja_sec=$pulang_kerja_sec-$mulai_kerja_sec;
                $status_kerja_text=[
                    'text'=>$list_status[4]['text'],
                    'alias'=>$list_status[4]['alias'],
                ];
            }elseif(!empty($get_hasil_rumus['list_p4'])){
                /*
                    masuk= telat,
                    istirahat= hadir,telat,cepat,
                    pulang= alpa,
                */
                $mulai_kerja_sec=$user_presensi_masuk_sec;
                $pulang_kerja_sec=$awal_istirahat_sec;
                $total_kerja_sec=$pulang_kerja_sec-$mulai_kerja_sec;
                $status_kerja_text=[
                    'text'=>$list_status[2]['text'].','.$list_status[4]['text'],
                    'alias'=>$list_status[2]['alias'].$list_status[4]['alias'],
                ];

            }elseif(!empty($get_hasil_rumus['list_p5'])){
                /*
                    masuk= hadir,cepat,
                    istirahat= hadir,alpa,telat,cepat,
                    pulang= cepat,
                */
                $mulai_kerja_sec=$masuk_kerja_sec;
                $pulang_kerja_sec=$user_presensi_pulang_sec;
                $total_kerja_sec=($pulang_kerja_sec-$mulai_kerja_sec)-$total_istirahat_sec;

                $status_kerja_text=[
                    'text'=>$list_status[5]['text'],
                    'alias'=>$list_status[5]['alias'],
                ];
            }elseif(!empty($get_hasil_rumus['list_p6'])){
                /*
                    masuk= telat,
                    istirahat= hadir,alpa,telat,cepat,
                    pulang= cepat,
                */
                $mulai_kerja_sec=$user_presensi_masuk_sec;
                $pulang_kerja_sec=$user_presensi_pulang_sec;
                $total_kerja_sec=($pulang_kerja_sec-$mulai_kerja_sec)-$total_istirahat_sec;

                $status_kerja_text=[
                    'text'=>$list_status[2]['text'].','.$list_status[5]['text'],
                    'alias'=>$list_status[2]['alias'].$list_status[5]['alias'],
                ];
            }elseif(!empty($get_hasil_rumus['list_p7'])){
                /*
                    masuk= alpa,
                    istirahat= hadir,telat,cepat,
                    pulang= cepat,
                */
                $mulai_kerja_sec=$akhir_istirahat_sec;
                $pulang_kerja_sec=$user_presensi_pulang_sec;
                $total_kerja_sec=($pulang_kerja_sec-$mulai_kerja_sec);

                $status_kerja_text=[
                    'text'=>$list_status[3]['text'].','.$list_status[5]['text'],
                    'alias'=>$list_status[3]['alias'].$list_status[5]['alias'],
                ];
            }

            return (object)[
                'mulai_kerja_sec'=>!empty($mulai_kerja_sec) ? $mulai_kerja_sec : 0,
                'mulai_kerja'=>!empty($mulai_kerja_sec) ? gmdate("H:i:s", $mulai_kerja_sec) : '00:00:00',
                'pulang_kerja_sec'=>!empty($pulang_kerja_sec) ? $pulang_kerja_sec : 0,
                'pulang_kerja'=>!empty($pulang_kerja_sec) ? gmdate("H:i:s", $pulang_kerja_sec) : '00:00:00',
                'total_kerja_sec'=>!empty($total_kerja_sec) ? $total_kerja_sec : 0,
                'total_kerja'=>!empty($total_kerja_sec) ? gmdate("H:i:s", $total_kerja_sec) : '00:00:00',
                'status_kerja_text'=>!empty($status_kerja_text) ? (object)$status_kerja_text : '',
                'kode_uniq_perhitungan'=>!empty($list_hasil_user_prensensi) ? $list_hasil_user_prensensi : [],
            ];
        }
    }

    public function getProses($data=[]){
        $list_presensi=!empty($data['list_presensi']) ? explode(',',$data['list_presensi']) : '';
        $list_data=!empty($data['list_data']) ? $data['list_data'] : '';
        $data_jadwal_kerja=!empty($data['data_jadwal_kerja']) ? $data['data_jadwal_kerja'] : '';
        $data_jadwal_mesin=!empty($data_jadwal_kerja->data_jadwal) ? $data_jadwal_kerja->data_jadwal : '';

        $hasil_presensi_by_mesin=$this->absensif->get_presensi_by_jadwal_mesin($data_jadwal_mesin,$list_presensi);
        
        $hasil_presensi_user=!empty($hasil_presensi_by_mesin->hasil_presensi) ? $hasil_presensi_by_mesin->hasil_presensi : '';

        $hasil_hitung=$this->rumus_3_jadwal($hasil_presensi_user,$data_jadwal_kerja);

        /* Tambahkan Data Jadwal Kerja */
        $data_jadwal_kerja_tmp=$data_jadwal_kerja;
        if($data_jadwal_kerja_tmp){

            $masuk_kerja=!empty($data_jadwal_kerja_tmp->masuk_kerja) ? $data_jadwal_kerja_tmp->masuk_kerja : '';
            $pulang_kerja=!empty($data_jadwal_kerja_tmp->pulang_kerja) ? $data_jadwal_kerja_tmp->pulang_kerja : '';
            $awal_istirahat=!empty($data_jadwal_kerja_tmp->awal_istirahat) ? $data_jadwal_kerja_tmp->awal_istirahat : '';
            $akhir_istirahat=!empty($data_jadwal_kerja_tmp->akhir_istirahat) ? $data_jadwal_kerja_tmp->akhir_istirahat : '';

            $masuk_kerja_sec=$this->absensif->his_to_seconds($masuk_kerja);
            $pulang_kerja_sec=$this->absensif->his_to_seconds($pulang_kerja);
            $awal_istirahat_sec=$this->absensif->his_to_seconds($awal_istirahat);
            $akhir_istirahat_sec=$this->absensif->his_to_seconds($akhir_istirahat);

            $total_istirahat_sec=$akhir_istirahat_sec-$awal_istirahat_sec;
            $total_kerja_default_sec=($pulang_kerja_sec-$masuk_kerja_sec)-$total_istirahat_sec;
            $data_jadwal_kerja_tmp=(array)$data_jadwal_kerja_tmp;
            $data_jadwal_kerja_tmp['total_kerja_sec']=$total_kerja_default_sec;
            $data_jadwal_kerja_tmp['total_kerja']=gmdate("H:i:s", $total_kerja_default_sec);
            if(!empty($data_jadwal_kerja_tmp['data_jadwal'])){
                unset($data_jadwal_kerja_tmp['data_jadwal']);
            }
            $data_jadwal_kerja_tmp=(object)$data_jadwal_kerja_tmp;
        }

        $jadwal_open_mesin=[];
        $hpresensi_user=!empty($hasil_presensi_by_mesin->hasil_presensi) ? $hasil_presensi_by_mesin->hasil_presensi : [];

        if(!empty($hasil_presensi_by_mesin->jadwal_mesin)){
            foreach($hasil_presensi_by_mesin->jadwal_mesin as $key => $val){

                $user_presensi='';
                $status_presensi='';
                $type_waktu='';
                $selisih_waktu_sec=0;
                $selisih_waktu=[];

                if(!empty($hpresensi_user[$key])){
                    $data=$hpresensi_user[$key];
                    $user_presensi=!empty($data->user_presensi) ? $data->user_presensi : '';
                    if(!empty($data->hasil_check)){
                        $tt=$data->hasil_check;
                        $status_presensi=!empty($tt->status_presensi) ? $tt->status_presensi : '';
                        $type_waktu=!empty($tt->type_waktu) ? $tt->type_waktu : '';
                        $selisih_waktu_sec=!empty($tt->selisih_waktu_sec) ? $tt->selisih_waktu_sec : 0;
                        $selisih_waktu=!empty($tt->selisih_waktu) ? (array)$tt->selisih_waktu : [];
                    }
                }

                $hasil=[
                    'user_presensi'=>!empty($user_presensi) ? $user_presensi : '',
                    'status_presensi'=>!empty($status_presensi) ? $status_presensi : '',
                    'type_waktu'=>!empty($type_waktu) ? $type_waktu : '',
                    'selisih_waktu_sec'=>!empty($selisih_waktu_sec) ? $selisih_waktu_sec : '',
                    'selisih_waktu'=>!empty($selisih_waktu) ? $selisih_waktu : '',
                ];

                $val_tmp=(array)$val;
                $jadwal_open_mesin[$key]=$val_tmp;
                $jadwal_open_mesin[$key]['user_presensi']=$hasil;
                $jadwal_open_mesin[$key]=(object)$jadwal_open_mesin[$key];
            }
        }

        return [
            'jadwal_kerja'=>$data_jadwal_kerja_tmp,
            'jadwal_open_mesin'=>$jadwal_open_mesin ?? [],
            'hasil_hitung_kerja'=>$hasil_hitung
        ];
    }

    public function getHitungRutin($data=[]){
        $hasil_proses=(new \App\Http\Traits\PresensiHitungRutinFunction)->getProses($data);
        
        
        $get_data_presensi_user=!empty($hasil_proses) ? (object)$hasil_proses : '';
        $get_open_mesin=!empty($get_data_presensi_user->jadwal_open_mesin) ? $get_data_presensi_user->jadwal_open_mesin : '';
        $presensi_jadwal=[];
        $presensi_jadwal = [];

        if(is_array($get_open_mesin) && !empty($get_open_mesin)){
            foreach($get_open_mesin as $gom){

                $get_gom_presensi = !empty($gom->user_presensi) 
                    ? (object)$gom->user_presensi 
                    : null;

                if(!empty($get_gom_presensi->user_presensi)){
                    $presensi_jadwal[] = $get_gom_presensi->user_presensi;
                }
            }
        }
        $presensi_jadwal=!empty($presensi_jadwal) ? implode(',',$presensi_jadwal) : '';
        $get_jadwal_kerja=!empty($get_data_presensi_user->jadwal_kerja) ? $get_data_presensi_user->jadwal_kerja : '';
        $total_waktu_kerja_sec=!empty($get_jadwal_kerja->total_kerja_sec) ? $get_jadwal_kerja->total_kerja_sec : 0;

        $get_kerja_user=!empty($get_data_presensi_user->hasil_hitung_kerja) ? $get_data_presensi_user->hasil_hitung_kerja : '';
        $total_waktu_kerja_user_sec=!empty($get_kerja_user->total_kerja_sec) ? $get_kerja_user->total_kerja_sec : 0;
        $status_kerja_user=!empty($get_kerja_user->status_kerja_text) ? $get_kerja_user->status_kerja_text : '';

        $hasil=[
            'presensi_user'=>trim($presensi_jadwal),
            'total_waktu_kerja_user_sec'=>$total_waktu_kerja_user_sec,
            'total_waktu_kerja_sistem_sec'=>$total_waktu_kerja_sec,
            'status_kerja'=>$status_kerja_user,
            'id_jenis_jadwal'=>!empty($get_jadwal_kerja->id_jenis_jadwal) ? $get_jadwal_kerja->id_jenis_jadwal : null,
        ];

        return $hasil;
    }

}

class PresensiHitungRutinFunction {
    use presensiHitungRutinTraits;
}

?>