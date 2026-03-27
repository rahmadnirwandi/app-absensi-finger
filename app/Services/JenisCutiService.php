<?php

namespace App\Services;

use Illuminate\Support\Facades\DB;

class JenisCutiService extends BaseService {
    public function getListData($params = []) {
       $query = DB::table('uxui_jenis_cuti')
            ->select('id', 'nama', 'jumlah', 'status')
            ->orderBy('created_at', 'desc')
            ->where('status', '=', 1);

            
         if (!empty($params['search'])) {
            $query->where(function ($q) use ($params) {
                $q->where('nama', 'like', '%'.$params['search'].'%')
                ->orWhere('jumlah', 'like', '%'.$params['search'].'%');
            });
        }
            
        return $query;
        
    }

    public function getList($params = [], $type = '') {
       $query = DB::table('uxui_jenis_cuti')
            ->select('id', 'nama', 'jumlah', 'status')
            ->orderBy('created_at', 'desc')
            ->where('status', '=', 1);

            
         if (!empty($params['search'])) {
            $query->where(function ($q) use ($params) {
                $q->where('nama', 'like', '%'.$params['search'].'%')
                ->orWhere('jumlah', 'like', '%'.$params['search'].'%');
            });
        }

        if(empty($type)){
            return $query->get();
        }else{
            return $query;
        }
    }

    public function insert($data_req)
    {
           $query = DB::table('uxui_jenis_cuti')->insertGetId([
                'nama'    => $data_req['nama_cuti'],
                'jumlah' => $data_req['jumlah_cuti'],
                'status'     => 1,
                'created_at'    => now(),
                'updated_at'    => now(),
            ]);

            if (!$query) {
                return false;
            }

            return true;
    }

    public function getJenisCuti($id_jenis_cuti) {
        return DB::table('uxui_jenis_cuti')
            ->select('id', 'nama', 'jumlah', 'status')
            ->where('id', '=', $id_jenis_cuti)
            ->first();
    }

    public function update($data_req) {
        try {
            $updateData = [
                'nama'      => $data_req['nama_cuti'],
                'jumlah' => $data_req['jumlah_cuti'],
                'updated_at'      => now(),
            ];

            DB::table('uxui_jenis_cuti')
                ->where('id', '=', $data_req['id'])
                ->update($updateData);

            return true;

        } catch (\Throwable $e) {
             throw $e;
        }
    }

    public function delete($data_req) {
        try {
            $id = $data_req['data_sent'];

            DB::table('uxui_jenis_cuti')
                ->where('id', '=', $id)
                ->delete();

            return true;
        } catch (\Throwable $e) {
            throw $e;
        }
    }

     public function getListJenisCuti($search = null)
    {
        $query = DB::table('uxui_jenis_cuti')
            ->select('id', 'nama')
            ->where('status', 1); 

        if (!empty($search)) {
            $query->where('nama', 'like', '%' . $search . '%');
        }

        return $query
            ->orderBy('nama', 'asc')
            ->limit(10)
            ->get()
            ->map(function ($item) {
                return [
                    'id'   => $item->id,
                    'text' => $item->nama
                ];
            })
            ->toArray();
    }
    
    public function getListJenisCutiKaryawan($params = [], $type = '', $id_karyawan)
    {
        $query = DB::table('uxui_jenis_cuti as jc')
            ->leftJoin('uxui_stok_cuti as sc', function ($join) use ($id_karyawan) {
                $join->on('jc.id', '=', 'sc.id_jenis_cuti')
                    ->where('sc.id_karyawan', '=', $id_karyawan);
            })
            ->select(
                'jc.id',
                'jc.nama',
                DB::raw('COALESCE(sc.sisa, 0) as sisa')
            );

        if (!empty($params['search'])) {
            $query->where(function ($q) use ($params) {
                $q->where('jc.nama', 'like', '%'.$params['search'].'%')
                ->orWhere('sc.sisa', 'like', '%'.$params['search'].'%');
            });
        }

        if (empty($type)) {
            return $query->get();
        } else {
            return $query;
        }
    }

}