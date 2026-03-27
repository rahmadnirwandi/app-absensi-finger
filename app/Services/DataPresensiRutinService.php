<?php

namespace App\Services;

use Illuminate\Support\Facades\DB;

class DataPresensiRutinService extends BaseService
{
    public $dataAbsensiKaryawan,$refJadwalService;

    public function __construct(){
        parent::__construct();
    }

    function getListKaryawan($params=[],$type){
        $query=DB::table(DB::raw(
            '(
                select
                    utama.id_karyawan,
                    rku.id_user,
                    id_jenis_jadwal,
                    nm_karyawan,
                    alamat,
                    nip,
                    rj.id_jabatan,
                    nm_jabatan,
                    rd.id_departemen,
                    nm_departemen,
                    rr.id_ruangan,
                    nm_ruangan
                from (
                    select * from ref_karyawan_jadwal where id_jenis_jadwal = 1
                ) utama
                inner join ref_karyawan_user rku on rku.id_karyawan=utama.id_karyawan
                inner join ref_karyawan rk on rk.id_karyawan=utama.id_karyawan
                left join ref_jabatan rj on rj.id_jabatan=rk.id_jabatan
                left join ref_departemen rd on rd.id_departemen=rk.id_departemen
                left join ref_ruangan rr on rr.id_ruangan=rk.id_ruangan
            ) utama'
        ));

        $list_search=[
            'where_or'=>['nm_karyawan'],
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

    public function getListKaryawanPresensi($params=[],$type){

        $search_parameter=!empty($params['search_parameter']) ? $params['search_parameter'] : [];
        $search_data_karyawan=!empty($params['search_karyawan']) ? $params['search_karyawan'] : [];
        $list_id_user=!empty($search_parameter['list_id_user']) ? $search_parameter['list_id_user'] : 0;
        $parameter_first=[
            'tanggal'=>[!empty($search_parameter['tanggal'][0]) ? $search_parameter['tanggal'][0] : date('Y-m-d'),!empty($search_parameter['tanggal'][1]) ? $search_parameter['tanggal'][1] : date('Y-m-d')],
            'list_id_karyawan'=>!empty($search_parameter['list_id_karyawan']) ? $search_parameter['list_id_karyawan'] : 0,
            'list_id_user'=>$list_id_user,
            'limit'=>!empty($params['limit']) ? $params['limit'] : [],
        ];

        // $query=DB::table(DB::raw(
        //     '(
        //         '.(new \App\Services\DataPresensiService)->get_log_per_tgl($parameter_first,1)->toSql().'
        //         LEFT JOIN (
        //             select
        //                 karyawan.*,
        //                 utama.id_user id_user_karyawan,
        //                 nm_jabatan,
        //                 nm_departemen,
        //                 nm_ruangan
        //             from (
        //                 select id_user,id_karyawan from ref_karyawan_user where id_user in ('.$list_id_user.')
        //             ) utama
        //             INNER JOIN ref_karyawan karyawan on utama.id_karyawan=karyawan.id_karyawan
        //             LEFT JOIN ref_jabatan rj on rj.id_jabatan=karyawan.id_jabatan
        //             LEFT JOIN ref_departemen rd on rd.id_departemen=karyawan.id_departemen
        //             LEFT JOIN ref_ruangan rr on rr.id_ruangan=karyawan.id_ruangan
        //         )karyawan on karyawan.id_user_karyawan=utama.id_user
        //     )utama'
        // ));

         $query = DB::table(DB::raw(
            '(
                '.(new \App\Services\DataPresensiService)
                    ->get_log_per_tgl($parameter_first,1)->toSql().'
                LEFT JOIN (
                    select
                        karyawan.*,
                        utama.id_user id_user_karyawan,
                        nm_jabatan,
                        nm_departemen,
                        nm_ruangan
                    from (
                        select id_user,id_karyawan 
                        from ref_karyawan_user
                    ) utama
                    INNER JOIN ref_karyawan karyawan 
                        on utama.id_karyawan=karyawan.id_karyawan
                    LEFT JOIN ref_jabatan rj 
                        on rj.id_jabatan=karyawan.id_jabatan
                    LEFT JOIN ref_departemen rd 
                        on rd.id_departemen=karyawan.id_departemen
                    LEFT JOIN ref_ruangan rr 
                        on rr.id_ruangan=karyawan.id_ruangan
                ) karyawan 
                on karyawan.id_user_karyawan=utama.id_user
            ) utama'
        ));

        $list_search=[
            'where_or'=>['nm_karyawan'],
        ];

        if($search_data_karyawan){
            $query=(new \App\Models\MyModel)->set_where($query,$search_data_karyawan,$list_search);
        }

        if(empty($type)){
            return $query->get();
        }else{
            return $query;
        }

    }


    public function getDataRumus3($params=[]){
        DB::statement("SET GLOBAL group_concat_max_len = 15000;");

        $form_filter_text=!empty($params['form_filter_text']) ? $params['form_filter_text'] : '';
        $filter_date_start=!empty($params['filter_date_start']) ? $params['filter_date_start'] : date('Y-m-d');
        $filter_date_end=!empty($params['filter_date_end']) ? $params['filter_date_end'] : date('Y-m-d');

        $filter_id_jabatan=!empty($params['filter_id_jabatan']) ? $params['filter_id_jabatan'] : '';
        $filter_id_departemen=!empty($params['filter_id_departemen']) ? $params['filter_id_departemen'] : '';
        $filter_id_ruangan=!empty($params['filter_id_ruangan']) ? $params['filter_id_ruangan'] : '';

        $paramater_data_karyawan_rutin=[
            'search'=>$form_filter_text
        ];

        if(!empty($filter_id_jabatan)){
            $paramater_data_karyawan_rutin['id_jabatan']=$filter_id_jabatan;
        }

        if(!empty($filter_id_departemen)){
            $paramater_data_karyawan_rutin['id_departemen']=$filter_id_departemen;
        }

        if(!empty($filter_id_ruangan)){
            $paramater_data_karyawan_rutin['id_ruangan']=$filter_id_ruangan;
        }

        $list_data_karyawan_rutin = $this->getListKaryawan($paramater_data_karyawan_rutin, 1)->select(DB::raw('group_concat(id_karyawan) as id_karyawan'),DB::raw('group_concat(id_user) as id_user'))->first();
        
        // $data_jadwal=(new \App\Http\Traits\PresensiHitungRutinFunction)->getWaktuKerja(['id_jenis_jadwal'=>1])->first(); 
        
        $parameter_search=[
            'search_parameter'=>[
                'tanggal'=>[$filter_date_start,$filter_date_end],
                'list_id_karyawan'=>!empty($list_data_karyawan_rutin->id_karyawan) ? $list_data_karyawan_rutin->id_karyawan : 0,
                'list_id_user'=>!empty($list_data_karyawan_rutin->id_user) ? $list_data_karyawan_rutin->id_user : '',
            ],
            'search_karyawan'=>$paramater_data_karyawan_rutin,
            'limit'=>!empty($params['limit']) ? $params['limit'] : [],
        ];
        
        $list_data=$this->getListKaryawanPresensi($parameter_search,1)->get();

        $filter_presensi_masuk=!empty($params['filter_presensi_masuk']) ? $params['filter_presensi_masuk'] : '';
        $filter_presensi_istirahat=!empty($params['filter_presensi_istirahat']) ? $params['filter_presensi_istirahat'] : '';
        $filter_presensi_pulang=!empty($params['filter_presensi_pulang']) ? $params['filter_presensi_pulang'] : '';

        $kode_uniq_search_status=[
            $filter_presensi_masuk,
            $filter_presensi_istirahat,
            $filter_presensi_pulang
        ];
        $filter_status=0;
        if($filter_presensi_masuk){
            $filter_status++;
        }
        if($filter_presensi_istirahat){
            $filter_status++;
        }
        if($filter_presensi_pulang){
            $filter_status++;
        }

        $list_db=[];
        if($list_data){
            foreach($list_data as $key => $value){
              
                $data_jadwal = (new \App\Http\Traits\PresensiHitungRutinFunction)
                    ->getWaktuKerjaByTanggal([
                        'id_karyawan' => $value->id_karyawan,
                        'tanggal'     => $value->tgl_presensi
                    ])
                    ->first();

                    

                $change_value=$list_data[$key];
                $change_value=(array)$change_value;
                $data_proses=[
                    'list_presensi'=>!empty($value->presensi) ? $value->presensi : '',
                    'list_data'=>!empty($value->presensi_data) ? $value->presensi_data : '',
                    'data_jadwal_kerja'=>!empty($data_jadwal) ? $data_jadwal  : ''
                ];
                
                
                $hasil_proses=(new \App\Http\Traits\PresensiHitungRutinFunction)->getProses($data_proses);
                $get_hasil_proses_hitung_kerja=!empty($hasil_proses['hasil_hitung_kerja']) ? $hasil_proses['hasil_hitung_kerja'] : [];
                $kode_uniq_perhitungan_user=!empty($get_hasil_proses_hitung_kerja->kode_uniq_perhitungan) ? $get_hasil_proses_hitung_kerja->kode_uniq_perhitungan : [];

                $change_value['status_nilai_kerja']=$hasil_proses;

                $change_value['presensi_data']=!empty($change_value['presensi_data']) ? json_decode($change_value['presensi_data']) : '';

                if(!empty($change_value['created'])){
                    unset($change_value['created']);
                }

                $change_value=(object)$change_value;
                $list_data[$key]=$change_value;

                if($filter_status>=1){
                    $hasil_cc=(new \App\Http\Traits\AbsensiFunction)->check_bolean_array($kode_uniq_search_status,$kode_uniq_perhitungan_user);
                    if(empty($hasil_cc)){
                        unset($list_data[$key]);
                    }
                }

                if(!empty($list_data[$key])){
                    $get_data=$list_data[$key];
                    $get_data_presensi_user=!empty($get_data->status_nilai_kerja) ? (object)$get_data->status_nilai_kerja : '';
                    $get_open_mesin=!empty($get_data_presensi_user->jadwal_open_mesin) ? $get_data_presensi_user->jadwal_open_mesin : '';
                    
                    $presensi_jadwal = [];

                    if(!empty($get_open_mesin)) {
                        foreach($get_open_mesin as $gom){
                            $get_gom_presensi=!empty($gom->user_presensi) ? (object)$gom->user_presensi : '';
                            if(!empty($get_gom_presensi->user_presensi)){
                                $presensi_jadwal[]=$get_gom_presensi->user_presensi;
                            }
                        }

                        $presensi_jadwal=!empty($presensi_jadwal) ? implode(',',$presensi_jadwal) : '';
                        $get_jadwal_kerja=!empty($get_data_presensi_user->jadwal_kerja) ? $get_data_presensi_user->jadwal_kerja : '';
                    }

                    $total_waktu_kerja_sec=!empty($get_jadwal_kerja->total_kerja_sec) ? $get_jadwal_kerja->total_kerja_sec : 0;

                    $get_kerja_user=!empty($get_data_presensi_user->hasil_hitung_kerja) ? $get_data_presensi_user->hasil_hitung_kerja : '';
                    $total_waktu_kerja_user_sec=!empty($get_kerja_user->total_kerja_sec) ? $get_kerja_user->total_kerja_sec : 0;
                    $status_kerja_user=!empty($get_kerja_user->status_kerja_text) ? json_encode($get_kerja_user->status_kerja_text) : '';
                    $presensi_detail=!empty($get_data->presensi_data) ? json_encode($get_data->presensi_data) : '';

                    $list_db[]=[
                        'id_user'=>$get_data->id_user,
                        'id_karyawan'=>$get_data->id_karyawan,
                        'id_jabatan'=>$get_data->id_jabatan,
                        'id_departemen'=>$get_data->id_departemen,
                        'id_ruangan'=>$get_data->id_ruangan,
                        'id_status_karyawan'=>$get_data->id_status_karyawan,
                        'tgl_presensi'=>$get_data->tgl_presensi,
                        'presensi_jadwal'=>trim($presensi_jadwal),
                        'presensi_all'=>trim($get_data->presensi),
                        'presensi_detail'=>trim($presensi_detail),
                        'total_waktu_kerja_user_sec'=>$total_waktu_kerja_user_sec,
                        'status_kerja'=>$status_kerja_user,
                        'id_jenis_jadwal'=>!empty($get_jadwal_kerja->id_jenis_jadwal) ? $get_jadwal_kerja->id_jenis_jadwal : null,
                    ];
                }
            }
        }

        return (object)[
            'list_data'=>$list_data,
            'list_db'=>$list_db
        ];
    }
}