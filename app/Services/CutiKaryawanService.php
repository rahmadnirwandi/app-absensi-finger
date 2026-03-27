<?php

namespace App\Services;

use App\Models\CutiKaryawan;
use Illuminate\Support\Facades\DB;

class CutiKaryawanService extends BaseService
{
    public $cutiKaryawan='';

    public function __construct(){
        parent::__construct();
        $this->cutiKaryawan = new CutiKaryawan;
    }

    function getList($params=[],$type=''){
        $query = $this->cutiKaryawan
            ->select('id_cuti_kary','ref_cuti_karyawan.id_karyawan','ref_karyawan.nip','ref_karyawan.nm_karyawan','nm_jabatan','nm_departemen','nm_ruangan','ref_cuti_karyawan.uraian','ref_cuti_karyawan.tgl_mulai','ref_cuti_karyawan.tgl_selesai','ref_cuti_karyawan.jumlah')
            ->Leftjoin('ref_karyawan','ref_cuti_karyawan.id_karyawan','=','ref_karyawan.id_karyawan')
            ->Leftjoin('ref_jabatan','ref_jabatan.id_jabatan','=','ref_karyawan.id_jabatan')
            ->Leftjoin('ref_departemen','ref_departemen.id_departemen','=','ref_karyawan.id_departemen')
            ->Leftjoin('ref_ruangan','ref_ruangan.id_ruangan','=','ref_karyawan.id_ruangan')
            ->orderBy('nm_karyawan','ASC')
        ;

        $list_search=[
            'where_or'=>['ref_karyawan.nm_karyawan','ref_karyawan.alamat','nip','nm_jabatan','nm_departemen'],
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

    // public function getDataCuti($params=[],$type){
    //     ini_set("memory_limit","800M");
    //     DB::statement("SET GLOBAL group_concat_max_len = 15000;");

    //     $tgl_awal=!empty($params['tanggal'][0]) ? $params['tanggal'][0] : date('Y-m-d');
    //     $tgl_akhir=!empty($params['tanggal'][1]) ? $params['tanggal'][1] : date('Y-m-d');

    //     unset($params['tanggal']);
        
    //     $query=DB::table(DB::raw(
    //         '(
    //             select
    //                 JSON_OBJECTAGG(
    //                     id_karyawan,data_cuti
    //                 ) hasil
    //             from(
    //                 select
    //                     utama.*,
    //                     json_object(
    //                         "waktu",
    //                         JSON_ARRAYAGG(
    //                             JSON_array(
    //                                 tgl_mulai,tgl_selesai,jml,uraian
    //                             )
    //                         ),
    //                         "nm_karyawan",
    //                         nm_karyawan
    //                     ) as data_cuti
    //                 from
    //                 (
    //                     select utama.*,nm_karyawan,rd.id_departemen,nm_departemen,rr.id_ruangan,nm_ruangan
    //                     from
    //                     (
    //                         select
    //                             id_karyawan,uraian,tgl_mulai,tgl_selesai,if(tgl_selesai>=tgl_mulai,(DATEDIFF(tgl_selesai,tgl_mulai)),0) jml
    //                         from ref_cuti_karyawan
    //                         where
    //                             ( tgl_mulai between "'.$tgl_awal.'" and "'.$tgl_akhir.'" ) or
    //                             ( tgl_selesai between "'.$tgl_awal.'" and "'.$tgl_akhir.'" )
    //                     ) utama
    //                     inner join ref_karyawan rk on rk.id_karyawan=utama.id_karyawan
    //                     left join ref_departemen rd on rd.id_departemen=rk.id_departemen
    //                     left join ref_ruangan rr on rr.id_ruangan=rk.id_ruangan
    //                     '.(!empty($params['search']) ? "where nm_karyawan like '%".$params['search']."%'" : '' ).'
    //                 )utama
    //                 group by id_karyawan
    //             )utama
    //         ) utama'
    //     ));

    //     $list_search=[];

    //     if($params){
    //         $query=(new \App\Models\MyModel)->set_where($query,$params,$list_search);
    //     }

    //     if(empty($type)){
    //         return $query->get();
    //     }else{
    //         return $query;
    //     }
    // }

    public function getDataCuti($params = [], $type)
    {
        ini_set("memory_limit", "800M");

        $tgl_awal  = !empty($params['tanggal'][0]) ? $params['tanggal'][0] : date('Y-m-d');
        $tgl_akhir = !empty($params['tanggal'][1]) ? $params['tanggal'][1] : date('Y-m-d');

        unset($params['tanggal']);

        $query = DB::table('ref_cuti_karyawan as rc')
            ->select(
                'rc.id_karyawan',
                'rc.uraian',
                'rc.tgl_mulai',
                'rc.tgl_selesai',
                DB::raw('IF(rc.tgl_selesai >= rc.tgl_mulai, DATEDIFF(rc.tgl_selesai, rc.tgl_mulai), 0) as jml'),
                'rk.nm_karyawan',
                'rd.id_departemen',
                'rd.nm_departemen',
                'rr.id_ruangan',
                'rr.nm_ruangan'
            )
            ->join('ref_karyawan as rk', 'rk.id_karyawan', '=', 'rc.id_karyawan')
            ->leftJoin('ref_departemen as rd', 'rd.id_departemen', '=', 'rk.id_departemen')
            ->leftJoin('ref_ruangan as rr', 'rr.id_ruangan', '=', 'rk.id_ruangan')
            ->where(function ($q) use ($tgl_awal, $tgl_akhir) {
                $q->whereBetween('rc.tgl_mulai', [$tgl_awal, $tgl_akhir])
                ->orWhereBetween('rc.tgl_selesai', [$tgl_awal, $tgl_akhir]);
            });

        // optional search
        if (!empty($params['search'])) {
            $query->where('rk.nm_karyawan', 'like', '%' . $params['search'] . '%');
        }

        // optional filter departemen
        if (!empty($params['id_departemen'])) {
            $query->where('rk.id_departemen', $params['id_departemen']);
        }

        // optional filter ruangan
        if (!empty($params['id_ruangan'])) {
            $query->where('rk.id_ruangan', $params['id_ruangan']);
        }

        if (empty($type)) {
            return $query->get();
        }

        // ====== BUILD JSON DI PHP (AMAN MYSQL 5.7) ======

        $data = $query->get()->groupBy('id_karyawan');

        $result = [];

        foreach ($data as $id => $rows) {

            $first = $rows->first();

            $result[$id] = [
                'nm_karyawan'   => $first->nm_karyawan,
                'id_departemen' => $first->id_departemen,
                'nm_departemen' => $first->nm_departemen,
                'id_ruangan'    => $first->id_ruangan,
                'nm_ruangan'    => $first->nm_ruangan,
                'waktu' => $rows->map(function ($r) {
                    return [
                        $r->tgl_mulai,
                        $r->tgl_selesai,
                        $r->jml,
                        $r->uraian
                    ];
                })->values()
            ];
        }

        // Supaya tetap kompatibel dengan controller lama
        return collect([
            (object)[
                'hasil' => json_encode($result)
            ]
        ]);
    }

}