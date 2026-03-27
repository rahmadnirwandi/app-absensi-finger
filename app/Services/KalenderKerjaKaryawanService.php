<?php

namespace App\Services;
use Illuminate\Support\Facades\DB;

class KalenderKerjaKaryawanService extends BaseService {

    public function getKaryawan($id_user) {
        return DB::table('uxui_users as u')
                ->select([
                    'u.id',
                    'rk.id_karyawan',
                    'rk.id_ruangan',
                    'rk.id_jabatan',
                    'rk.nm_karyawan',
                    'rk.id_departemen'
                ])
                ->leftJoin('uxui_users_karyawan as uk', 'u.id', '=', 'uk.id_uxui_users')
                ->leftJoin('ref_karyawan as rk', 'uk.id_karyawan', '=', 'rk.id_karyawan')
                ->where('u.id', '=', $id_user)
                ->first();
    }
    public function get_data_karyawan_absensi($params = [], $id_karyawan)
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
            ->leftJoin('uxui_kalender_kerja as jadwal', 'jadwal.id_karyawan', '=', 'karyawan.id_karyawan')
            ->whereNotNull('rku.id_user');

        if (!empty($id_karyawan)) {
            $karyawanQuery->where('karyawan.id_karyawan', $id_karyawan);
        }

        $listIdUser = $karyawanQuery->pluck('rku.id_user')->filter()->unique()->toArray();
        
        // Jika tidak ada user, return empty
        if (empty($listIdUser)) {
            return empty($type) ? collect() : DB::table(DB::raw('(SELECT 1) as empty'))->whereRaw('1=0');
        }

        // Step 2: Get data karyawan lengkap (yang punya jadwal shift)
        $karyawanData = DB::table('ref_karyawan_user as rku')
            ->join('ref_karyawan as karyawan', 'karyawan.id_karyawan', '=', 'rku.id_karyawan')
            ->leftJoin('uxui_kalender_kerja as jadwal', 'jadwal.id_karyawan', '=', 'karyawan.id_karyawan')
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
            ])
            ->get()
            ->unique('id_user')
            ->keyBy('id_user');

        // Step 3: Get data jadwal kerja
        $jadwalData = DB::table('uxui_kalender_kerja as ukk')
            ->leftJoin('ref_jenis_jadwal as rjj', 'rjj.id_jenis_jadwal', '=', 'ukk.id_jenis_jadwal')
            ->whereIn('ukk.id_karyawan', $karyawanData->pluck('id_karyawan'))
            ->whereBetween('ukk.tanggal', [$tgl_awal, $tgl_akhir])
            ->select([
                'ukk.id',
                'ukk.id_karyawan',
                'ukk.tanggal',
                'ukk.id_ruangan',
                'ukk.id_jenis_jadwal',
                'rjj.nm_jenis_jadwal',
                'rjj.masuk_kerja',
                'rjj.pulang_kerja',
            ])
            ->orderBy('ukk.tanggal')
            ->get()
            ->groupBy('id_karyawan');

        // Step 4: Build hasil akhir
        $results = collect();

        foreach ($karyawanData as $id_user => $karyawan) {

            $jadwal = [];
            $id_karyawan = $karyawan->id_karyawan;

            if (isset($jadwalData[$id_karyawan])) {

                foreach ($jadwalData[$id_karyawan] as $row) {
                    $tgl = $row->tanggal;

                    $jadwal[$tgl] = [
                        'id' => $row->id,
                        'id_jenis_jadwal' => $row->id_jenis_jadwal,
                        'nm_jenis_jadwal' => $row->nm_jenis_jadwal,
                        'jam_masuk'       => $row->masuk_kerja,
                        'jam_pulang'      => $row->pulang_kerja,
                        'id_ruangan'      => $row->id_ruangan,
                    ];
                }
            }

            $results->push((object)[
                'id_user'        => $id_user,
                'id_karyawan'    => $karyawan->id_karyawan,
                'id_departemen'    => $karyawan->id_departemen,
                'id_ruangan'    => $karyawan->id_ruangan,
                'id_status_karyawan'    => $karyawan->id_status_karyawan,
                'nm_karyawan'    => $karyawan->nm_karyawan,
                'nm_departemen'  => $karyawan->nm_departemen,
                'nm_ruangan'     => $karyawan->nm_ruangan,
                'nm_status_karyawan' => $karyawan->nm_status_karyawan,
                'nm_jabatan'     => $karyawan->nm_jabatan,
                'jadwal'         => $jadwal
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
    }

    public function getJenisJadwal() {
       return DB::table('ref_jenis_jadwal')
        ->select('id_jenis_jadwal','nm_jenis_jadwal','masuk_kerja as jam_masuk','pulang_kerja as jam_pulang','bg_color')
        ->get();
    }

    public function getKaryawanPerunit($id_ruangan) {
        return DB::table('ref_karyawan')
            ->select('id_karyawan', 'id_ruangan', 'nm_karyawan')
            ->where('id_ruangan', $id_ruangan)
            ->get();
    }

    
}