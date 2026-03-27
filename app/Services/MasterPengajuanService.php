<?php

namespace App\Services;
use Illuminate\Support\Facades\DB;

class MasterPengajuanService {
    public function mappingApproved($jns_pengajuan, $id_ruangan) {
        return DB::table('approval_mappings')
                ->where('jenis_pengajuan', $jns_pengajuan)
                ->where('level', 1)
                ->where('aktif', 1)
                ->where('scope_type', 'ruangan')
                ->where('scope_id', $id_ruangan)
                ->first();
    }

    public function getApproved($mapping) {
        return DB::table('ref_karyawan')
                ->select('id_karyawan')
                ->where('id_jabatan', '=', $mapping->approver_value)
                ->where('id_ruangan', '=', $mapping->scope_id)
                ->first();
    }

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
}