<?php

namespace App\Services;
use Illuminate\Support\Facades\DB;

class RefDataAbsensiTmpService extends BaseService
{
    public function __construct(){
        parent::__construct();
    }

    // public function getList2($params=[]){
    //     ini_set("memory_limit","800M");

    //     $query=DB::table(DB::raw(
    //         '(
    //             select
    //                 ref_data_absensi_tmp.id_mesin_absensi,
    //                 nm_mesin,
    //                 lokasi_mesin,
    //                 id_user,
    //                 waktu as waktu_absensi,
    //                 verified as verified_mesin,
    //                 status as status_absensi_mesin
    //             from
    //                 ref_data_absensi_tmp
    //                 inner join ref_mesin_absensi rma on rma.id_mesin_absensi = ref_data_absensi_tmp.id_mesin_absensi
    //             where date(waktu) BETWEEN "'.$params['tanggal']['start'].'" and "'.$params['tanggal']['end'].'"
    //             order by
    //             UNIX_TIMESTAMP( waktu_absensi )
    //         ) utama'
    //         ))
    //     ;

    //     return $query;
    // }

    public function getList($params=[]){
        ini_set("memory_limit","800M");

        $sql_limit='';
        if(!empty($params['limit'])){
            $limit=$params['limit'];
            $limit_start=!empty($limit['start']) ? $limit['start'] : 0;
            $limit_end=!empty($limit['end']) ? $limit['end'] : 0;
            $sql_limit="LIMIT ".$limit_start.",".$limit_end;
        }

        $where=[];
        if(!empty($params['id_mesin_absensi'])){
            $where[]=' ref_data_absensi_tmp.id_mesin_absensi = '.$params['id_mesin_absensi'];
        }

        if(!empty($params['tanggal'])){
            $where[]=' date(waktu) BETWEEN "'.$params['tanggal']['start'].'" and "'.$params['tanggal']['end'].'" ';
        }

        if(!empty($where)){
            $where=" where ".implode(' and ',$where);
        }
        
        $query=DB::table(DB::raw('(select
                    ref_data_absensi_tmp.id_mesin_absensi,
                    nm_mesin,
                    lokasi_mesin,
                    id_user,
                    waktu as waktu_absensi,
                    verified as verified_mesin,
                    status as status_absensi_mesin
                from
                    ref_data_absensi_tmp
                    inner join ref_mesin_absensi rma on rma.id_mesin_absensi = ref_data_absensi_tmp.id_mesin_absensi
                '.$where.'
                '.$sql_limit.'
                ) utama')
                )
                ->leftJoin(DB::raw('(select
                                    utama.id_user as idu1,
                                    name as username,
                                    karyawan.*
                                from ref_user_info utama
                                left join (
                                    select 
                                        rk.*,
                                        id_user as idu,
                                        id_jenis_jadwal,
                                        nm_jabatan,
                                        nm_departemen
                                    from ref_karyawan_user rku
                                    inner join ref_karyawan rk on rk.id_karyawan=rku.id_karyawan
                                    left join ref_karyawan_jadwal rkj on rkj.id_karyawan=rk.id_karyawan
                                    left join ref_jabatan jabatan on jabatan.id_jabatan=rk.id_jabatan
                                    left join ref_departemen departemen on departemen.id_departemen=rk.id_departemen
                                )karyawan on karyawan.idu=utama.id_user) data_user'),'data_user.idu1','=','utama.id_user'
                )
                ->orderByRaw('UNIX_TIMESTAMP( waktu_absensi )','ASC')
        ;

        return $query;
    }

    public function getListCount($params=[]){
        ini_set("memory_limit","800M");

        $sql_limit='';
        if(!empty($params['limit'])){
            $limit=$params['limit'];
            $limit_start=!empty($limit['start']) ? $limit['start'] : 0;
            $limit_end=!empty($limit['end']) ? $limit['end'] : 0;
            $sql_limit="LIMIT ".$limit_start.",".$limit_end;
        }

        $where=[];
        if(!empty($params['id_mesin_absensi'])){
            $where[]=' ref_data_absensi_tmp.id_mesin_absensi = '.$params['id_mesin_absensi'];
        }

        if(!empty($params['tanggal'])){
            $where[]=' date(waktu) BETWEEN "'.$params['tanggal']['start'].'" and "'.$params['tanggal']['end'].'" ';
        }

        if(!empty($where)){
            $where=" where ".implode(' and ',$where);
        }
        
        $query=DB::table(DB::raw('(select
                    count(id_user) as jml
                from
                    ref_data_absensi_tmp
                    inner join ref_mesin_absensi rma on rma.id_mesin_absensi = ref_data_absensi_tmp.id_mesin_absensi
                '.$where.'
                '.$sql_limit.'
                order by UNIX_TIMESTAMP( waktu ) asc
                )utama'))
                ->selectRaw('jml')
        ;

        return $query;
    }

    public function getAbsensiRutin($params = [], $type = 0) {
        $list_id_karyawan = !empty($params['list_id_karyawan']) 
            ? explode(',', $params['list_id_karyawan']) 
            : [];

        $list_id_user = !empty($params['list_id_user']) 
            ? explode(',', $params['list_id_user']) 
            : [];

        // dd($list_id_karyawan, $list_id_user);

         $query = DB::table('ref_karyawan_user as rku')
            ->join('ref_karyawan as karyawan', 'karyawan.id_karyawan', '=', 'rku.id_karyawan')
            ->join('ref_karyawan_jadwal as jadwal_rutin', 'jadwal_rutin.id_karyawan', '=', 'karyawan.id_karyawan')
            ->leftJoin('ref_departemen as rd', 'rd.id_departemen', '=', 'karyawan.id_departemen')
            ->leftJoin('ref_ruangan as rr', 'rr.id_ruangan', '=', 'karyawan.id_ruangan')
            ->select([
                'rku.id_user',
                'karyawan.id_karyawan',
                'karyawan.id_ruangan',
                'karyawan.id_status_karyawan',
                'karyawan.nm_karyawan',
                'karyawan.id_departemen',
                'rd.nm_departemen',
            ]);

        // Filter id_user jika ada
        if (!empty($list_id_user)) {
            $query->whereIn('rku.id_user', $list_id_user);
        }

        // Filter id_karyawan jika ada
        if (!empty($list_id_karyawan)) {
            $query->whereIn('karyawan.id_karyawan', $list_id_karyawan);
        }

        // Search
        if (!empty($params['search'])) {
            $query->where(function ($q) use ($params) {
                $q->where('karyawan.nm_karyawan', 'like', '%' . $params['search'] . '%')
                ->orWhere('rku.id_user', 'like', '%' . $params['search'] . '%');
            });
        }

        // Order (hindari ambiguous)
        $query->orderBy('karyawan.id_departemen', 'ASC')
            ->orderBy('karyawan.id_ruangan', 'ASC')
            ->orderBy('karyawan.id_status_karyawan', 'ASC')
            ->orderBy('karyawan.nm_karyawan', 'ASC');

        // Kalau perlu groupBy, harus lengkap (strict mode safe)
        $query->groupBy([
            'rku.id_user',
            'karyawan.id_karyawan',
            'karyawan.id_ruangan',
            'karyawan.id_status_karyawan',
            'karyawan.nm_karyawan',
            'karyawan.id_departemen',
            'rd.nm_departemen'
        ]);

        if ($type == 0) {
            return $query->get();
        }

        return $query;
    }

    public function get_data_karyawan_absensi_rutin_query($params = [], $type = 0)
    {
        $list_id_karyawan = !empty($params['list_id_karyawan']) 
            ? explode(',', $params['list_id_karyawan']) 
            : [];

        $list_id_user = !empty($params['list_id_user']) 
            ? explode(',', $params['list_id_user']) 
            : [];

        $query = DB::table('ref_karyawan_user as rku')
            ->join('ref_karyawan as karyawan', 'karyawan.id_karyawan', '=', 'rku.id_karyawan')
            ->join('ref_karyawan_jadwal as jadwal_rutin', 'jadwal_rutin.id_karyawan', '=', 'karyawan.id_karyawan')
            ->leftJoin('ref_departemen as rd', 'rd.id_departemen', '=', 'karyawan.id_departemen')
            ->leftJoin('ref_ruangan as rr', 'rr.id_ruangan', '=', 'karyawan.id_ruangan')
            ->select([
                'rku.id_user',
                'karyawan.id_karyawan',
                'karyawan.id_ruangan',
                'karyawan.id_status_karyawan',
                'karyawan.nm_karyawan',
                'karyawan.id_departemen',
                'rd.nm_departemen',
            ]);

        // Filter id_user jika ada
        if (!empty($list_id_user)) {
            $query->whereIn('rku.id_user', $list_id_user);
        }

        // Filter id_karyawan jika ada
        if (!empty($list_id_karyawan)) {
            $query->whereIn('karyawan.id_karyawan', $list_id_karyawan);
        }

        // Search
        if (!empty($params['search'])) {
            $query->where(function ($q) use ($params) {
                $q->where('karyawan.nm_karyawan', 'like', '%' . $params['search'] . '%')
                ->orWhere('rku.id_user', 'like', '%' . $params['search'] . '%');
            });
        }

        // Order (hindari ambiguous)
        $query->orderBy('karyawan.id_departemen', 'ASC')
            ->orderBy('karyawan.id_ruangan', 'ASC')
            ->orderBy('karyawan.id_status_karyawan', 'ASC')
            ->orderBy('karyawan.nm_karyawan', 'ASC');

        // Kalau perlu groupBy, harus lengkap (strict mode safe)
        $query->groupBy([
            'rku.id_user',
            'karyawan.id_karyawan',
            'karyawan.id_ruangan',
            'karyawan.id_status_karyawan',
            'karyawan.nm_karyawan',
            'karyawan.id_departemen',
            'rd.nm_departemen'
        ]);

        if ($type == 0) {
            return $query->get();
        }

        return $query;
    }
}