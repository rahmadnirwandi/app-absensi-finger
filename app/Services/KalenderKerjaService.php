<?php

namespace App\Services;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;
use PhpOffice\PhpSpreadsheet\IOFactory;
use PhpOffice\PhpSpreadsheet\Spreadsheet;

class KalenderKerjaService extends BaseService {

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
    public function get_data_karyawan_absensi($params = [], $id_ruangan)
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

        if (!empty($id_ruangan)) {
            $karyawanQuery->where('karyawan.id_ruangan', $id_ruangan);
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

    public function insert(array $data)
    {
        DB::beginTransaction();

        try {

            $idRuangan = $data['id_ruangan'];
            $jadwal    = $data['jadwal'];

            $firstTanggal = collect($jadwal)->first();
            $firstDateKey = array_key_first($firstTanggal);

            $bulan = Carbon::parse($firstDateKey)->month;
            $tahun = Carbon::parse($firstDateKey)->year;

            DB::table('uxui_kalender_kerja')
                ->where('id_ruangan', $idRuangan)
                ->whereMonth('tanggal', $bulan)
                ->whereYear('tanggal', $tahun)
                ->delete();

            $insertData = [];

            foreach ($jadwal as $idPegawai => $tanggalList) {

                foreach ($tanggalList as $tanggal => $idShift) {

                    if (!empty($idShift)) {

                        $insertData[] = [
                            'id_ruangan' => $idRuangan,
                            'id_karyawan' => $idPegawai,
                            'tanggal'    => $tanggal,
                            'id_jenis_jadwal'   => $idShift,
                            'created_at' => now(),
                            'updated_at' => now(),
                        ];
                    }
                }
            }

            if (!empty($insertData)) {
                DB::table('uxui_kalender_kerja')->insert($insertData);
            }

            DB::commit();

        } catch (\Exception $e) {

            DB::rollBack();
            throw $e;

        }
    }

    public function update($data_req)
    {
        try {

            DB::beginTransaction();

            $affected = DB::table('uxui_kalender_kerja')
                ->where('id', $data_req['id'])
                ->update([
                    'id_karyawan' => $data_req['id_karyawan'],
                    'id_jenis_jadwal'    => $data_req['id_shift'],
                    'updated_at'  => now()
                ]);

            if ($affected == 0) {
                throw new \Exception('Data tidak ditemukan atau tidak ada perubahan');
            }

            DB::commit();

            return true;

        } catch (\Throwable $e) {

            DB::rollBack();
            throw new \Exception($e->getMessage());
        }
    }

    public function import($file, $list_tgl, $id_ruangan)
    {
        $spreadsheet = IOFactory::load($file->getPathname());
        $sheet = $spreadsheet->getActiveSheet();
        $rows = $sheet->toArray();

        $listShift = DB::table('ref_jenis_jadwal')
            ->pluck('id_jenis_jadwal', 'nm_jenis_jadwal')
            ->toArray();

        $insertData = [];

        foreach ($rows as $index => $row) {

            if ($index == 0) continue;

            $id_karyawan = $row[1] ?? null;

            if (!$id_karyawan) continue;

            foreach ($list_tgl as $key => $tanggal) {

                $excelColumnIndex = $key + 3;

                $shiftName = trim($row[$excelColumnIndex] ?? '');

                if (!$shiftName) continue;

                $id_shift = $listShift[$shiftName] ?? null;

                if (!$id_shift) continue;

                $insertData[] = [
                    'id_karyawan'     => $id_karyawan,
                    'tanggal'         => $tanggal,
                    'id_jenis_jadwal' => $id_shift,
                    'id_ruangan' => $id_ruangan,
                    'created_at'      => now(),
                    'updated_at'      => now(),
                ];
            }
        }

        if (!empty($insertData)) {

            DB::table('uxui_kalender_kerja')
                ->whereIn('tanggal', $list_tgl)
                ->delete();

            DB::table('uxui_kalender_kerja')->insert($insertData);
        }

        return count($insertData);
    }

    public function generateTemplateExcel($karyawan, $list_tgl)
    {
        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();

        $col = 1;

        $sheet->setCellValueByColumnAndRow($col++,1,'No');
        $sheet->setCellValueByColumnAndRow($col++,1,'ID');
        $sheet->setCellValueByColumnAndRow($col++,1,'Nama');
        foreach ($list_tgl as $tgl) {

            $day = date('d', strtotime($tgl));

            $sheet->setCellValueByColumnAndRow(
                $col++,
                1,
                'tanggal_'.$day
            );
        }

        $row = 2;
        $no = 1;

        foreach ($karyawan as $k) {
            $col = 1;

            $sheet->setCellValueByColumnAndRow($col++,$row,$no);
            $sheet->setCellValueByColumnAndRow($col++,$row,$k->id_karyawan);
            $sheet->setCellValueByColumnAndRow($col++,$row,$k->nm_karyawan);

            foreach ($list_tgl as $tgl) {
                $sheet->setCellValueByColumnAndRow($col++,$row,'');
            }

            $row++;
            $no++;
        }

        $lastColumn = $sheet->getHighestColumn();
        $sheet->getStyle("A1:{$lastColumn}1")->getFont()->setBold(true);

        // AUTO WIDTH
        foreach(range('A',$lastColumn) as $column){
            $sheet->getColumnDimension($column)->setAutoSize(true);
        }

        return $spreadsheet;
    }

    public function downloadExcel($spreadsheet, $fileName)
    {
        $writer = IOFactory::createWriter($spreadsheet, 'Xlsx');

        header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        header("Content-Disposition: attachment;filename=\"$fileName\"");
        header('Cache-Control: max-age=0');

        $writer->save('php://output');
        exit;
    }
}