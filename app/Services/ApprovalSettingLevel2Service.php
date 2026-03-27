<?php

namespace App\Services;

use Illuminate\Support\Facades\DB;

class ApprovalSettingLevel2Service extends BaseService {
    public function getAllLevel2($params = [])
    {
        $query = DB::table('approval_mappings as am')
            ->select(
                'am.id',
                'am.level',
                'am.jenis_pengajuan',
                'am.scope_type',
                'am.scope_id',
                'am.aktif as status',
                'rd.nm_departemen',
                'k.nm_karyawan',
                'rj.nm_jabatan',
                'rr.nm_ruangan'
            )
            ->leftJoin('ref_jabatan as rj', 'am.approver_value', '=', 'rj.id_jabatan')
            ->leftJoin('ref_karyawan as k', 'am.id_karyawan', '=', 'k.id_karyawan')
            ->leftJoin('ref_departemen as rd', 'k.id_departemen', '=', 'rd.id_departemen')
            ->leftJoin('ref_ruangan as rr', 'am.scope_id', '=', 'rr.id_ruangan')
            ->where('am.level', '=', 2)
            ->orderBy('am.created_at', 'desc');

        if (!empty($params['search'])) {
            $query->where(function ($q) use ($params) {
                $q->where('am.level', 'like', '%'.$params['search'].'%')
                    ->orWhere('k.nm_karyawan', 'like', '%'.$params['search'].'%');
            });
        }

        return $query;
    }

    public function insert($data_req)
    {
        return DB::transaction(function () use ($data_req) {

            $id_karyawan = $data_req['id_karyawan']; 
            $id_ruangan = $data_req['id_ruangan'];
            $id_jabatan = $data_req['id_jabatan'];

            $existing = DB::table('approval_mappings')
                ->where('id_karyawan', $id_karyawan)
                ->where('scope_id', $id_ruangan)
                ->where('approver_value', $id_jabatan)
                ->where('jenis_pengajuan', $data_req['jenis_pengajuan'])
                ->where('level', 2)
                ->where('aktif', 1)
                ->first();

            if ($existing) {
                throw new \Exception('Mapping approval sudah ada dan masih aktif.');
            }

            DB::table('approval_mappings')->insert([
                'id_karyawan'      => $id_karyawan,
                'scope_id'         => $id_ruangan,
                'approver_value'   => $id_jabatan,
                'approver_type'     => 'jabatan',
                'scope_type'       => 'ruangan',
                'aktif'            => 1,
                'jenis_pengajuan'  => $data_req['jenis_pengajuan'],
                'level'            => 2,
                'created_at'       => now(),
                'updated_at'       => now(),
            ]);

            return true;
        });
    }

    public function getApprovalMappings($id_approval_mapping)
    {
        return DB::table('approval_mappings as am')
            ->select(
                'am.id',
                'am.level',
                'am.jenis_pengajuan',
                'am.scope_type',
                'am.scope_id',
                'am.aktif as status',
                'am.approver_value',
                'rd.nm_departemen',
                'k.nm_karyawan',
                'rj.nm_jabatan',
                'rr.nm_ruangan'
            )
            ->leftJoin('ref_jabatan as rj', 'am.approver_value', '=', 'rj.id_jabatan')
            ->leftJoin('ref_karyawan as k', 'am.id_karyawan', '=', 'k.id_karyawan')
            ->leftJoin('ref_departemen as rd', 'k.id_departemen', '=', 'rd.id_departemen')
            ->leftJoin('ref_ruangan as rr', function ($join) {
                $join->on('am.scope_id', '=', 'rr.id_ruangan')
                    ->where('am.scope_type', '=', 'ruangan');
            })
            ->where('am.id', '=', $id_approval_mapping)
            ->first();
            
    }

    public function update($data_req)
    {
        return DB::transaction(function () use ($data_req) {
            
            $id_karyawan = $data_req['id_karyawan']; 
            $id_ruangan = $data_req['id_ruangan'];
            $id_jabatan = $data_req['id_jabatan'];
            $id = $data_req['id'];

                $exists = DB::table('approval_mappings')
                    ->where('id_karyawan', $id_karyawan)
                    ->where('scope_id', $id_ruangan)
                    ->where('approver_value', $id_jabatan)
                    ->where('jenis_pengajuan', $data_req['jenis_pengajuan'])
                    ->where('level', 2)
                    ->where('aktif', 1)
                    ->where('id', '!=', $id)
                    ->exists();


            if ($exists) {
                throw new \Exception('Mapping approval sudah ada dan masih aktif.');
            }


            DB::table('approval_mappings')
            ->where('id', $id)
            ->update([
                'aktif'            => $data_req['status'],
                'jenis_pengajuan'  => $data_req['jenis_pengajuan'],
                'updated_at'       => now(),
            ]);

            return true;
        });
    }

    public function delete($data_req) {
        try {
            $id = $data_req['data_sent'];

            DB::table('approval_mappings')
                ->where('id', '=', $id)
                ->delete();

            return true;
        } catch (\Throwable $e) {
            throw $e;
        }
    }


}