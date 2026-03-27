<?php

namespace App\Services;

use App\Services\BaseService;
use Illuminate\Support\Facades\DB;

class ApproverCutiService extends BaseService
{
    public function getListData($params = [])
    {
        $query = DB::table('uxui_cuti as uc')
            ->select(
                'uc.id',
                'uc.id_karyawan',
                'uc.keterangan',
                'uc.tgl_mulai',
                'uc.tgl_selesai',
                'uc.status',
                'uc.current_level',
                'uc.tgl_pengajuan',
                'jc.nama as nm_jenis_cuti',
                'uc.created_at',
                'k.nip',
                'k.nm_karyawan'
            )
            ->join('ref_karyawan as k', 'k.id_karyawan', '=', 'uc.id_karyawan')
            ->join('uxui_jenis_cuti as jc', 'uc.id_jenis_cuti', '=', 'jc.id')
            ->where('uc.status', '=', 'pending')
            ->orderBy('uc.created_at', 'desc');

        if (empty($params['is_super_admin']) || $params['is_super_admin'] === false) {
            
            $hasGlobalMapping = DB::table('approval_mappings')
                ->where('jenis_pengajuan', 'cuti')
                ->where('approver_type', 'jabatan')
                ->where('approver_value', $params['id_jabatan'])
                ->where('scope_id', $params['id_ruangan'])
                ->where('scope_type', 'global')
                ->where('aktif', 1)
                ->exists();

            $query->where(function ($q) use ($params, $hasGlobalMapping) {
                if (!empty($params['id_jabatan'])) {
                    $q->whereExists(function ($sub) use ($params, $hasGlobalMapping) {
                        $sub->select(DB::raw(1))
                            ->from('approval_mappings as am')
                            ->where('am.jenis_pengajuan', 'cuti')
                            ->where('am.approver_type', 'jabatan')
                            ->where('am.approver_value', $params['id_jabatan'])
                            ->where('am.scope_id', $params['id_ruangan'])
                            ->where('am.aktif', 1);

                        if ($hasGlobalMapping) {
                            $sub->where('am.scope_type', 'global')
                                ->whereColumn('uc.current_level', '>=', 'am.level');
                        } else {
                            $sub->where('am.scope_type', 'ruangan')
                                ->whereColumn('am.scope_id', 'uc.id_ruangan');
                        }
                    });
                }
            });

            $query->whereNotExists(function ($sub) use ($params) {
                $sub->select(DB::raw(1))
                    ->from('approval_histories as ah')
                    ->whereColumn('ah.pengajuan_id', 'uc.id')
                    ->where('ah.jenis_pengajuan', 'cuti')
                    ->where('ah.approver_id', $params['id_karyawan']);
            });
        }

        if (!empty($params['search'])) {
            $query->where(function ($q) use ($params) {
                $q->where('k.nm_karyawan', 'like', '%'.$params['search'].'%')
                ->orWhere('k.nip', 'like', '%'.$params['search'].'%')
                ->orWhere('ah.status', 'like', '%'.$params['search'].'%');
            });
        }

        return $query;
    }

    public function approved($params=[])
    {
        DB::beginTransaction();

        try {

            $pengajuan_id = $params['data_req']['pengajuan_id'];

            $pengajuan = DB::table('uxui_cuti')
                ->where('id', $pengajuan_id)
                ->lockForUpdate()
                ->first();

            if (!$pengajuan) {
                throw new \Exception('Pengajuan tidak ditemukan');
            }

            if (empty($params['is_super_admin']) || $params['is_super_admin'] === false) {

                $valid = DB::table('approval_mappings')
                ->where('jenis_pengajuan', 'cuti')
                ->where('level', $pengajuan->current_level)
                ->where('approver_type', 'jabatan')
                ->where('approver_value', $params['id_jabatan'])
                ->where('aktif', 1)
                ->exists();

                if (!$valid) {
                    throw new \Exception('Anda tidak berhak approve pengajuan ini');
                }

                DB::table('approval_histories')->insert([
                    'jenis_pengajuan' => 'cuti',
                    'pengajuan_id'    => $pengajuan_id,
                    'level'           => $pengajuan->current_level,
                    'approver_id'     => $params['id_karyawan'],
                    'status'          => 'approved',
                    'approved_at'     => now(),
                    'created_at'      => now(),
                    'updated_at'      => now(),
                ]);

                $maxLevel = DB::table('approval_mappings')
                    ->where('jenis_pengajuan', 'cuti')
                    ->where('aktif', 1)
                    ->max('level');

                if ($pengajuan->current_level < $maxLevel) {

                    DB::table('uxui_cuti')
                        ->where('id', $pengajuan_id)
                        ->update([
                            'current_level' => $pengajuan->current_level + 1,
                            'updated_at'    => now(),
                        ]);

                } else {
                    
                    $stokCuti = DB::table('uxui_stok_cuti')
                        ->where('id_karyawan', $pengajuan->id_karyawan)
                        ->where('id_jenis_cuti', $pengajuan->id_jenis_cuti)
                        ->lockForUpdate()
                        ->first();

                    if (!$stokCuti) {
                        throw new \Exception('Stok cuti tidak ditemukan');
                    }

                    if ($stokCuti->sisa < $pengajuan->jumlah_hari) {
                        throw new \Exception('Sisa cuti tidak mencukupi');
                    }

                    DB::table('uxui_stok_cuti')
                        ->where('id', $stokCuti->id)
                        ->update([
                            'sisa'  => $stokCuti->sisa - $pengajuan->jumlah_hari,
                            'pakai' => $stokCuti->pakai + $pengajuan->jumlah_hari,
                            'updated_at' => now(),
                        ]);

                    DB::table('uxui_cuti')
                        ->where('id', $pengajuan_id)
                        ->update([
                            'status'     => 'approved',
                            'updated_at' => now(),
                        ]);
                }


            }else {
                throw new \Exception('Anda tidak berhak approve pengajuan ini');
            }

            DB::commit();
            return true;

        } catch (\Throwable $e) {
            DB::rollBack();
            \Log::error($e->getMessage());
            return false;
        }
    }

    public function rejected($params = []) {
        DB::beginTransaction();

        try {

            $pengajuan_id = $params['data_req']['pengajuan_id'];
            $reason = $params['data_req']['reason'];

            $pengajuan = DB::table('uxui_cuti')
                ->where('id', $pengajuan_id)
                ->lockForUpdate()
                ->first();

            if (!$pengajuan) {
                throw new \Exception('Pengajuan tidak ditemukan');
            }

            if (empty($params['is_super_admin']) || $params['is_super_admin'] === false) {

                $valid = DB::table('approval_mappings')
                ->where('jenis_pengajuan', 'cuti')
                ->where('level', $pengajuan->current_level)
                ->where('approver_type', 'jabatan')
                ->where('approver_value', $params['id_jabatan'])
                ->where('aktif', 1)
                ->exists();

                if (!$valid) {
                    throw new \Exception('Anda tidak berhak approve pengajuan ini');
                }

                DB::table('approval_histories')->insert([
                    'jenis_pengajuan' => 'cuti',
                    'pengajuan_id'    => $pengajuan_id,
                    'level'           => $pengajuan->current_level,
                    'approver_id'     => $params['id_karyawan'],
                    'catatan'         => $reason,
                    'status'          => 'rejected',
                    'approved_at'     => now(),
                    'created_at'      => now(),
                    'updated_at'      => now(),
                ]);

                DB::table('uxui_cuti')
                ->where('id', $pengajuan_id)
                ->update([
                    'status'     => 'rejected',
                    'updated_at' => now(),
                ]);

            }else {
                throw new \Exception('Anda tidak berhak approve pengajuan ini');
            }

            DB::commit();
            return true;

        } catch (\Throwable $e) {
            DB::rollBack();
            \Log::error($e->getMessage());
            return false;
        }
    }

    public function userLevel1($id_jabatan, $id_ruangan)
    {
        return DB::table('approval_mappings')
            ->select('level')
            ->where('jenis_pengajuan', 'cuti')
            ->where('approver_type', 'jabatan')
            ->where('approver_value', $id_jabatan)
            ->where('scope_id', $id_ruangan)
            ->where('level', 1)
            ->where('aktif', 1)
            ->first();
    }

    public function userLevel2($id_jabatan, $id_ruangan)
    {
        return DB::table('approval_mappings')
            ->select('level')
            ->where('jenis_pengajuan', 'cuti')
            ->where('approver_type', 'jabatan')
            ->where('approver_value', $id_jabatan)
            ->where('scope_id', $id_ruangan)
            ->where('level', 2)
            ->where('aktif', 1)
            ->first();
    }

    public function getListDataByStatus($params = [], $status = 'approved')
    {
        $query = DB::table('approval_histories as ah')
            ->select(
                'ah.id',
                'ah.level',
                'ah.status as status_approved_histories',
                'ah.catatan',
                'ah.approved_at',

                'uc.id',
                'uc.id_karyawan',
                'uc.keterangan',
                'uc.tgl_mulai',
                'uc.tgl_selesai',
                'uc.status',
                'uc.current_level',
                'uc.tgl_pengajuan',
                'jc.nama as nm_jenis_cuti',
                'uc.created_at',

                'pemohon.nip as nip_pemohon',
                'pemohon.nm_karyawan as nama_pemohon',

                'ah1.status as status_level1',
                'ah1.catatan as catatan_level1',
                'ah1.approved_at as approved_at_level1',
                'approver1.nip as nip_approver_level1',
                'approver1.nm_karyawan as nama_approver_level1',

                'ah2.status as status_level2',
                'ah2.catatan as catatan_level2',
                'ah2.approved_at as approved_at_level2',
                'approver2.nip as nip_approver_level2',
                'approver2.nm_karyawan as nama_approver_level2'
            )
            ->leftJoin('uxui_cuti as uc', 'uc.id', '=', 'ah.pengajuan_id')
            ->leftJoin('uxui_jenis_cuti as jc', 'uc.id_jenis_cuti', '=', 'jc.id')
            ->join('ref_karyawan as pemohon', 'pemohon.id_karyawan', '=', 'uc.id_karyawan')

            ->leftJoin('approval_histories as ah1', function ($join) {
                $join->on('ah1.pengajuan_id', '=', 'ah.pengajuan_id')
                    ->where('ah1.jenis_pengajuan', '=', 'cuti')
                    ->where('ah1.level', '=', 1);
            })
            ->leftJoin('ref_karyawan as approver1', 'approver1.id_karyawan', '=', 'ah1.approver_id')

            ->leftJoin('approval_histories as ah2', function ($join) {
                $join->on('ah2.pengajuan_id', '=', 'ah.pengajuan_id')
                    ->where('ah2.jenis_pengajuan', '=', 'cuti')
                    ->where('ah2.level', '=', 2);
            })
            ->leftJoin('ref_karyawan as approver2', 'approver2.id_karyawan', '=', 'ah2.approver_id')

            ->where('ah.approver_id', $params['id_karyawan'])
            ->where('ah.jenis_pengajuan', 'cuti')
            ->where('ah.status', $status) 

            ->orderBy('ah.approved_at', 'desc');

        if (!empty($params['search'])) {
            $query->where(function ($q) use ($params) {
                $q->where('pemohon.nm_karyawan', 'like', '%'.$params['search'].'%')
                ->orWhere('pemohon.nip', 'like', '%'.$params['search'].'%');
            });
        }

        return $query;
    }

}
