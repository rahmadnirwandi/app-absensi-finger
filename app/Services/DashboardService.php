<?php

namespace App\Services;

use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class DashboardService
{
    public function getSummary()
    {
        $today = Carbon::today()->toDateString();

        $totalKaryawan = DB::table('ref_karyawan')->count();

        $scanSub = DB::table('ref_data_absensi_tmp')
            ->select(
                'id_user',
                DB::raw('MIN(waktu) as waktu_scan')
            )
            ->whereDate('waktu', $today)
            ->groupBy('id_user');

        $data = DB::table('uxui_kalender_kerja as k')
            ->join('ref_jenis_jadwal as j', 'k.id_jenis_jadwal', '=', 'j.id_jenis_jadwal')
            ->join('ref_karyawan_user as rku', 'k.id_karyawan', '=', 'rku.id_karyawan')
            ->leftJoinSub($scanSub, 'scan', function ($join) {
                $join->on('rku.id_user', '=', 'scan.id_user');
            })
            ->whereDate('k.tanggal', $today)
            ->select(
                'k.id_karyawan',
                'j.masuk_kerja',
                'scan.waktu_scan'
            )
            ->get();

        $totalTepatWaktu = 0;
        $totalTerlambat  = 0;
        $totalAlpa       = 0;

        foreach ($data as $item) {

            if (!$item->waktu_scan) {
                $totalAlpa++;
                continue;
            }

            $jamScan  = Carbon::parse($item->waktu_scan)->format('H:i:s');
            $jamMasuk = $item->masuk_kerja;

            if ($jamScan <= $jamMasuk) {
                $totalTepatWaktu++;
            } else {
                $totalTerlambat++;
            }
        }

        return [
            'totalKaryawan'   => $totalKaryawan,
            'totalTepatWaktu' => $totalTepatWaktu,
            'totalTerlambat'  => $totalTerlambat,
            'totalAlpa'       => $totalAlpa,
        ];

    }


    public function getDataBulanan()
    {
        $year = now()->year;

        $scanSub = DB::table('ref_data_absensi_tmp')
            ->select(
                'id_user',
                DB::raw('DATE(waktu) as tanggal'),
                DB::raw('MIN(waktu) as waktu_scan')
            )
            ->whereYear('waktu', $year)
            ->groupBy('id_user', DB::raw('DATE(waktu)'));

        $data = DB::table('uxui_kalender_kerja as k')
            ->join('ref_jenis_jadwal as j', 'k.id_jenis_jadwal', '=', 'j.id_jenis_jadwal')
            ->join('ref_karyawan_user as rku', 'k.id_karyawan', '=', 'rku.id_karyawan')
            ->leftJoinSub($scanSub, 'scan', function ($join) {
                $join->on('rku.id_user', '=', 'scan.id_user')
                    ->on('k.tanggal', '=', 'scan.tanggal');
            })
            ->whereYear('k.tanggal', $year)
            ->select(
                DB::raw('MONTH(k.tanggal) as bulan'),
                'j.masuk_kerja',
                'scan.waktu_scan'
            )
            ->get();

        $result = [];

        for ($i = 1; $i <= 12; $i++) {

            $tepat = 0;
            $terlambat = 0;
            $alpa = 0;

            foreach ($data->where('bulan', $i) as $item) {

                if (!$item->waktu_scan) {
                    $alpa++;
                    continue;
                }

                $jamScan  = Carbon::parse($item->waktu_scan)->format('H:i:s');
                $jamMasuk = $item->masuk_kerja;

                if ($jamScan <= $jamMasuk) {
                    $tepat++;
                } else {
                    $terlambat++;
                }
            }

            $result[] = [
                'bulan' => Carbon::create()->month($i)->translatedFormat('M'),
                'tepat_waktu' => $tepat,
                'terlambat'   => $terlambat,
                'alpa'        => $alpa,
            ];
        }

        return $result;
    }

    public function getDataMingguan()
    {
        $start = now()->startOfWeek();
        $end   = now()->endOfWeek();

        $scanSub = DB::table('ref_data_absensi_tmp')
            ->select(
                'id_user',
                DB::raw('DATE(waktu) as tanggal'),
                DB::raw('MIN(waktu) as waktu_scan')
            )
            ->whereBetween('waktu', [$start, $end])
            ->groupBy('id_user', DB::raw('DATE(waktu)'));

        $data = DB::table('uxui_kalender_kerja as k')
            ->join('ref_jenis_jadwal as j', 'k.id_jenis_jadwal', '=', 'j.id_jenis_jadwal')
            ->join('ref_karyawan_user as rku', 'k.id_karyawan', '=', 'rku.id_karyawan')
            ->leftJoinSub($scanSub, 'scan', function ($join) {
                $join->on('rku.id_user', '=', 'scan.id_user')
                    ->on('k.tanggal', '=', 'scan.tanggal');
            })
            ->whereBetween('k.tanggal', [$start, $end])
            ->select(
                'k.tanggal',
                'j.masuk_kerja',
                'scan.waktu_scan'
            )
            ->get();

        $result = [];

        for ($date = $start->copy(); $date <= $end; $date->addDay()) {

            $tepat = 0;
            $terlambat = 0;
            $alpa = 0;

            foreach ($data->where('tanggal', $date->toDateString()) as $item) {

                if (!$item->waktu_scan) {
                    $alpa++;
                    continue;
                }

                $jamScan  = Carbon::parse($item->waktu_scan)->format('H:i:s');
                $jamMasuk = $item->masuk_kerja;

                if ($jamScan <= $jamMasuk) {
                    $tepat++;
                } else {
                    $terlambat++;
                }
            }

            $result[] = [
                'hari' => $date->translatedFormat('l'),
                'tepat_waktu' => $tepat,
                'terlambat'   => $terlambat,
                'alpa'        => $alpa,
            ];
        }

        return $result;
    }
}