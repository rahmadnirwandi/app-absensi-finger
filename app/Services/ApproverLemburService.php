<?php

namespace App\Services;

use App\Services\BaseService;
use Illuminate\Support\Facades\DB;

class ApproverLemburService extends BaseService
{
    public function getListData($params = [])
    {
        $query = DB::table('uxui_lembur as l')
            ->select(
                'l.id_karyawan',
                'l.id',
                'l.keterangan',
                'l.tgl_pengajuan',
                'l.jam_mulai',
                'l.jam_selesai',
                'l.file_dokumen',
                'l.jenis_lembur',
                'l.tgl_lembur',
                'l.rupiah_per_jam',
                'l.total_jam',
                'l.total_bayar',
                'l.deposit_jam',
                'l.redeemed',
                'l.status',
                'l.current_level',
                'l.created_at',
                'k.nip',
                'k.nm_karyawan'
            )
            ->join('ref_karyawan as k', 'k.id_karyawan', '=', 'l.id_karyawan')
            ->where('l.status', '=', 'pending')
            ->orderBy('l.created_at', 'desc');

        if (empty($params['is_super_admin']) || $params['is_super_admin'] === false) {
            
            $hasGlobalMapping = DB::table('approval_mappings')
                ->where('jenis_pengajuan', 'lembur')
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
                            ->where('am.jenis_pengajuan', 'lembur')
                            ->where('am.approver_type', 'jabatan')
                            ->where('am.approver_value', $params['id_jabatan'])
                            ->where('am.scope_id', $params['id_ruangan'])
                            ->where('am.aktif', 1);

                        if ($hasGlobalMapping) {
                            $sub->where('am.scope_type', 'global')
                                ->whereColumn('l.current_level', '>=', 'am.level');
                        } else {
                            $sub->where('am.scope_type', 'ruangan')
                                ->whereColumn('am.scope_id', 'l.id_ruangan');
                        }
                    });
                }
            });

            $query->whereNotExists(function ($sub) use ($params) {
                $sub->select(DB::raw(1))
                    ->from('approval_histories as ah')
                    ->whereColumn('ah.pengajuan_id', 'l.id')
                    ->where('ah.jenis_pengajuan', 'lembur')
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

            $pengajuan = DB::table('uxui_lembur')
                ->where('id', $pengajuan_id)
                ->lockForUpdate()
                ->first();

            if (!$pengajuan) {
                throw new \Exception('Pengajuan tidak ditemukan');
            }

            if (empty($params['is_super_admin']) || $params['is_super_admin'] === false) {

                $valid = DB::table('approval_mappings')
                ->where('jenis_pengajuan', 'lembur')
                ->where('level', $pengajuan->current_level)
                ->where('approver_type', 'jabatan')
                ->where('approver_value', $params['id_jabatan'])
                ->where('aktif', 1)
                ->exists();

                if (!$valid) {
                    throw new \Exception('Anda tidak berhak approve pengajuan ini');
                }

                DB::table('approval_histories')->insert([
                    'jenis_pengajuan' => 'lembur',
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
                    DB::table('uxui_lembur')
                        ->where('id', $pengajuan_id)
                        ->update([
                            'current_level' => $pengajuan->current_level + 1,
                            'updated_at'    => now(),
                        ]);
                } else {
                    DB::table('uxui_lembur')
                        ->where('id', $pengajuan_id)
                        ->update([
                            'status'        => 'approved',
                            'updated_at'    => now(),
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

            $pengajuan = DB::table('uxui_lembur')
                ->where('id', $pengajuan_id)
                ->lockForUpdate()
                ->first();

            if (!$pengajuan) {
                throw new \Exception('Pengajuan tidak ditemukan');
            }

            if (empty($params['is_super_admin']) || $params['is_super_admin'] === false) {

                $valid = DB::table('approval_mappings')
                ->where('jenis_pengajuan', 'lembur')
                ->where('level', $pengajuan->current_level)
                ->where('approver_type', 'jabatan')
                ->where('approver_value', $params['id_jabatan'])
                ->where('aktif', 1)
                ->exists();

                if (!$valid) {
                    throw new \Exception('Anda tidak berhak approve pengajuan ini');
                }

                DB::table('approval_histories')->insert([
                    'jenis_pengajuan' => 'lembur',
                    'pengajuan_id'    => $pengajuan_id,
                    'level'           => $pengajuan->current_level,
                    'approver_id'     => $params['id_karyawan'],
                    'catatan'         => $reason,
                    'status'          => 'rejected',
                    'approved_at'     => now(),
                    'created_at'      => now(),
                    'updated_at'      => now(),
                ]);

                DB::table('uxui_lembur')
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
            ->where('jenis_pengajuan', 'lembur')
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
            ->where('jenis_pengajuan', 'lembur')
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

                'ul.keterangan',
                'ul.tgl_pengajuan',
                'ul.jam_mulai',
                'ul.jam_selesai',
                'ul.file_dokumen',
                'ul.jenis_lembur',
                'ul.tgl_lembur',
                'ul.rupiah_per_jam',
                'ul.total_jam',
                'ul.total_bayar',
                'ul.deposit_jam',
                'ul.redeemed',
                'ul.status',
                'ul.current_level',
                'ul.created_at',

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
            ->leftJoin('uxui_lembur as ul', 'ul.id', '=', 'ah.pengajuan_id')
            ->join('ref_karyawan as pemohon', 'pemohon.id_karyawan', '=', 'ul.id_karyawan')

            ->leftJoin('approval_histories as ah1', function ($join) {
                $join->on('ah1.pengajuan_id', '=', 'ah.pengajuan_id')
                    ->where('ah1.jenis_pengajuan', '=', 'lembur')
                    ->where('ah1.level', '=', 1);
            })
            ->leftJoin('ref_karyawan as approver1', 'approver1.id_karyawan', '=', 'ah1.approver_id')

            ->leftJoin('approval_histories as ah2', function ($join) {
                $join->on('ah2.pengajuan_id', '=', 'ah.pengajuan_id')
                    ->where('ah2.jenis_pengajuan', '=', 'lembur')
                    ->where('ah2.level', '=', 2);
            })
            ->leftJoin('ref_karyawan as approver2', 'approver2.id_karyawan', '=', 'ah2.approver_id')

            ->where('ah.approver_id', $params['id_karyawan'])
            ->where('ah.jenis_pengajuan', 'lembur')
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
