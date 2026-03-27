<?php

namespace App\Services;

use Illuminate\Support\Facades\DB;

class DataCutiKaryawanService extends BaseService {

    public function getListData($params = []) {
       $query = DB::table('uxui_stok_cuti as sc')
            ->select([
                'sc.id',
                'rk.nm_karyawan',
                'jc.nama as nama_jenis_cuti',
                'sc.awal_cuti',
                'sc.akhir_cuti',
                'sc.jumlah',
                'sc.pakai',
                'sc.sisa',
                'sc.tukar',
            ])
            ->join('ref_karyawan as rk', 'sc.id_karyawan', '=', 'rk.id_karyawan')
            ->join('uxui_jenis_cuti as jc', 'sc.id_jenis_cuti', '=', 'jc.id')
            ->orderBy('sc.created_at', 'desc');

            
         if (!empty($params['search'])) {
            $query->where(function ($q) use ($params) {
                $q->where('jc.nama', 'like', '%'.$params['search'].'%')
                ->orWhere('sc.jumlah', 'like', '%'.$params['search'].'%')
                    ->orWhere('rk.nm_karyawan', 'like', '%'.$params['search'].'%');
            });
        }
            
        return $query;
        
    }
    public function insert($data_req) {
        try {
            $query = DB::table('uxui_stok_cuti')->insertGetId([
                'id_karyawan'    => $data_req['id_karyawan'],
                'awal_cuti' => $data_req['awal_cuti'],
                'akhir_cuti' => $data_req['akhir_cuti'],
                'id_jenis_cuti' => $data_req['id_jenis_cuti'],
                'jumlah' => $data_req['jumlah_cuti'],
                'pakai' => $data_req['pakai'],
                'sisa' => $data_req['sisa'],
                'tukar' => $data_req['tukar'],
                'created_at'    => now(),
                'updated_at'    => now(),
            ]);

            if (!$query) {
                return false;
            }

            return true;
        } catch (\Throwable $e) {
            throw $e;
        }
    }

    public function getDataCutiKaryawan($id_cuti_karyawan) {
        return DB::table('uxui_stok_cuti as sc')
            ->select([
                'sc.id',
                'sc.id_jenis_cuti',
                'sc.id_karyawan',
                'rk.nm_karyawan',
                'jc.nama as nama_jenis_cuti',
                'sc.awal_cuti',
                'sc.akhir_cuti',
                'sc.jumlah',
                'sc.pakai',
                'sc.sisa',
                'sc.tukar',
            ])
            ->join('ref_karyawan as rk', 'sc.id_karyawan', '=', 'rk.id_karyawan')
            ->join('uxui_jenis_cuti as jc', 'sc.id_jenis_cuti', '=', 'jc.id')
            ->where('sc.id', '=', $id_cuti_karyawan)
            ->first();
    }

    public function update($data_req) {
        try {
            $updateData = [
                'id_karyawan'    => $data_req['id_karyawan'],
                'awal_cuti' => $data_req['awal_cuti'],
                'akhir_cuti' => $data_req['akhir_cuti'],
                'id_jenis_cuti' => $data_req['id_jenis_cuti'],
                'jumlah' => $data_req['jumlah_cuti'],
                'pakai' => $data_req['pakai'],
                'sisa' => $data_req['sisa'],
                'tukar' => $data_req['tukar'],
                'updated_at'      => now(),
            ];

            DB::table('uxui_stok_cuti')
                ->where('id', '=', $data_req['id'])
                ->update($updateData);

            return true;

        } catch (\Throwable $e) {
             throw $e;
        }
    }

    public function insertSemuaKaryawan($data_req)
    {
        try {
            $karyawan = DB::table('ref_karyawan')->pluck('id_karyawan');

            $dataInsert = [];

            foreach ($karyawan as $id_karyawan) {
                $dataInsert[] = [
                    'id_karyawan'   => $id_karyawan,
                    'awal_cuti'     => $data_req['awal_cuti'],
                    'akhir_cuti'    => $data_req['akhir_cuti'],
                    'id_jenis_cuti' => $data_req['id_jenis_cuti_all'],
                    'jumlah'        => $data_req['jumlah_cuti_all'],
                    'pakai'         => 0,
                    'sisa'          => $data_req['jumlah_cuti_all'],
                    'tukar'         => 0,
                    'created_at'    => now(),
                    'updated_at'    => now(),
                ];
            }

            DB::table('uxui_stok_cuti')->insert($dataInsert);

            return true;
        } catch (\Throwable $e) {
            throw $e;
        }
    }


    public function delete($data_req) {
        try {
            $id = $data_req['data_sent'];

            DB::table('uxui_stok_cuti')
                ->where('id', '=', $id)
                ->delete();

            return true;
        } catch (\Throwable $e) {
            throw $e;
        }
    }
}