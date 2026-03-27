<?php

namespace App\Services;

use DateTime;
use Illuminate\Support\Facades\DB;

class DataPresensiService extends BaseService
{
    public function __construct(){
        parent::__construct();
    }

    public function get_log_per_tgl($params = [], $type)
    {
        ini_set("memory_limit", "800M");
        DB::statement("SET SESSION group_concat_max_len = 15000;");

        $tgl_awal = !empty($params['tanggal'][0]) ? $params['tanggal'][0] : date('Y-m-d');
        $tgl_akhir = !empty($params['tanggal'][1]) ? $params['tanggal'][1] : date('Y-m-d');
        $list_id_user = !empty($params['list_id_user']) ? $params['list_id_user'] : '0';

        $limit_data = !empty($params['limit']) ? $params['limit'] : [];
        $limit_data = (new \App\Http\Traits\GlobalFunction)->limit_mysql_manual($limit_data);
        $limit = '';
        if (!empty($limit_data)) {
            $limit = "LIMIT " . implode(',', $limit_data);
        }

        // MySQL 5.7 compatible - pakai GROUP_CONCAT bukan JSON_OBJECTAGG
        $query = DB::table(DB::raw(
            '(
                SELECT
                    id_user,
                    DATE(waktu) AS tgl_presensi,
                    GROUP_CONCAT(
                        TIME_FORMAT(waktu, "%H:%i:%s") 
                        ORDER BY TIME(waktu) ASC
                    ) AS presensi,
                    CONCAT("{",
                        GROUP_CONCAT(
                            CONCAT(
                                \'"\', TIME_FORMAT(waktu, "%H:%i:%s"), \'":{\',
                                \'"idmesin":"\', utama.id_mesin_absensi, \'",\',
                                \'"mesin":"\', IFNULL(REPLACE(REPLACE(nm_mesin, \'\\\\\', \'\\\\\\\\\'), \'"\', \'\\\\"\'), \'\'), \'",\',
                                \'"lokasi":"\', IFNULL(REPLACE(REPLACE(lokasi_mesin, \'\\\\\', \'\\\\\\\\\'), \'"\', \'\\\\"\'), \'\'), \'",\',
                                \'"verif":"\', IFNULL(verified, \'\'), \'",\',
                                \'"sts":"\', IFNULL(status, \'\'), \'"\',
                                \'}\'
                            )
                            ORDER BY TIME(waktu) ASC
                        ),
                    "}") AS presensi_data
                FROM ref_data_absensi_tmp utama
                INNER JOIN ref_mesin_absensi rma ON rma.id_mesin_absensi = utama.id_mesin_absensi
                WHERE DATE(waktu) BETWEEN "' . $tgl_awal . '" AND "' . $tgl_akhir . '"
                    AND id_user IN (' . $list_id_user . ')
                GROUP BY id_user, DATE(waktu)
                ORDER BY DATE(waktu) ASC
                ' . $limit . '
            ) utama'
        ));

        if (empty($type)) {
            return $query->get();
        } else {
            return $query;
        }
    }

    public function get_log_mesin_by_histori($params = [], $type)
    {
        ini_set("memory_limit", "800M");
        DB::statement("SET SESSION group_concat_max_len = 15000;");

        $tgl_awal = !empty($params['tanggal'][0]) ? $params['tanggal'][0] : date('Y-m-d');
        $tgl_akhir = !empty($params['tanggal'][1]) ? $params['tanggal'][1] : date('Y-m-d');
        unset($params['tanggal']);

        $limit_data = !empty($params['limit']) ? $params['limit'] : [];
        $limit_data = (new \App\Http\Traits\GlobalFunction)->limit_mysql_manual($limit_data);
        unset($params['limit']);

        // Query dasar - ambil semua data mentah
        $rawData = DB::table('ref_data_absensi_tmp as utama')
            ->leftJoin('ref_mesin_absensi as rma', 'rma.id_mesin_absensi', '=', 'utama.id_mesin_absensi')
            ->leftJoin('ref_karyawan_user as rku', 'rku.id_user', '=', 'utama.id_user')
            ->join('ref_karyawan as karyawan', 'karyawan.id_karyawan', '=', 'rku.id_karyawan')
            ->leftJoin('ref_jabatan as rj', 'rj.id_jabatan', '=', 'karyawan.id_jabatan')
            ->leftJoin('ref_departemen as rd', 'rd.id_departemen', '=', 'karyawan.id_departemen')
            ->leftJoin('ref_ruangan as rr', 'rr.id_ruangan', '=', 'karyawan.id_ruangan')
            ->leftJoin('ref_status_karyawan as rsk', 'rsk.id_status_karyawan', '=', 'karyawan.id_status_karyawan')
            ->leftJoin('ref_karyawan_jadwal as jadwal_rutin', 'jadwal_rutin.id_karyawan', '=', 'karyawan.id_karyawan')
            ->leftJoin('ref_karyawan_jadwal_shift as jadwal_shift', 'jadwal_shift.id_karyawan', '=', 'karyawan.id_karyawan')
            ->whereBetween(DB::raw('DATE(waktu)'), [$tgl_awal, $tgl_akhir])
            ->select([
                'utama.id_user',
                'karyawan.id_karyawan',
                'karyawan.id_ruangan',
                'karyawan.nm_karyawan',
                'karyawan.id_departemen',
                'rd.nm_departemen',
                'karyawan.id_ruangan',
                'rr.nm_ruangan',
                'karyawan.id_status_karyawan',
                'rsk.nm_status_karyawan',
                'karyawan.id_jabatan',
                'rj.nm_jabatan',
                'jadwal_rutin.id_jenis_jadwal',
                'jadwal_shift.id_template_jadwal_shift',
                DB::raw('DATE(waktu) as tgl'),
                DB::raw('TIME_FORMAT(waktu, "%H:%i:%s") as jam'),
                'utama.id_mesin_absensi',
                'rma.nm_mesin',
                'rma.lokasi_mesin',
                'utama.verified',
                'utama.status',
            ])
            ->orderBy(DB::raw('DATE(waktu)'))
            ->orderBy(DB::raw('TIME(waktu)'))
            ->get();

        $groupedByUser = $rawData->groupBy('id_user');

        $results = collect();

        foreach ($groupedByUser as $id_user => $userData) {
            $first = $userData->first();

            $groupedByTgl = $userData->groupBy('tgl');

            $presensi = [];
            $list_data_detail = [];

            foreach ($groupedByTgl as $tgl => $tglData) {
                $jamList = $tglData->pluck('jam')->toArray();
                $presensi[$tgl] = [
                    'presensi' => $jamList
                ];

                // Detail data per tanggal
                $list_data_detail[$tgl] = [
                    'id_mesin'     => $tglData->pluck('id_mesin_absensi')->toArray(),
                    'nm_mesin'     => $tglData->pluck('nm_mesin')->toArray(),
                    'lokasi_mesin' => $tglData->pluck('lokasi_mesin')->toArray(),
                    'verif'        => $tglData->pluck('verified')->toArray(),
                    'sts'          => $tglData->pluck('status')->toArray(),
                ];
            }

            $jadwal_rutin = $first->id_jenis_jadwal;
            $jadwal_shift = $first->id_template_jadwal_shift;

            $results->push((object) [
                'id_user'            => $id_user,
                'id_karyawan'        => $first->id_karyawan,
                'nm_karyawan'        => $first->nm_karyawan,
                'id_departemen'      => $first->id_departemen,
                'nm_departemen'      => $first->nm_departemen,
                'id_ruangan'         => $first->id_ruangan,
                'nm_ruangan'         => $first->nm_ruangan,
                'id_status_karyawan' => $first->id_status_karyawan,
                'nm_status_karyawan' => $first->nm_status_karyawan,
                'id_jabatan'         => $first->id_jabatan,
                'nm_jabatan'         => $first->nm_jabatan,
                'jadwal_rutin'       => $jadwal_rutin,
                'jadwal_shift'       => $jadwal_shift,
                'ada_jadwal'         => ($jadwal_rutin || $jadwal_shift) ? 1 : 0,
                'presensi'           => json_encode($presensi),
                'list_data_detail'   => json_encode($list_data_detail),
            ]);
        }

        // Apply filter/search jika ada params
        if ($params) {
            $results = $this->applyFilters($results, $params);
        }

        // Apply limit
        if (!empty($limit_data)) {
            $offset = isset($limit_data[0]) ? (int)$limit_data[0] : 0;
            $length = isset($limit_data[1]) ? (int)$limit_data[1] : null;
            $results = $results->slice($offset, $length)->values();
        }

        return $results;
        // if (empty($type)) {
        //     return $results;
        // } else {
        //     // Jika butuh query builder, return raw query (tapi tanpa JSON functions)
        //     return $this->get_log_mesin_by_histori_query($tgl_awal, $tgl_akhir, $params);
        // }
    }

    /**
     * Helper: Apply filters di collection
     */
    private function applyFilters($collection, $params)
    {
        foreach ($params as $key => $value) {
            if (empty($value)) continue;

            if ($key === 'where_or') continue; // Skip config

            $collection = $collection->filter(function ($item) use ($key, $value) {
                if (isset($item->$key)) {
                    // Support array value (whereIn style)
                    if (is_array($value)) {
                        return in_array($item->$key, $value);
                    }
                    // Support LIKE style
                    if (strpos($value, '%') !== false) {
                        $pattern = str_replace('%', '.*', preg_quote($value, '/'));
                        return preg_match('/^' . $pattern . '$/i', $item->$key);
                    }
                    return $item->$key == $value;
                }
                return true;
            });
        }

        return $collection->values();
    }

    /**
     * Return query builder untuk type != empty (pagination, dll)
     */
    private function get_log_mesin_by_histori_query($tgl_awal, $tgl_akhir, $params = [])
    {
        $query = DB::table('ref_data_absensi_tmp as utama')
            ->leftJoin('ref_karyawan_user as rku', 'rku.id_user', '=', 'utama.id_user')
            ->join('ref_karyawan as karyawan', 'karyawan.id_karyawan', '=', 'rku.id_karyawan')
            ->leftJoin('ref_jabatan as rj', 'rj.id_jabatan', '=', 'karyawan.id_jabatan')
            ->leftJoin('ref_departemen as rd', 'rd.id_departemen', '=', 'karyawan.id_departemen')
            ->leftJoin('ref_ruangan as rr', 'rr.id_ruangan', '=', 'karyawan.id_ruangan')
            ->leftJoin('ref_status_karyawan as rsk', 'rsk.id_status_karyawan', '=', 'karyawan.id_status_karyawan')
            ->whereBetween(DB::raw('DATE(waktu)'), [$tgl_awal, $tgl_akhir])
            ->select([
                'utama.id_user',
                'karyawan.id_karyawan',
                'karyawan.id_ruangan',
                'karyawan.nm_karyawan',
                'karyawan.id_departemen',
                'karyawan.id_status_karyawan',
                'rd.nm_departemen',
            ])
            ->groupBy('utama.id_user');

        $list_search = [
            'where_or' => ['id_user', 'nm_karyawan'],
        ];

        if ($params) {
            $query = (new \App\Models\MyModel)->set_where($query, $params, $list_search);
        }

        return $query;
    }

    public function get_log_mesin_by_karyawan($params = [], $type)
    {
        ini_set("memory_limit", "800M");
        DB::statement("SET SESSION group_concat_max_len = 15000;");

        $tgl_awal = !empty($params['tanggal'][0]) ? $params['tanggal'][0] : date('Y-m-d');
        $tgl_akhir = !empty($params['tanggal'][1]) ? $params['tanggal'][1] : date('Y-m-d');
        unset($params['tanggal']);

        $limit_data = !empty($params['limit']) ? $params['limit'] : [];
        $limit_data = (new \App\Http\Traits\GlobalFunction)->limit_mysql_manual($limit_data);
        unset($params['limit']);

        // Step 1: Get list id_user berdasarkan filter departemen/ruangan
        $karyawanQuery = DB::table('ref_karyawan as karyawan')
            ->leftJoin('ref_karyawan_user as rku', 'rku.id_karyawan', '=', 'karyawan.id_karyawan')
            ->whereNotNull('rku.id_user');

        if (!empty($params['id_departemen'])) {
            $karyawanQuery->where('karyawan.id_departemen', $params['id_departemen']);
        }

        if (!empty($params['id_ruangan'])) {
            $karyawanQuery->where('karyawan.id_ruangan', $params['id_ruangan']);
        }

        $listIdUser = $karyawanQuery->pluck('rku.id_user')->filter()->toArray();

        // Jika tidak ada user, return empty
        if (empty($listIdUser)) {
            return empty($type) ? collect() : DB::table(DB::raw('(SELECT 1) as empty'))->whereRaw('1=0');
        }

        // Step 2: Get data karyawan lengkap
        $karyawanData = DB::table('ref_karyawan_user as rku')
            ->leftJoin('ref_karyawan as karyawan', 'karyawan.id_karyawan', '=', 'rku.id_karyawan')
            ->leftJoin('ref_jabatan as rj', 'rj.id_jabatan', '=', 'karyawan.id_jabatan')
            ->leftJoin('ref_departemen as rd', 'rd.id_departemen', '=', 'karyawan.id_departemen')
            ->leftJoin('ref_ruangan as rr', 'rr.id_ruangan', '=', 'karyawan.id_ruangan')
            ->leftJoin('ref_status_karyawan as rsk', 'rsk.id_status_karyawan', '=', 'karyawan.id_status_karyawan')
            ->leftJoin('ref_karyawan_jadwal as jadwal_rutin', 'jadwal_rutin.id_karyawan', '=', 'karyawan.id_karyawan')
            ->leftJoin('ref_karyawan_jadwal_shift as jadwal_shift', 'jadwal_shift.id_karyawan', '=', 'karyawan.id_karyawan')
            ->whereIn('rku.id_user', $listIdUser)
            ->select([
                'rku.id_user',
                'karyawan.id_karyawan',
                'karyawan.nm_karyawan',
                'karyawan.id_departemen',
                'rd.nm_departemen',
                'karyawan.id_ruangan',
                'rr.nm_ruangan',
                'karyawan.id_status_karyawan',
                'rsk.nm_status_karyawan',
                'karyawan.id_jabatan',
                'rj.nm_jabatan',
                'jadwal_rutin.id_jenis_jadwal',
                'jadwal_shift.id_template_jadwal_shift',
            ])
            ->get()
            ->keyBy('id_user');

        // Step 3: Get data presensi
        $presensiData = DB::table('ref_data_absensi_tmp as utama')
            ->leftJoin('ref_mesin_absensi as rma', 'rma.id_mesin_absensi', '=', 'utama.id_mesin_absensi')
            ->whereIn('utama.id_user', $listIdUser)
            ->whereBetween(DB::raw('DATE(waktu)'), [$tgl_awal, $tgl_akhir])
            ->select([
                'utama.id_user',
                DB::raw('DATE(waktu) as tgl'),
                DB::raw('TIME_FORMAT(waktu, "%H:%i:%s") as jam'),
                'utama.id_mesin_absensi',
                'rma.nm_mesin',
                'rma.lokasi_mesin',
                'utama.verified',
                'utama.status',
            ])
            ->orderBy(DB::raw('DATE(waktu)'))
            ->orderBy(DB::raw('TIME(waktu)'))
            ->get()
            ->groupBy('id_user');

        $listIdKaryawan = $karyawanData->pluck('id_karyawan')->toArray();

        $kalenderKerja = DB::table('uxui_kalender_kerja as ukk')
            ->leftJoin('ref_jenis_jadwal as rjj', 'rjj.id_jenis_jadwal', '=', 'ukk.id_jenis_jadwal')
            ->whereIn('ukk.id_karyawan', $listIdKaryawan)
            ->whereBetween('ukk.tanggal', [$tgl_awal, $tgl_akhir])
            ->select([
                'ukk.id_karyawan',
                'ukk.tanggal',
                'ukk.id_ruangan',
                'ukk.id_jenis_jadwal',

                'rjj.nm_jenis_jadwal',
                'rjj.masuk_kerja as jam_masuk',
                'rjj.pulang_kerja as jam_pulang',
            ])
            ->orderBy('ukk.tanggal')
            ->get()
            ->groupBy('id_karyawan');

        $results = collect();

        foreach ($karyawanData as $id_user => $karyawan) {
            $presensi = [];
            $list_data_detail = [];
            $jadwal_per_tanggal = [];

            if (isset($kalenderKerja[$karyawan->id_karyawan])) {

                foreach ($kalenderKerja[$karyawan->id_karyawan] as $jadwal) {

                    $tgl = $jadwal->tanggal;

                    $jadwal_per_tanggal[$tgl] = [
                        'id_jenis_jadwal' => $jadwal->id_jenis_jadwal,
                        'nama_jadwal'     => $jadwal->nm_jenis_jadwal,
                        'jam_masuk'       => $jadwal->jam_masuk,
                        'jam_pulang'      => $jadwal->jam_pulang,
                        'id_ruangan'      => $jadwal->id_ruangan,
                    ];
                }
            }
            if (isset($presensiData[$id_user])) {

                $userPresensi = $presensiData[$id_user]->groupBy('tgl');

                foreach ($userPresensi as $tgl => $tglData) {

                    $presensi[$tgl] = [
                        'presensi' => $tglData->pluck('jam')->toArray()
                    ];

                    $list_data_detail[$tgl] = [
                        'id_mesin'     => $tglData->pluck('id_mesin_absensi')->toArray(),
                        'nm_mesin'     => $tglData->pluck('nm_mesin')->toArray(),
                        'lokasi_mesin' => $tglData->pluck('lokasi_mesin')->toArray(),
                        'verif'        => $tglData->pluck('verified')->toArray(),
                        'sts'          => $tglData->pluck('status')->toArray(),
                    ];
                }
            }

            $jadwal_rutin = $karyawan->id_jenis_jadwal;
            $jadwal_shift = $karyawan->id_template_jadwal_shift;

            $results->push((object) [
                'id_user'            => $id_user,
                'id_karyawan'        => $karyawan->id_karyawan,
                'nm_karyawan'        => $karyawan->nm_karyawan,
                'id_departemen'      => $karyawan->id_departemen,
                'nm_departemen'      => $karyawan->nm_departemen,
                'id_ruangan'         => $karyawan->id_ruangan,
                'nm_ruangan'         => $karyawan->nm_ruangan,
                'id_status_karyawan' => $karyawan->id_status_karyawan,
                'nm_status_karyawan' => $karyawan->nm_status_karyawan,
                'id_jabatan'         => $karyawan->id_jabatan,
                'nm_jabatan'         => $karyawan->nm_jabatan,
//                'jadwal_rutin'       => $jadwal_rutin,
//                'jadwal_shift'       => $jadwal_shift,
                'jadwal_per_tanggal' => json_encode($jadwal_per_tanggal),
                'ada_jadwal'         => ($jadwal_rutin || $jadwal_shift) ? 1 : 0,
                'presensi'           => json_encode($presensi),
                'list_data_detail'   => json_encode($list_data_detail),
            ]);
        }

        // Step 5: Apply search filter
        if (!empty($params['search'])) {
            $search = $params['search'];
            $results = $results->filter(function ($item) use ($search) {
                return stripos($item->id_user, $search) !== false
                    || stripos($item->nm_karyawan, $search) !== false;
            })->values();
        }

        // Unset filter yang sudah dipakai
        unset($params['id_departemen'], $params['id_ruangan'], $params['search']);

        // Apply remaining filters
        if ($params) {
            $results = $this->applyCollectionFilters($results, $params);
        }

        // Step 6: Apply limit
        if (!empty($limit_data)) {
            $offset = isset($limit_data[0]) ? (int)$limit_data[0] : 0;
            $length = isset($limit_data[1]) ? (int)$limit_data[1] : null;
            $results = $results->slice($offset, $length)->values();
        }
        return $results;
        // if (empty($type)) {
        //     return $results;
        // } else {
        //     // Return query builder untuk pagination dll
        //     return $this->get_log_mesin_by_karyawan_query($listIdUser, $tgl_awal, $tgl_akhir, $params);
        // }
    }

    /**
     * Helper: Apply filters pada collection
     */
    private function applyCollectionFilters($collection, $params)
    {
        foreach ($params as $key => $value) {
            if (empty($value) || $key === 'where_or') continue;

            $collection = $collection->filter(function ($item) use ($key, $value) {
                if (!isset($item->$key)) return true;

                if (is_array($value)) {
                    return in_array($item->$key, $value);
                }

                return $item->$key == $value;
            });
        }

        return $collection->values();
    }

    /**
     * Return query builder untuk type != empty
     */
    private function get_log_mesin_by_karyawan_query($listIdUser, $tgl_awal, $tgl_akhir, $params = [])
    {
        $query = DB::table('ref_karyawan_user as rku')
            ->leftJoin('ref_karyawan as karyawan', 'karyawan.id_karyawan', '=', 'rku.id_karyawan')
            ->leftJoin('ref_departemen as rd', 'rd.id_departemen', '=', 'karyawan.id_departemen')
            ->leftJoin('ref_ruangan as rr', 'rr.id_ruangan', '=', 'karyawan.id_ruangan')
            ->whereIn('rku.id_user', $listIdUser)
            ->select([
                'rku.id_user',
                'karyawan.id_karyawan',
                'karyawan.id_ruangan',
                'karyawan.id_status_karyawan',
                'karyawan.nm_karyawan',
                'karyawan.id_departemen',
                'rd.nm_departemen',
            ])
            ->groupBy('rku.id_user');

        $list_search = [
            'where_or' => ['id_user', 'nm_karyawan'],
        ];

        if ($params) {
            $query = (new \App\Models\MyModel)->set_where($query, $params, $list_search);
        }

        return $query;
    }

    public function get_data_karyawan_absensi_rutin($params = [], $type = '')
    {
        ini_set("memory_limit", "800M");
        DB::statement("SET SESSION group_concat_max_len = 15000;");

        $tgl_awal = !empty($params['tanggal'][0]) ? $params['tanggal'][0] : date('Y-m-d');
        $tgl_akhir = !empty($params['tanggal'][1]) ? $params['tanggal'][1] : date('Y-m-d');
        unset($params['tanggal']);

        $limit_data = !empty($params['limit']) ? $params['limit'] : [];
        $limit_data = (new \App\Http\Traits\GlobalFunction)->limit_mysql_manual($limit_data);
        unset($params['limit']);

        $karyawanQuery = DB::table('ref_karyawan as karyawan')
            ->join('ref_karyawan_user as rku', 'rku.id_karyawan', '=', 'karyawan.id_karyawan')
            ->join('ref_karyawan_jadwal as jadwal', 'jadwal.id_karyawan', '=', 'karyawan.id_karyawan')
            ->whereNotNull('rku.id_user');

        if (!empty($params['id_departemen'])) {
            $karyawanQuery->where('karyawan.id_departemen', $params['id_departemen']);
        }

        if (!empty($params['id_ruangan'])) {
            $karyawanQuery->where('karyawan.id_ruangan', $params['id_ruangan']);
        }

        $listIdUser = $karyawanQuery->pluck('rku.id_user')->filter()->unique()->toArray();

        if (empty($listIdUser)) {
            return empty($type) ? collect() : DB::table(DB::raw('(SELECT 1) as empty'))->whereRaw('1=0');
        }

        $karyawanData = DB::table('ref_karyawan_user as rku')
            ->join('ref_karyawan as karyawan', 'karyawan.id_karyawan', '=', 'rku.id_karyawan')
            ->join('ref_karyawan_jadwal as jadwal_rutin', 'jadwal_rutin.id_karyawan', '=', 'karyawan.id_karyawan')
            ->leftJoin('ref_jabatan as rj', 'rj.id_jabatan', '=', 'karyawan.id_jabatan')
            ->leftJoin('ref_departemen as rd', 'rd.id_departemen', '=', 'karyawan.id_departemen')
            ->leftJoin('ref_ruangan as rr', 'rr.id_ruangan', '=', 'karyawan.id_ruangan')
            ->leftJoin('ref_status_karyawan as rsk', 'rsk.id_status_karyawan', '=', 'karyawan.id_status_karyawan')
            ->whereIn('rku.id_user', $listIdUser)
            ->select([
                'rku.id_user',
                'karyawan.id_karyawan',
                'karyawan.nm_karyawan',
                'karyawan.id_departemen',
                'rd.nm_departemen',
                'karyawan.id_ruangan',
                'rr.nm_ruangan',
                'karyawan.id_status_karyawan',
                'rsk.nm_status_karyawan',
                'karyawan.id_jabatan',
                'rj.nm_jabatan',
                'jadwal_rutin.id_jenis_jadwal',
            ])
            ->get()
            ->unique('id_user')
            ->keyBy('id_user');

        
        $kalenderKerja = DB::table('uxui_kalender_kerja as kk')
                ->join('ref_jenis_jadwal as rjj', 'rjj.id_jenis_jadwal', '=', 'kk.id_jenis_jadwal')
                ->whereIn('kk.id_karyawan', $karyawanData->pluck('id_karyawan'))
                ->whereBetween('kk.tanggal', [$tgl_awal, $tgl_akhir])
                ->select([
                    'kk.id_karyawan',
                    'kk.tanggal',
                    'kk.id_jenis_jadwal',
                    'rjj.masuk_kerja as jam_masuk',
                    'rjj.pulang_kerja as jam_pulang',
                    'rjj.nm_jenis_jadwal'
                ])
                ->get()
                ->groupBy('id_karyawan');

        $presensiData = DB::table('ref_data_absensi_tmp as utama')
            ->leftJoin('ref_mesin_absensi as rma', 'rma.id_mesin_absensi', '=', 'utama.id_mesin_absensi')
            ->whereIn('utama.id_user', $listIdUser)
            ->whereBetween(DB::raw('DATE(waktu)'), [$tgl_awal, $tgl_akhir])
            ->select([
                'utama.id_user',
                DB::raw('DATE(waktu) as tgl'),
                DB::raw('TIME_FORMAT(waktu, "%H:%i:%s") as jam'),
                'utama.id_mesin_absensi',
                'rma.nm_mesin',
                'rma.lokasi_mesin',
                'utama.verified',
                'utama.status',
            ])
            ->orderBy(DB::raw('DATE(waktu)'))
            ->orderBy(DB::raw('TIME(waktu)'))
            ->get()
            ->groupBy('id_user');

        $results = collect();

        foreach ($karyawanData as $id_user => $karyawan) {
            $presensi = [];
            $list_data_detail = [];

            $jadwalPerTanggal = [];

            if (isset($kalenderKerja[$karyawan->id_karyawan])) {
                foreach ($kalenderKerja[$karyawan->id_karyawan] as $kk) {

                    $data_jadwal = (new \App\Http\Traits\PresensiHitungRutinFunction)
                        ->getWaktuKerjaByTanggal([
                            'id_karyawan' => $kk->id_karyawan,
                            'tanggal'     => $kk->tanggal
                        ])
                        ->first();

                    $jadwalPerTanggal[$kk->tanggal] = $data_jadwal;
                }
            }

            $userPresensi = collect();

            if (isset($presensiData[$id_user])) {
                $userPresensi = $presensiData[$id_user]->groupBy('tgl');
            }


//                foreach ($userPresensi as $tgl => $tglData) {
//                    $presensi[$tgl] = [
//                        'presensi' => $tglData->pluck('jam')->toArray(),
//                        'jadwal'   => $jadwalPerTanggal[$tgl] ?? null
//                    ];
//                    $list_data_detail[$tgl] = [
//                        'id_mesin'     => $tglData->pluck('id_mesin_absensi')->toArray(),
//                        'nm_mesin'     => $tglData->pluck('nm_mesin')->toArray(),
//                        'lokasi_mesin' => $tglData->pluck('lokasi_mesin')->toArray(),
//                        'verif'        => $tglData->pluck('verified')->toArray(),
//                        'sts'          => $tglData->pluck('status')->toArray(),
//                    ];
//                }

                foreach ($jadwalPerTanggal as $tgl => $jadwalTanggal) {

                    $presensiJam = [];
                    $detailData  = [];

                    if (isset($presensiData[$id_user])) {

                        $userPresensi = $presensiData[$id_user]->groupBy('tgl');

                        if (isset($userPresensi[$tgl])) {
                            $tglData = $userPresensi[$tgl];

                            $presensiJam = $tglData->pluck('jam')->toArray();

                            $detailData = [
                                'id_mesin'     => $tglData->pluck('id_mesin_absensi')->toArray(),
                                'nm_mesin'     => $tglData->pluck('nm_mesin')->toArray(),
                                'lokasi_mesin' => $tglData->pluck('lokasi_mesin')->toArray(),
                                'verif'        => $tglData->pluck('verified')->toArray(),
                                'sts'          => $tglData->pluck('status')->toArray(),
                            ];
                        }
                    }

                    $presensi[$tgl] = [
                        'presensi' => $presensiJam,
                        'jadwal'   => $jadwalTanggal
                    ];

                    $list_data_detail[$tgl] = $detailData;
                }

            $results->push((object) [
                'id_user'            => $id_user,
                'id_karyawan'        => $karyawan->id_karyawan,
                'nm_karyawan'        => $karyawan->nm_karyawan,
                'id_departemen'      => $karyawan->id_departemen,
                'nm_departemen'      => $karyawan->nm_departemen,
                'id_ruangan'         => $karyawan->id_ruangan,
                'nm_ruangan'         => $karyawan->nm_ruangan,
                'id_status_karyawan' => $karyawan->id_status_karyawan,
                'nm_status_karyawan' => $karyawan->nm_status_karyawan,
                'id_jabatan'         => $karyawan->id_jabatan,
                'nm_jabatan'         => $karyawan->nm_jabatan,
                'jadwal_rutin'       => $karyawan->id_jenis_jadwal,
                'presensi'           => json_encode($presensi),
                'list_data_detail'   => json_encode($list_data_detail),
            ]);
        }

        if (!empty($params['search'])) {
            $search = $params['search'];
            $results = $results->filter(function ($item) use ($search) {
                return stripos((string)$item->id_user, $search) !== false
                    || stripos((string)$item->nm_karyawan, $search) !== false;
            })->values();
        }

        unset($params['id_departemen'], $params['id_ruangan'], $params['search']);

        if ($params) {
            $results = $this->applyCollectionFilters($results, $params);
        }

        $results = $results->sort(function ($a, $b) {
            return [$a->id_departemen, $a->id_ruangan, $a->id_status_karyawan, $a->nm_karyawan]
                <=> [$b->id_departemen, $b->id_ruangan, $b->id_status_karyawan, $b->nm_karyawan];
        })->values();

        if (!empty($limit_data)) {
            $offset = isset($limit_data[0]) ? (int)$limit_data[0] : 0;
            $length = isset($limit_data[1]) ? (int)$limit_data[1] : null;
            $results = $results->slice($offset, $length)->values();
        }

        return $results;
        
        // if (empty($type)) {
        //     return $results;
        // } else {
        //     return $this->get_data_karyawan_absensi_rutin_query($listIdUser, $params);
        // }
    }

    /**
     * Return query builder untuk type != empty
     */
    private function get_data_karyawan_absensi_rutin_query($listIdUser, $params = [])
    {
        $query = DB::table('ref_karyawan_user as rku')
            ->join('ref_karyawan as karyawan', 'karyawan.id_karyawan', '=', 'rku.id_karyawan')
            ->join('ref_karyawan_jadwal as jadwal_rutin', 'jadwal_rutin.id_karyawan', '=', 'karyawan.id_karyawan')
            ->leftJoin('ref_departemen as rd', 'rd.id_departemen', '=', 'karyawan.id_departemen')
            ->leftJoin('ref_ruangan as rr', 'rr.id_ruangan', '=', 'karyawan.id_ruangan')
            ->whereIn('rku.id_user', $listIdUser)
            ->select([
                'rku.id_user',
                'karyawan.id_karyawan',
                'karyawan.id_ruangan',
                'karyawan.id_status_karyawan',
                'karyawan.nm_karyawan',
                'karyawan.id_departemen',
                'rd.nm_departemen',
            ])
            ->groupBy('rku.id_user')
            ->orderBy('id_departemen','ASC')
            ->orderBy('id_ruangan','ASC')
            ->orderBy('id_status_karyawan','ASC')
            ->orderBy('nm_karyawan','ASC');

        $list_search = [
            'where_or' => ['id_user', 'nm_karyawan'],
        ];

        if ($params) {
            $query = (new \App\Models\MyModel)->set_where($query, $params, $list_search);
        }

        return $query;
    }

    public function get_data_karyawan_absensi_shift($params = [], $type = '')
    {
        ini_set("memory_limit", "800M");
        DB::statement("SET SESSION group_concat_max_len = 15000;");

        // Tanggal dengan buffer -1 dan +1 hari (untuk shift yang melewati tengah malam)
        $tgl_awal_tmp = !empty($params['tanggal'][0]) ? $params['tanggal'][0] : date('Y-m-d');
        $tgl_akhir_tmp = !empty($params['tanggal'][1]) ? $params['tanggal'][1] : date('Y-m-d');

        $tgl_awal = (new \DateTime($tgl_awal_tmp))->modify('-1 day')->format('Y-m-d');
        $tgl_akhir = (new \DateTime($tgl_akhir_tmp))->modify('+1 day')->format('Y-m-d');

        unset($params['tanggal']);

        $limit_data = !empty($params['limit']) ? $params['limit'] : [];
        $limit_data = (new \App\Http\Traits\GlobalFunction)->limit_mysql_manual($limit_data);
        unset($params['limit']);

        // Step 1: Get list id_user yang punya jadwal SHIFT
        $karyawanQuery = DB::table('ref_karyawan as karyawan')
            ->join('ref_karyawan_user as rku', 'rku.id_karyawan', '=', 'karyawan.id_karyawan')
            ->join('ref_karyawan_jadwal_shift as jadwal', 'jadwal.id_karyawan', '=', 'karyawan.id_karyawan')
            ->whereNotNull('rku.id_user');

        if (!empty($params['id_departemen'])) {
            $karyawanQuery->where('karyawan.id_departemen', $params['id_departemen']);
        }

        if (!empty($params['id_ruangan'])) {
            $karyawanQuery->where('karyawan.id_ruangan', $params['id_ruangan']);
        }

        $listIdUser = $karyawanQuery->pluck('rku.id_user')->filter()->unique()->toArray();
        
        // Jika tidak ada user, return empty
        if (empty($listIdUser)) {
            return empty($type) ? collect() : DB::table(DB::raw('(SELECT 1) as empty'))->whereRaw('1=0');
        }

        // Step 2: Get data karyawan lengkap (yang punya jadwal shift)
        $karyawanData = DB::table('ref_karyawan_user as rku')
            ->join('ref_karyawan as karyawan', 'karyawan.id_karyawan', '=', 'rku.id_karyawan')
            ->join('ref_karyawan_jadwal_shift as jadwal_shift', 'jadwal_shift.id_karyawan', '=', 'karyawan.id_karyawan')
            ->leftJoin('ref_template_jadwal_shift as rtjs', 'rtjs.id_template_jadwal_shift', '=', 'jadwal_shift.id_template_jadwal_shift')
            ->leftJoin('ref_jabatan as rj', 'rj.id_jabatan', '=', 'karyawan.id_jabatan')
            ->leftJoin('ref_departemen as rd', 'rd.id_departemen', '=', 'karyawan.id_departemen')
            ->leftJoin('ref_ruangan as rr', 'rr.id_ruangan', '=', 'karyawan.id_ruangan')
            ->leftJoin('ref_status_karyawan as rsk', 'rsk.id_status_karyawan', '=', 'karyawan.id_status_karyawan')
            ->whereIn('rku.id_user', $listIdUser)
            ->select([
                'rku.id_user',
                'karyawan.id_karyawan',
                'karyawan.nm_karyawan',
                'karyawan.id_departemen',
                'rd.nm_departemen',
                'karyawan.id_ruangan',
                'rr.nm_ruangan',
                'karyawan.id_status_karyawan',
                'rsk.nm_status_karyawan',
                'karyawan.id_jabatan',
                'rj.nm_jabatan',
                'jadwal_shift.id_template_jadwal_shift',
                'rtjs.nm_shift',
            ])
            ->get()
            ->unique('id_user')
            ->keyBy('id_user');

        // Step 3: Get data presensi (dengan range tanggal yang diperluas)
        $presensiData = DB::table('ref_data_absensi_tmp as utama')
            ->leftJoin('ref_mesin_absensi as rma', 'rma.id_mesin_absensi', '=', 'utama.id_mesin_absensi')
            ->whereIn('utama.id_user', $listIdUser)
            ->whereBetween(DB::raw('DATE(waktu)'), [$tgl_awal, $tgl_akhir])
            ->select([
                'utama.id_user',
                DB::raw('DATE(waktu) as tgl'),
                DB::raw('TIME_FORMAT(waktu, "%H:%i:%s") as jam'),
                'utama.id_mesin_absensi',
                'rma.nm_mesin',
                'rma.lokasi_mesin',
                'utama.verified',
                'utama.status',
            ])
            ->orderBy(DB::raw('DATE(waktu)'))
            ->orderBy(DB::raw('TIME(waktu)'))
            ->get()
            ->groupBy('id_user');

        // Step 4: Build hasil akhir
        $results = collect();

        foreach ($karyawanData as $id_user => $karyawan) {
            $presensi = [];
            $list_data_detail = [];

            // Jika ada data presensi untuk user ini
            if (isset($presensiData[$id_user])) {
                $userPresensi = $presensiData[$id_user]->groupBy('tgl');

                foreach ($userPresensi as $tgl => $tglData) {
                    // Presensi per tanggal
                    $presensi[$tgl] = [
                        'presensi' => $tglData->pluck('jam')->toArray()
                    ];

                    // Detail data per tanggal
                    $list_data_detail[$tgl] = [
                        'id_mesin'     => $tglData->pluck('id_mesin_absensi')->toArray(),
                        'nm_mesin'     => $tglData->pluck('nm_mesin')->toArray(),
                        'lokasi_mesin' => $tglData->pluck('lokasi_mesin')->toArray(),
                        'verif'        => $tglData->pluck('verified')->toArray(),
                        'sts'          => $tglData->pluck('status')->toArray(),
                    ];
                }
            }

            $results->push((object) [
                'id_user'                 => $id_user,
                'id_karyawan'             => $karyawan->id_karyawan,
                'nm_karyawan'             => $karyawan->nm_karyawan,
                'id_departemen'           => $karyawan->id_departemen,
                'nm_departemen'           => $karyawan->nm_departemen,
                'id_ruangan'              => $karyawan->id_ruangan,
                'nm_ruangan'              => $karyawan->nm_ruangan,
                'id_status_karyawan'      => $karyawan->id_status_karyawan,
                'nm_status_karyawan'      => $karyawan->nm_status_karyawan,
                'id_jabatan'              => $karyawan->id_jabatan,
                'nm_jabatan'              => $karyawan->nm_jabatan,
                'id_template_jadwal_shift'=> $karyawan->id_template_jadwal_shift,
                'nm_shift'                => $karyawan->nm_shift,
                'presensi'                => json_encode($presensi),
                'list_data_detail'        => json_encode($list_data_detail),
            ]);
        }
        // Step 5: Apply search filter
        if (!empty($params['search'])) {
            $search = $params['search'];
            $results = $results->filter(function ($item) use ($search) {
                return stripos((string)$item->id_user, $search) !== false
                    || stripos((string)$item->nm_karyawan, $search) !== false;
            })->values();
        }

        // Unset filter yang sudah dipakai
        unset($params['id_departemen'], $params['id_ruangan'], $params['search']);

        // Apply remaining filters
        if ($params) {
            $results = $this->applyCollectionFilters($results, $params);
        }

        // Step 6: Apply limit
        if (!empty($limit_data)) {
            $offset = isset($limit_data[0]) ? (int)$limit_data[0] : 0;
            $length = isset($limit_data[1]) ? (int)$limit_data[1] : null;
            $results = $results->slice($offset, $length)->values();
        }


        return $results;
        // if (empty($type)) {
        //     return $results;
        // } else {
        //     return $this->get_data_karyawan_absensi_shift_query($listIdUser, $params);
        // }
    }

    /**
     * Return query builder untuk type != empty
     */
    private function get_data_karyawan_absensi_shift_query($listIdUser, $params = [])
    {
        $query = DB::table('ref_karyawan_user as rku')
            ->join('ref_karyawan as karyawan', 'karyawan.id_karyawan', '=', 'rku.id_karyawan')
            ->join('ref_karyawan_jadwal_shift as jadwal_shift', 'jadwal_shift.id_karyawan', '=', 'karyawan.id_karyawan')
            ->leftJoin('ref_template_jadwal_shift as rtjs', 'rtjs.id_template_jadwal_shift', '=', 'jadwal_shift.id_template_jadwal_shift')
            ->leftJoin('ref_departemen as rd', 'rd.id_departemen', '=', 'karyawan.id_departemen')
            ->leftJoin('ref_ruangan as rr', 'rr.id_ruangan', '=', 'karyawan.id_ruangan')
            ->whereIn('rku.id_user', $listIdUser)
            ->select([
                'rku.id_user',
                'karyawan.id_karyawan',
                'karyawan.id_ruangan',
                'karyawan.id_status_karyawan',
                'karyawan.nm_karyawan',
                'karyawan.id_departemen',
                'rd.nm_departemen',
            ])
            ->groupBy('rku.id_user');

        $list_search = [
            'where_or' => ['id_user', 'nm_karyawan'],
        ];

        if ($params) {
            $query = (new \App\Models\MyModel)->set_where($query, $params, $list_search);
        }

        return $query;
    }

    public function get_type_verified($type=null){
        $list_data=[
            1=>'Finger',
            3=>'Password',
        ];

        if(!isset($type)){
            return $list_data;
        }else{
            return !empty($list_data[$type]) ? $list_data[$type] : '';
        }
    }

    public function save_update_rekap($data_save)
    {
        ini_set("memory_limit","800M");
        set_time_limit(0);
        
        DB::beginTransaction();
        $pesan = [];
        try {
            $is_save = 0;

            $model = (new \App\Models\DataAbsensiKaryawanRekap);

            // if(!empty($data_save[0])){
            //     $get_tgl_presensi=$data_save[0]['tgl_presensi'];
            //     if(!empty($get_tgl_presensi)){
            //         $tb_presensi=new \DateTime($get_tgl_presensi);
            //         $tahun_presensi=$tb_presensi->format('Y');
            //         $bulan_presensi=(int)$tb_presensi->format('m');

            //         $model::whereRaw('YEAR(tgl_presensi)', $tahun_presensi)->whereRaw('MONTH(tgl_presensi)', $bulan_presensi)->delete();
            //     }
                
            // }
            
            if($model::insertOrIgnore($data_save)){
                $is_save = 1;
            }
            
            $check_tidak_update=0;
            if($is_save<=0){
                foreach($data_save as $value){
                    $model = (new \App\Models\DataAbsensiKaryawanRekap)->where([
                        'id_user'=>$value['id_user'],
                        'id_karyawan'=>$value['id_karyawan'],
                        'tgl_presensi'=>$value['tgl_presensi'],
                        'id_jenis_jadwal'=>$value['id_jenis_jadwal'],
                    ])->first();
                    
                    $hasil_check = array_diff_assoc($model->getOriginal(), $value);
                    if(!empty($hasil_check)){
                        $model->set_model_with_data($value);
                        if ($model->save()) {
                            $is_save++;
                        }
                    }else{
                        $check_tidak_update++;
                    }
                }
            }

            if ($is_save) {
                DB::commit();
                $pesan = ['success',2];
            } else {
                DB::rollBack();
                if(!empty($check_tidak_update)){
                    $pesan = ['success',2];    
                }else{
                    $pesan = ['error',3];
                }
            }
        } catch (\Illuminate\Database\QueryException $e) {
            DB::rollBack();
            if ($e->errorInfo[1] == '1062') {
            }
            $pesan = ['error',3];
        } catch (\Throwable $e) {
            DB::rollBack();
            $pesan = ['error',3];
        }

        return $pesan;
    }

    public function get_data_hari_libur($params=[]){

        $tanggal=!empty($params['tanggal']) ? $params['tanggal'] : [];
        $tanggal[0]=!empty($tanggal[0]) ? $tanggal[0] : date('Y-m-d');
        $tanggal[1]=!empty($tanggal[1]) ? $tanggal[1] : date('Y-m-d');

        $paramater_where=[
            'search' => !empty($params['search']) ? $params['search'] : '',
            'where_between'=>['tanggal'=>[$tanggal[0],$tanggal[1] ]],
        ];

        $data_tmp_tmp=( new \App\Models\RefHariLiburUmum() );
        $list_data_array=$data_tmp_tmp->set_where($data_tmp_tmp,$paramater_where)->get();
        $list_data=[];
        if($list_data_array){
            foreach($list_data_array as $value){
                $tanggal_awal=$value->tanggal;
                $jumlah=$value->jumlah;
                $jumlah=$jumlah-1;
                if($jumlah<=0){
                    $jumlah=0;
                }
                $get_next=$tanggal_awal." +".$jumlah." days";
                $tanggal_akhir=date('Y-m-d', strtotime($get_next));

                $tanggal_awal_str = new \DateTime($tanggal_awal);
                $tanggal_akhir_str = new \DateTime($tanggal_akhir);


                for ($tanggal = $tanggal_awal_str; $tanggal <= $tanggal_akhir_str; $tanggal->modify("+1 day")) {
                    $this_tanggal=$tanggal->format("Y-m-d");
                    $list_data[$this_tanggal]=(object)[
                        'asal_tanggal'=>$tanggal_awal,
                        'uraian'=>$value->uraian
                    ];
                }
            }
        }

        return $list_data;

    }

    public function hitung_tgl_rekursif($params){
        $tgl_dasar=$params['tgl_dasar'];
        $tgl_awal=$params['tgl_awal'];
        $jml_range=$params['jml_range'];
        $value_data=$params['value_data'];

        $tgl_akhir=!empty($params['tgl_akhir']) ? $params['tgl_akhir'] : '';
        
        $callback=!empty($params['callback']) ? $params['callback'] : [];

        $return=[];
        
        $tgl_next_tmp = new \DateTime($tgl_awal);
        $tgl_next_tmp->modify('+'.$jml_range.' day');
        $tgl_next_tahun=$tgl_next_tmp->format('Y');
        $tgl_next_bulan=$tgl_next_tmp->format('m');
        $tgl_next=$tgl_next_tmp->format('Y-m-d');
        
        if($tgl_dasar==$tgl_awal){
            $tgl_dasar_tmp = new \DateTime($tgl_dasar);
            $callback[$tgl_dasar_tmp->format('Y-m-d')]=$value_data;
        }

        if(!empty($callback)){
            $return=$callback;
        }
        
        $tgl_tmp = new \DateTime($tgl_dasar);
        $tgl_first=(int)$tgl_tmp->format('d');
        $tahun_first=(int)$tgl_tmp->format('Y');
        $tgl_check=(int)$tgl_next_tmp->format('d');
        $tahun_check=(int)$tgl_next_tmp->format('Y');

        $status_lopping=0;
        if( $tgl_first!=$tgl_check){
            $status_lopping=1;
        }else{
            if( $tahun_first==$tahun_check){
                $status_lopping=1;
            }
        }

        if(!empty($tgl_akhir)){
            $status_lopping=0;

            $strtotime_start=strtotime($tgl_next);
            $strtotime_end=strtotime($tgl_akhir);

            if($strtotime_start<=$strtotime_end){
                $status_lopping=1;
            }
        }

        if( ($status_lopping)){
            $callback[$tgl_next]=$value_data;
        }

        if(!empty($callback)){
            ksort($callback);
        }

        if( ($status_lopping)){
            $paramater=[
                'tgl_dasar'=>$tgl_dasar,
                'tgl_awal'=>$tgl_next,
                'jml_range'=>$jml_range,
                'value_data'=>$value_data,
                'callback'=>$callback,
            ];

            if(!empty($tgl_akhir)){
                $paramater['tgl_akhir']=$tgl_akhir;
            }
            
            return $this->hitung_tgl_rekursif($paramater);
        }else{
            $return=$callback;
        }

        return $return;
    }

    public function hitung_tgl_rekursif_bulan($params){
        $tgl_dasar=$params['tgl_dasar'];
        $tgl_awal=$params['tgl_awal'];
        $jml_range=$params['jml_range'];
        $value_data=$params['value_data'];

        $tgl_akhir=!empty($params['tgl_akhir']) ? $params['tgl_akhir'] : '';
        
        $callback=!empty($params['callback']) ? $params['callback'] : [];

        $return=[];
        
        $tgl_next_tmp = new \DateTime($tgl_awal);
        $tgl_next_tmp->modify('+'.$jml_range.' day');
        $tgl_next_tahun=$tgl_next_tmp->format('Y');
        $tgl_next_bulan=$tgl_next_tmp->format('m');
        $tgl_next=$tgl_next_tmp->format('Y-m-d');
        
        if($tgl_dasar==$tgl_awal){
            $tgl_dasar_tmp = new \DateTime($tgl_dasar);
            $callback[$tgl_dasar_tmp->format('Y')][(int)$tgl_dasar_tmp->format('m')][$tgl_dasar_tmp->format('Y-m-d')]=$value_data;
        }

        if(!empty($callback)){
            $return=$callback;
        }

        $tgl_tmp = new \DateTime($tgl_dasar);
        $tgl_first=(int)$tgl_tmp->format('d');
        $tahun_first=(int)$tgl_tmp->format('Y');
        $tgl_check=(int)$tgl_next_tmp->format('d');
        $tahun_check=(int)$tgl_next_tmp->format('Y');

        $status_lopping=0;
        if( $tgl_first!=$tgl_check){
            $status_lopping=1;
        }else{
            if( $tahun_first==$tahun_check){
                $status_lopping=1;
            }
        }

        if(!empty($tgl_akhir)){
            $status_lopping=0;

            $strtotime_start=strtotime($tgl_next);
            $strtotime_end=strtotime($tgl_akhir);

            if($strtotime_start<=$strtotime_end){
                $status_lopping=1;
            }
        }

        if( ($status_lopping)){
            $callback[$tgl_next_tahun][(int)$tgl_next_bulan][$tgl_next]=$value_data;
        }

        if(!empty($callback)){
            ksort($callback);
        }

        if(!empty($callback[$tgl_next_tahun])){
            ksort($callback[$tgl_next_tahun]);
        }

        if(!empty($callback[$tgl_next_tahun][(int)$tgl_next_bulan])){
            ksort($callback[$tgl_next_tahun][(int)$tgl_next_bulan]);
        }

        if( ($status_lopping)){
            $paramater=[
                'tgl_dasar'=>$tgl_dasar,
                'tgl_awal'=>$tgl_next,
                'jml_range'=>$jml_range,
                'value_data'=>$value_data,
                'callback'=>$callback,
            ];

            if(!empty($tgl_akhir)){
                $paramater['tgl_akhir']=$tgl_akhir;
            }

            return $this->hitung_tgl_rekursif_bulan($paramater);
        }else{
            $return=$callback;
        }

        return $return;
    }

    function setListShiftFirst($params=[]){

        $type_fungsi=!empty($params['type_fungsi']) ? $params['type_fungsi'] : 'bulan';
        unset($params['type_fungsi']);

        $query=DB::table(DB::raw(
            '(
                select
                    utama.id_template_jadwal_shift,
                    utama.nm_shift,
                    rtjsd.id_template_jadwal_shift_detail,
                    tgl_mulai,
                    rtjsw.id_jenis_jadwal,
                    nm_jenis_jadwal,masuk_kerja,pulang_kerja,pulang_kerja_next_day,bg_color,
                    type as type_jadwal,
                    tgl
                from (
                    select * from ref_template_jadwal_shift '.(!empty($params['id_template_jadwal_shift']) ? "where id_template_jadwal_shift=".$params['id_template_jadwal_shift'] : '').'
                ) utama
                inner join ref_template_jadwal_shift_detail rtjsd on rtjsd.id_template_jadwal_shift = utama.id_template_jadwal_shift
                inner join ref_template_jadwal_shift_waktu rtjsw on rtjsw.id_template_jadwal_shift_detail = rtjsd.id_template_jadwal_shift_detail
                left join ref_jenis_jadwal rjj on rjj.id_jenis_jadwal = rtjsw.id_jenis_jadwal
                
            ) utama'
        ));

        $hasil_query=$query->get();

        $data_jadwal_tmp=[];
        $data_hari_shift=[];
        $data_hari_shift_tmp=[];
        $tgl_mulai_perhitungan='';
        if($hasil_query->count()){
            foreach($hasil_query as $key => $value){
                if(!empty($value->tgl)){
                    $tgl_mulai_perhitungan=$value->tgl_mulai;
                    
                    if($value->type_jadwal==2){
                        $value=(array)$value;
                        $value['nm_jenis_jadwal']='Libur';
                        $value['bg_color']='#e1dede';
                        $value=(object)$value;
                    }

                    $data_jadwal_tmp[$value->id_jenis_jadwal]=$value;
                    $data_hari=explode(',',$value->tgl);
                    foreach($data_hari as $key_hari => $value_hari){
                        if(empty($data_hari_shift[$value->id_jenis_jadwal])){
                            $data_hari_shift[$value->id_jenis_jadwal]=[];
                        }
                        array_push($data_hari_shift[$value->id_jenis_jadwal], $value_hari);
                        $data_hari_shift_tmp[$value_hari][]=$value->id_jenis_jadwal;
                        
                    }
                }
            }
        }else{
            return [];
        }
        ksort($data_jadwal_tmp);
        ksort($data_hari_shift_tmp);

        $return=[];
        
        if(!empty($data_hari_shift_tmp) && !empty($data_jadwal_tmp) ){
            $tgl_mulai_perhitungan = new \DateTime($tgl_mulai_perhitungan);
            $tgl_mulai_perhitungan=(object)[
                'tahun'=>$tgl_mulai_perhitungan->format('Y'),
                'bulan'=>$tgl_mulai_perhitungan->format('m'),
                'tahun_bulan'=>$tgl_mulai_perhitungan->format('Y-m'),
                'tgl'=>$tgl_mulai_perhitungan->format('Y-m-d'),
            ];

            $end_tahun_bulan_mksimal=$tgl_mulai_perhitungan->tahun.'-12';
            $get_tgl_per_bulan=(new \App\Http\Traits\AbsensiFunction)->get_tgl_per_bulan($end_tahun_bulan_mksimal);
            $end_tanggal=!empty($get_tgl_per_bulan->tgl_start_end[1]) ? $get_tgl_per_bulan->tgl_start_end[1] : '';

            foreach($data_hari_shift_tmp as $key => $value){
                $tgl_awal=$tgl_mulai_perhitungan->tahun_bulan.'-'.$key;
                
                $value_data=[];
                if($value){
                    foreach($value as $nilai){
                        if(!empty($data_jadwal_tmp[$nilai])){
                            $detail_data=(array)$data_jadwal_tmp[$nilai];
                            unset($detail_data['tgl']);
                            $detail_data=(object)$detail_data;
                            $value_data[]=$detail_data;
                        }
                        
                    }
                }

                $paramater=[
                    'tgl_dasar'=>$tgl_awal,
                    'tgl_awal'=>$tgl_awal,
                    'jml_range'=>count($data_hari_shift_tmp),
                    'value_data'=>$value_data,
                    // 'id_jadwal'=>$value,
                    'tgl_akhir'=>$end_tanggal
                ];

                if(!empty($return)){
                    $paramater['callback']=$return;
                }

                if($type_fungsi=='tanggal'){
                    $return=$this->hitung_tgl_rekursif($paramater);
                }else if($type_fungsi=='bulan'){
                    $return=$this->hitung_tgl_rekursif_bulan($paramater);
                }
            }
        }

        return $return;
    }

    function setListShiftFirstDatabase($params=[]){
        $id_template_jadwal_shift=!empty($params['id_template_jadwal_shift']) ? $params['id_template_jadwal_shift'] : 0;
        if(!empty($id_template_jadwal_shift)){
            $hasil=$this->setListShiftFirst($params);
            
            if(empty($hasil)){
                $get_data_tw = (new \App\Models\RefTemplateJadwalShiftDetail)->where([
                    'id_template_jadwal_shift'=> $id_template_jadwal_shift,
                ])->select('id_template_jadwal_shift_detail')->first();
                if($get_data_tw->id_template_jadwal_shift_detail){
                    $check_data = (new \App\Models\RefTemplateJadwalShiftWaktu)->where([
                        'id_template_jadwal_shift_detail'=> $get_data_tw->id_template_jadwal_shift_detail,
                    ])->count('id_template_jadwal_shift_detail');
                    if(empty($check_data)){
                        $get_data = (new \App\Models\RefTemplateJadwalShiftLanjutan)->where([
                            'id_template_jadwal_shift'=> $id_template_jadwal_shift
                        ])->delete();
                    }
                }
            }

            DB::beginTransaction();

            $message_default = [
                'success' => !empty($kode) ? 'Data berhasil diubah' : 'Data berhasil disimpan',
                'error' => !empty($kode) ? 'Data tidak berhasil diubah' : 'Data berhasil disimpan'
            ];

            try {
                $data_insert=[];
                foreach($hasil as $k_hasil => $v_hasil){
                    if(!empty($v_hasil)){
                        $get_data = (new \App\Models\RefTemplateJadwalShiftLanjutan)->where([
                            'id_template_jadwal_shift'=> $id_template_jadwal_shift,
                            'tahun'=>$k_hasil
                        ])->count('id_template_jadwal_shift');
                        if(!empty($get_data)){
                            $get_data = (new \App\Models\RefTemplateJadwalShiftLanjutan)->where([
                                'id_template_jadwal_shift'=> $id_template_jadwal_shift,
                                'tahun'=>$k_hasil
                            ])->delete();
                        }
                        foreach($v_hasil as $bulan => $nilai){
                            if($nilai){
                                $nilai_json=json_encode($nilai);
                                $data_insert[]=[
                                    'id_template_jadwal_shift'=>$id_template_jadwal_shift,
                                    'tahun'=>$k_hasil,
                                    'bulan'=>$bulan,
                                    'data'=>$nilai_json
                                ];
                            }
                        }
                        
                    }
                }
                
                $is_save=0;
                if($data_insert){
                    $model=(new \App\Models\RefTemplateJadwalShiftLanjutan);
                    if($model->insert($data_insert)){
                        $is_save=1;
                    }
                }

                if ($is_save) {
                    DB::commit();
                    $pesan = ['success', $message_default['success'], 2];
                } else {
                    DB::rollBack();
                    $pesan = ['error', $message_default['error'], 3];
                }

            } catch (\Illuminate\Database\QueryException $e) {
                DB::rollBack();
                if ($e->errorInfo[1] == '1062') {
                }
                $pesan = ['error', $message_default['error'], 3];
            } catch (\Throwable $e) {
                DB::rollBack();
                $pesan = ['error', $message_default['error'], 3];
            }
            return $pesan;
        }
        return '';
    }

    function setListShift($params=[]){
        
        $get_data=(new \App\Models\RefTemplateJadwalShiftLanjutan)->where([
            'id_template_jadwal_shift'=>$params['id_template_jadwal_shift'],
            'tahun'=>$params['tahun'],
            'bulan'=>$params['bulan'],
        ])->first();
        if($get_data){
            $chec_rumus_mulai = (new \App\Models\RefTemplateJadwalShiftDetail)->where([
                'id_template_jadwal_shift'=> $params['id_template_jadwal_shift'],
            ])->whereYear('tgl_mulai', '=', $params['tahun'])->whereMonth('tgl_mulai', '=', $params['bulan'])
            ->count('id_template_jadwal_shift');
            
            if(empty($chec_rumus_mulai)){
                $jml=0;
                $data_tmp=json_decode($get_data->data,true);
                if(count($data_tmp)<28){
                    $get_data=0;
                }
            }

            
        }
        
        if($get_data){
            return (object)$get_data->getAttributes();
        }else{
            $get_data_tahun=[];
            $get_data_tahun_tmp=(new \App\Models\RefTemplateJadwalShiftLanjutan)->where([
                'id_template_jadwal_shift'=>$params['id_template_jadwal_shift']
            ])->select(DB::raw('max(tahun) as tahun'))->first();

            $get_data_bulan_tmp=(new \App\Models\RefTemplateJadwalShiftLanjutan)->where([
                'id_template_jadwal_shift'=>$params['id_template_jadwal_shift'],
                'tahun'=>$get_data_tahun_tmp->tahun
            ])->select(DB::raw('max(bulan) as bulan'))->first();

            $get_data_tahun=(object)[
                'tahun'=>$get_data_tahun_tmp->tahun,
                'bulan'=>$get_data_bulan_tmp->bulan,
            ];

            if($get_data_tahun){
                $get_data_old=(new \App\Models\RefTemplateJadwalShiftLanjutan)->where([
                    'id_template_jadwal_shift'=>$params['id_template_jadwal_shift'],
                    'tahun'=>$get_data_tahun->tahun,
                    'bulan'=>$get_data_tahun->bulan,
                ])->first();

                if(!empty($get_data_old)){
                
                    $tahun_old=$get_data_old->tahun;
                    $bulan_old=$get_data_old->bulan;
                    
                    $data=!empty($get_data_old->data) ? json_decode($get_data_old->data) : '';

                    $get_data_tw = (new \App\Models\RefTemplateJadwalShiftDetail)->where([
                        'id_template_jadwal_shift'=> $params['id_template_jadwal_shift'],
                    ])->select('id_template_jadwal_shift_detail')->first();

                    $get_rumus=[];
                    if($get_data_tw->id_template_jadwal_shift_detail){
                        $get_data_td = (new \App\Models\RefTemplateJadwalShiftWaktu)->where([
                            'id_template_jadwal_shift_detail'=> $get_data_tw->id_template_jadwal_shift_detail,
                        ])->get();
                        if($get_data_td){
                            foreach($get_data_td as $value){
                                $exp_tgl=explode(',',$value->tgl);
                                foreach($exp_tgl as $tgl){
                                    $get_rumus[$tgl]=$value->id_jenis_jadwal;
                                }
                            }
                        }
                    }
                    ksort($get_rumus);
                    $get_rumus=array_values($get_rumus);
                    
                    $data_tmp=(array)$data;
                    $first_data=key($data_tmp);
                    $this_day=$first_data;
                    $next_day='';
                    $i=0;
                    $j=0;
                    $get_hasil_rumus=[];
                    while($i<=31){
                        $tgl_next_tmp = new \DateTime($this_day);
                        $tgl_next_tmp->modify('+1 day');
                        $next_day=$tgl_next_tmp->format('Y-m-d');

                        if(!empty($data_tmp[$this_day])){
                            $get_data=$data_tmp[$this_day];
                            foreach($get_data as $val){
                                
                                if($get_rumus[$j]==$val->id_jenis_jadwal){
                                    $get_hasil_rumus[$this_day]=$val->id_jenis_jadwal;
                                    $j++;
                                }else{
                                    $j=0;
                                }
                            }    
                        }
                        $this_day=$next_day;
                        $i++;
                        if(count($get_hasil_rumus)==count($get_rumus)){
                            $i=32;
                        }
                    }

                    $return=[];
                    if($get_hasil_rumus){
                        $filter_tahun_bulan=$params['tahun'].'-'.$params['bulan'];
                        $get_tgl_per_bulan=(new \App\Http\Traits\AbsensiFunction)->get_tgl_per_bulan($filter_tahun_bulan);
                        // $tgl_akhir=!empty($get_tgl_per_bulan->tgl_start_end[1]) ? $get_tgl_per_bulan->tgl_start_end[1] : $filter_tahun_bulan.'-31';
                        $tgl_akhir=$filter_tahun_bulan.'-31';

                        foreach($get_hasil_rumus as $tgl => $val){
                            if(!empty($data_tmp[$tgl])){
                                $data_wow=$data_tmp[$tgl];
                                
                                $paramater=[
                                    'tgl_dasar'=>$tgl,
                                    'tgl_awal'=>$tgl,
                                    'jml_range'=>count($get_hasil_rumus),
                                    'value_data'=>$data_wow,
                                    'tgl_akhir'=>$tgl_akhir
                                ];
                
                                if(!empty($return)){
                                    $paramater['callback']=$return;
                                }
                
                                $return=$this->hitung_tgl_rekursif_bulan($paramater);

                            }

                        }
                    }

                    if(!empty($return[$tahun_old][$bulan_old])){
                        unset($return[$tahun_old][$bulan_old]);
                    }

                    if(empty($return[$tahun_old])){
                        unset($return[$tahun_old]);
                    }

                    $data_insert=[];
                    if($return){

                        DB::beginTransaction();
                        
                        try {
                            foreach($return as $k_hasil => $v_hasil){
                                foreach($v_hasil as $bulan => $nilai){
                                    if(count($nilai)>=28){
                                        $nilai_json=json_encode($nilai);
                                        $data_insert[]=[
                                            'id_template_jadwal_shift'=>$params['id_template_jadwal_shift'],
                                            'tahun'=>$k_hasil,
                                            'bulan'=>$bulan,
                                            'data'=>$nilai_json
                                        ];

                                        $delete_data = (new \App\Models\RefTemplateJadwalShiftLanjutan)->where([
                                            'id_template_jadwal_shift'=> $params['id_template_jadwal_shift'],
                                            'tahun'=>$k_hasil,
                                            'bulan'=>$bulan,
                                        ])->delete();
                                    }
                                }
                            }

                            $is_save=0;
                            if($data_insert){
                                $model=(new \App\Models\RefTemplateJadwalShiftLanjutan);
                                if($model->insert($data_insert)){
                                    $is_save=1;
                                }
                            }

                            if ($is_save) {
                                DB::commit();
                                return $this->setListShift($params);
                                // $pesan = ['success', $message_default['success'], 2];
                            } else {
                                DB::rollBack();
                                return [];
                            }


                            
                        } catch (\Illuminate\Database\QueryException $e) {
                            DB::rollBack();
                            if ($e->errorInfo[1] == '1062') {
                            }
                            return [];
                        } catch (\Throwable $e) {
                            DB::rollBack();
                            return [];
                        }
                    }
                }
                
                return [];
            }
        }
    }
}