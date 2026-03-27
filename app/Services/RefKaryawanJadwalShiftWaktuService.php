<?php

namespace App\Services;

use App\Models\RefKaryawanJadwalShiftWaktu;
use Illuminate\Support\Facades\DB;

class RefKaryawanJadwalShiftWaktuService extends BaseService
{
    public $refKaryawanJadwalShiftWaktu='';

    public function __construct(){
        parent::__construct();
        $this->refKaryawanJadwalShiftWaktu = new RefKaryawanJadwalShiftWaktu();
    }

    function getData($params=[],$type=''){

        $tahun=!empty($params['tahun']) ? $params['tahun'] : date('Y');
        $bulan=!empty($params['bulan']) ? $params['bulan'] : date('m');
        $id_karyawan=!empty($params['id_karyawan']) ? $params['id_karyawan'] : '';
        $id_template_jadwal_shift=!empty($params['id_template_jadwal_shift']) ? $params['id_template_jadwal_shift'] : '';

        unset($params['tahun']);
        unset($params['bulan']);
        unset($params['id_karyawan']);
        unset($params['id_template_jadwal_shift']);

        $where_karyawan='';
        if(!empty($id_karyawan)){
            $where_karyawan="id_karyawan in (".$id_karyawan.")";
        }

        $where_id_template_jadwal_shift='';
        if(!empty($id_template_jadwal_shift)){
            $where_id_template_jadwal_shift="id_template_jadwal_shift in (".$id_template_jadwal_shift.")";
        }
        

        $query=DB::table(DB::raw(
            '(
                select 
                    utama.*,nm_karyawan,id_jabatan,id_departemen,id_ruangan,id_status_karyawan
                from(
                    select 
                        * 
                    from ref_karyawan_jadwal_shift_waktu
                    where 
                    year(tanggal)='.$tahun.'
                    and MONTH(tanggal)='.$bulan.'
                    '.(!empty($where_karyawan) ? 'and '.$where_karyawan : '').'
                    '.(!empty($where_id_template_jadwal_shift) ? 'and '.$where_id_template_jadwal_shift : '').'
                )utama
                inner join ref_karyawan karyawan on karyawan.id_karyawan=utama.id_karyawan
            )utama'
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

    function getDataList($params=[],$type=''){
        $list_tamplate_user_tmp=$this->getData($params,$type);

        $data_jadwal_shift_by_sistem=[];
        $get_data_jadwal=(new \App\Services\RefJadwalService())->getListJadwal(['type_jenis'=>2,'status_jadwal'=>1],1)->orderBy('kd_jadwal','ASC')->get();
        foreach($get_data_jadwal as $item){
            $data_jadwal_shift_by_sistem[$item->id_jenis_jadwal][$item->kd_jadwal]=$item;
        }

        $list_tamplate_user=[];
        if(!empty($list_tamplate_user_tmp)){
            foreach($list_tamplate_user_tmp as $item){
                
                $data_jadwal_tmp=!empty($item->data) ? json_decode($item->data) : '';
                $data_jadwal_tmp2=[];
                if(!empty($data_jadwal_tmp)){
                    foreach($data_jadwal_tmp as $dj){
                        $type_jenis=0;
                        $masuk_kerja='';
                        $pulang_kerja='';
                        $pulang_kerja_next_day='';
                        
                        if(empty($dj->type_jenis)){
                            if(empty($dj->id_jenis_jadwal)){
                                $type_jenis=$dj->type;
                                $nm_jenis_jadwal="libur";
                                $bg_color='#e1dede';
                            }
                        }else{
                            $type_jenis=$dj->type_jenis;
                        }

                        $get_data_jadwal='';
                        if(!empty($data_jadwal_shift_by_sistem[$dj->id_jenis_jadwal])){
                            $get_data_jadwal_tmp=$data_jadwal_shift_by_sistem[$dj->id_jenis_jadwal];
                            $get_data_jadwal_user=$get_data_jadwal_tmp[array_key_first($get_data_jadwal_tmp)];
                            
                            $nm_jenis_jadwal=!empty($get_data_jadwal_user->nm_jenis_jadwal) ? $get_data_jadwal_user->nm_jenis_jadwal : '';
                            $masuk_kerja=!empty($get_data_jadwal_user->masuk_kerja) ? $get_data_jadwal_user->masuk_kerja : '';
                            $pulang_kerja= !empty($get_data_jadwal_user->pulang_kerja) ? $get_data_jadwal_user->pulang_kerja : '';
                            $pulang_kerja_next_day = !empty($get_data_jadwal_user->pulang_kerja_next_day) ? $get_data_jadwal_user->pulang_kerja_next_day : '';
                            $bg_color=!empty($get_data_jadwal_user->bg_color) ? $get_data_jadwal_user->bg_color : '';
                        }
                        $data_jadwal_tmp2[]=[
                            'id_template_jadwal_shift'=>$item->id_template_jadwal_shift,
                            'tgl_mulai'=>$item->tanggal,
                            'id_jenis_jadwal'=>$dj->id_jenis_jadwal,
                            'nm_jenis_jadwal'=>!empty($nm_jenis_jadwal) ? $nm_jenis_jadwal : '',
                            "masuk_kerja" => !empty($masuk_kerja) ? $masuk_kerja : '',
                            "pulang_kerja" => !empty($pulang_kerja) ? $pulang_kerja : '',
                            "pulang_kerja_next_day" => !empty($pulang_kerja_next_day) ? $pulang_kerja_next_day : '',
                            "bg_color" => $bg_color,
                            "type_jadwal" => $type_jenis,
                        ];
                    }
                }
                $list_tamplate_user[$item->id_karyawan][$item->id_template_jadwal_shift][$item->tanggal]=$data_jadwal_tmp2;                
            }
        }

        return $list_tamplate_user;
    }
}