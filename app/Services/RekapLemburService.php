<?php

namespace App\Services;

use Illuminate\Support\Facades\DB;

class RekapLemburService extends BaseService
{
    public function getRekapLembur($params = [])
    {
        $query = DB::table('uxui_lembur as l')
            ->select(
                'l.id_karyawan',
                'k.nip',
                'k.nm_karyawan',
                'rd.nm_departemen',
                'ru.nm_ruangan',
                DB::raw('SUM(l.total_jam) as total_jam_lembur'),
                DB::raw('SUM(l.total_bayar) as total_bayar_lembur')
            )
            ->leftJoin('ref_karyawan as k', 'k.id_karyawan', '=', 'l.id_karyawan')
            ->leftJoin('ref_departemen as rd', 'k.id_departemen', '=', 'rd.id_departemen')
            ->leftJoin('ref_ruangan as ru', 'k.id_ruangan', '=', 'ru.id_ruangan')
            ->where('l.status', 'approved')
            ->groupBy('l.id_karyawan', 'k.nip', 'k.nm_karyawan')
            ->orderBy('k.nm_karyawan', 'asc');

        if (!empty($params['search'])) {
            $query->where(function ($q) use ($params) {
                $q->where('k.nm_karyawan', 'like', '%'.$params['search'].'%')
                ->orWhere('k.nip', 'like', '%'.$params['search'].'%');
            });
        }

        if (!empty($params['filter_nm_departemen'])) {
            $query->where('rd.nm_departemen', $params['filter_nm_departemen'] ?? null);
        }

        if (!empty($params['filter_nm_ruangan'])) {
            $query->where('ru.nm_ruangan', $params['filter_nm_ruangan'] ?? null);
        }

        if (!empty($params['form_filter_start']) && !empty($params['form_filter_end'])) {
            $query->whereBetween('l.tgl_lembur', [
                $params['form_filter_start'],
                $params['form_filter_end']
            ]);
        }

        return $query;
    }

}
