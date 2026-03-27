<?php

namespace App\Services;
use Illuminate\Support\Facades\DB;

class DataLaporanPresensiService extends BaseService
{
    public function __construct(){
        parent::__construct();
    }

    public function getRekapPresensi($params = [], $type)
    {
        ini_set("memory_limit", "800M");
        DB::statement("SET SESSION group_concat_max_len = 15000;");

        $id_jenis_jadwal = !empty($params['id_jenis_jadwal']) ? $params['id_jenis_jadwal'] : '';
        $tgl_awal = !empty($params['tanggal'][0]) ? $params['tanggal'][0] : date('Y-m-d');
        $tgl_akhir = !empty($params['tanggal'][1]) ? $params['tanggal'][1] : date('Y-m-d');

        $limit_data = !empty($params['limit']) ? $params['limit'] : [];
        $limit_data = (new \App\Http\Traits\GlobalFunction)->limit_mysql_manual($limit_data);

        unset($params['tanggal']);
        unset($params['id_jenis_jadwal']);
        unset($params['limit']);

        // Step 1: Get raw data dari data_absensi_karyawan_rekap
        $query = DB::table('data_absensi_karyawan_rekap as utama')
            ->leftJoin('ref_karyawan as karyawan', 'karyawan.id_karyawan', '=', 'utama.id_karyawan')
            ->leftJoin('ref_jabatan as rj', 'rj.id_jabatan', '=', 'utama.id_jabatan')
            ->leftJoin('ref_departemen as rd', 'rd.id_departemen', '=', 'utama.id_departemen')
            ->leftJoin('ref_ruangan as rr', 'rr.id_ruangan', '=', 'utama.id_ruangan')
            ->leftJoin('ref_status_karyawan as rsk', 'rsk.id_status_karyawan', '=', 'utama.id_status_karyawan')
            ->whereBetween('tgl_presensi', [$tgl_awal, $tgl_akhir])
            ->select([
                'utama.id_user',
                'utama.id_karyawan',
                'karyawan.nm_karyawan',
                'utama.id_departemen',
                'rd.nm_departemen',
                'utama.id_ruangan',
                'rr.nm_ruangan',
                'utama.id_status_karyawan',
                'rsk.nm_status_karyawan',
                'utama.id_jabatan',
                'rj.nm_jabatan',
                'utama.tgl_presensi',
                'utama.presensi_jadwal',
                'utama.presensi_all',
                'utama.total_waktu_kerja_user_sec',
                'utama.status_kerja',
            ])
            ->orderBy('tgl_presensi')
            ->orderBy('utama.id_departemen');

        // Filter id_jenis_jadwal jika ada
        if (!empty($id_jenis_jadwal)) {
            $query->where('utama.id_jenis_jadwal', $id_jenis_jadwal);
        }

        // Jika butuh query builder (untuk pagination, dll)
        if (!empty($type)) {
            // Apply search filter ke query
            if ($params) {
                $list_search = [
                    'where_or' => ['nm_karyawan'],
                ];
                $query = (new \App\Models\MyModel)->set_where($query, $params, $list_search);
            }
            return $query;
        }

        // Step 2: Get data dan process di PHP
        $rawData = $query->get();

        // Step 3: Group by id_user dan build JSON
        $groupedByUser = $rawData->groupBy('id_user');

        $results = collect();

        foreach ($groupedByUser as $id_user => $userData) {
            $first = $userData->first();

            $presensi_jadwal = [];
            $presensi_all = [];
            $detail_hitung = [];
            $sum_waktu_kerja_user_sec = 0;

            foreach ($userData as $row) {
                $tgl = $row->tgl_presensi;

                // presensi_jadwal: tgl => value
                $presensi_jadwal[$tgl] = $row->presensi_jadwal;

                // presensi_all: tgl => [value] (array)
                $presensi_all[$tgl] = [$row->presensi_all];

                // detail_hitung: tgl => object
                $detail_hitung[$tgl] = [
                    'total_waktu_kerja_user_sec' => $row->total_waktu_kerja_user_sec,
                    'status_kerja'               => $row->status_kerja,
                ];

                // Sum waktu kerja
                $sum_waktu_kerja_user_sec += (int)$row->total_waktu_kerja_user_sec;
            }

            $results->push((object) [
                'id_user'                   => $id_user,
                'id_karyawan'               => $first->id_karyawan,
                'nm_karyawan'               => $first->nm_karyawan,
                'id_departemen'             => $first->id_departemen,
                'nm_departemen'             => $first->nm_departemen,
                'id_ruangan'                => $first->id_ruangan,
                'nm_ruangan'                => $first->nm_ruangan,
                'id_status_karyawan'        => $first->id_status_karyawan,
                'nm_status_karyawan'        => $first->nm_status_karyawan,
                'id_jabatan'                => $first->id_jabatan,
                'nm_jabatan'                => $first->nm_jabatan,
                'presensi_jadwal'           => json_encode($presensi_jadwal),
                'presensi_all'              => json_encode($presensi_all),
                'detail_hitung'             => json_encode($detail_hitung),
                'sum_waktu_kerja_user_sec'  => $sum_waktu_kerja_user_sec,
            ]);
        }

        // Step 4: Apply search filter di collection
        if (!empty($params['search'])) {
            $search = $params['search'];
            $results = $results->filter(function ($item) use ($search) {
                return stripos((string)$item->nm_karyawan, $search) !== false;
            })->values();
            unset($params['search']);
        }

        // Apply remaining filters
        if ($params) {
            $results = $this->applyCollectionFilters($results, $params);
        }

        // Step 5: Apply limit
        if (!empty($limit_data)) {
            $offset = isset($limit_data[0]) ? (int)$limit_data[0] : 0;
            $length = isset($limit_data[1]) ? (int)$limit_data[1] : null;
            $results = $results->slice($offset, $length)->values();
        }

        return $results;
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
}