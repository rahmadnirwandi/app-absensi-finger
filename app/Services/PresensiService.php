<?php

namespace App\Services;

use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class PresensiService
{
    private function getIdUser($id_karyawan)
    {
        return DB::table('ref_karyawan_user')
            ->where('id_karyawan', $id_karyawan)
            ->value('id_user');
    }

    public function getPresensiBulanan($id_karyawan)
    {
        $id_user = $this->getIdUser($id_karyawan);

        $now = Carbon::now();
        $bulan = $now->month;
        $tahun = $now->year;

        $scanSub = DB::table('ref_data_absensi_tmp')
            ->select(
                DB::raw('DATE(waktu) as tanggal'),
                DB::raw('MIN(waktu) as waktu_scan')
            )
            ->where('id_user', $id_user)
            ->whereMonth('waktu', $bulan)
            ->whereYear('waktu', $tahun)
            ->groupBy(DB::raw('DATE(waktu)'));

        $data = DB::table('uxui_kalender_kerja as k')
            ->join('ref_jenis_jadwal as j', 'k.id_jenis_jadwal', '=', 'j.id_jenis_jadwal')
            ->leftJoinSub($scanSub, 'scan', function ($join) {
                $join->on('k.tanggal', '=', 'scan.tanggal');
            })
            ->where('k.id_karyawan', $id_karyawan)
            ->whereMonth('k.tanggal', $bulan)
            ->whereYear('k.tanggal', $tahun)
            ->select('k.tanggal', 'j.masuk_kerja', 'scan.waktu_scan')
            ->get();

        $tepat = 0;
        $telat = 0;
        $alpa  = 0;

        foreach ($data as $item) {

            if (!$item->waktu_scan) {
                $alpa++;
                continue;
            }

            $jamScan  = Carbon::parse($item->waktu_scan)->format('H:i:s');
            $jamMasuk = $item->masuk_kerja;

            if ($jamScan <= $jamMasuk) {
                $tepat++;
            } else {
                $telat++;
            }
        }

        return [
            'tepat' => $tepat,
            'telat' => $telat,
            'alpa'  => $alpa,
        ];
    }

    public function getLogTerakhir($id_karyawan)
    {
        $id_user = $this->getIdUser($id_karyawan);

        return DB::table('ref_data_absensi_tmp')
            ->where('id_user', $id_user)
            ->orderBy('waktu', 'asc')
            ->get();
    }

    public function getStatusHariIni($id_karyawan)
    {
        $id_user = $this->getIdUser($id_karyawan);
        $today = Carbon::today();

        $scan = DB::table('ref_data_absensi_tmp')
            ->where('id_user', $id_user)
            ->whereDate('waktu', $today)
            ->orderBy('waktu')
            ->get();

        if ($scan->isEmpty()) {
            return [
                'scanMasuk'  => null,
                'scanPulang' => null,
            ];
        }

        return [
            'scanMasuk'  => Carbon::parse($scan->first()->waktu)->format('H:i:s'),
            'scanPulang' => Carbon::parse($scan->last()->waktu)->format('H:i:s'),
        ];
    }
}