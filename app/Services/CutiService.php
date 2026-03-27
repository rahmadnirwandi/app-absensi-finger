<?php

namespace App\Services;

use App\Services\BaseService;
use Illuminate\Support\Facades\DB;
use App\Services\MasterPengajuanService;
use Carbon\Carbon;


class CutiService extends BaseService {
    protected $masterPengajuanService;

    public function __construct() {
        $this->masterPengajuanService = new MasterPengajuanService;
    }
    
    public function getKaryawan($id_karyawan) {
        $query = DB::table('ref_karyawan as rf')
                ->select([
                    'rf.id_karyawan',
                    'rf.nm_karyawan',
                    'rf.nip',
                    'rf.id_jabatan',
                    'rj.nm_jabatan',
                    'rf.id_departemen',
                    'rd.nm_departemen',
                    'rf.id_ruangan',
                    'rr.nm_ruangan'
                ])
                ->leftJoin('ref_jabatan as rj', 'rf.id_jabatan', '=', 'rj.id_jabatan')
                ->leftJoin('ref_departemen as rd', 'rf.id_departemen', '=', 'rd.id_departemen')
                ->leftJoin('ref_ruangan as rr', 'rf.id_ruangan', '=', 'rr.id_ruangan')
                ->where('rf.id_karyawan', '=', $id_karyawan)
                ->first();

        return $query;
    }

    public function getListData($params = [])
    {
        $query = DB::table('uxui_cuti as c')
            ->select(
                'c.id_karyawan',
                'c.id',
                'c.keterangan',
                'c.tgl_pengajuan',
                'c.tgl_mulai',
                'c.tgl_selesai',
                'c.jumlah_hari',
                'c.sisa_cuti',
                'c.status',
                'c.current_level',
                'jc.nama as nm_jenis_cuti',
                'c.created_at',
                'k.nip',
                'k.nm_karyawan'
            )
            ->leftJoin('ref_karyawan as k', 'k.id_karyawan', '=', 'c.id_karyawan')
            ->leftJoin('uxui_jenis_cuti as jc', 'c.id_jenis_cuti', '=', 'jc.id')
            ->orderBy('c.created_at', 'desc');

        
        if (empty($params['is_super_admin']) || $params['is_super_admin'] === false) {
            $query->where('c.id_karyawan', $params['id_karyawan'])
                ->where('c.status', 'pending');
        }

        
        if (!empty($params['search'])) {
            $query->where(function ($q) use ($params) {
                $q->where('k.nm_karyawan', 'like', '%'.$params['search'].'%')
                ->orWhere('k.nip', 'like', '%'.$params['search'].'%')
                ->orWhere('jc.nama', 'like', '%'.$params['search'].'%');
            });
        }

        return $query;
    }


    public function insert($data_req)
    {
        return DB::transaction(function () use ($data_req) {

            $id_karyawan = $data_req['key_old']; 
            $id_ruangan = $data_req['id_ruangan'];
            $id_jenis_cuti = $data_req['id_jenis_cuti'];
            $tgl_mulai   = Carbon::parse($data_req['tgl_mulai']);
            $tgl_selesai = Carbon::parse($data_req['tgl_selesai']);

            $jumlah_hari = $tgl_mulai->diffInDays($tgl_selesai) + 1;

            $check_kepala_bagian = $this->checkKepalaRuangan($data_req['id_jabatan']);
            $check_ruangan = $this->checkRuangan($data_req['id_ruangan']);

            $stok_cuti = $this->checkStokCuti($id_karyawan, $id_jenis_cuti);

            if (!$stok_cuti) {
                throw new \Exception('Stok cuti '. $data_req['nama_jenis_cuti']. ' belum tersedia');
            }

            if ($stok_cuti->sisa < $jumlah_hari) {
                throw new \Exception('Sisa cuti tidak mencukupi');
            }

            $sisa_cuti = $stok_cuti->sisa - $jumlah_hari;

            DB::table('uxui_cuti')->insertGetId([
                'id_karyawan'   => $id_karyawan,
                'id_ruangan'    => $id_ruangan,
                'id_jenis_cuti' => $id_jenis_cuti,

                'tgl_pengajuan' => today(),
                'tgl_mulai'     => $tgl_mulai->toDateString(),
                'tgl_selesai'   => $tgl_selesai->toDateString(),

                'jumlah_hari'   => $jumlah_hari,
                'sisa_cuti'     => $sisa_cuti,

                'keterangan'    => $data_req['keterangan'],
                'current_level' => $check_kepala_bagian || $check_ruangan ? 2 : 1,
                'status'        => 'pending',

                'created_at'    => now(),
                'updated_at'    => now(),
            ]);


            $mapping = $this->masterPengajuanService->mappingApproved('cuti', $id_ruangan);

            if (!$mapping) {
                throw new \Exception('Approval mapping level 1 tidak ditemukan');
            }

            $approver = $this->masterPengajuanService->getApproved($mapping);

            if (!$approver) {
                throw new \Exception('Kepala ruangan tidak ditemukan');
            }

            return true;
        });
    }

    public function checkKepalaRuangan($id_jabatan) {
        $query = DB::table('approval_mappings')
            ->where('approver_value', $id_jabatan)
            ->exists();

        if($query) {
            return true;
        }

        return false;
    }

    public function checkRuangan($id_ruangan) {
        $query = DB::table('ref_ruangan')
            ->select('nm_ruangan')
            ->where('id_ruangan', $id_ruangan)
            ->first();

        if($query->nm_ruangan == 'SDI') {
            return true;
        }

        return false;
    }

    public function checkStokCuti($id_karyawan, $id_jenis_cuti) {
        return DB::table('uxui_stok_cuti')
            ->select('id', 'jumlah', 'pakai', 'sisa')
            ->where('id_karyawan', $id_karyawan)
            ->where('id_jenis_cuti', $id_jenis_cuti)
            ->first();
    }

    public function getCuti($id_cuti)
    {
        return DB::table('uxui_cuti as c')
            ->select(
                'c.id_karyawan',
                'c.id',
                'c.keterangan',
                'c.tgl_pengajuan',
                'c.tgl_mulai',
                'c.tgl_selesai',
                'c.jumlah_hari',
                'c.sisa_cuti',
                'c.id_jenis_cuti',
                'c.status',
                'c.current_level',
                'jc.nama as nm_jenis_cuti',
                'c.created_at',
                'k.nip',
                'k.nm_karyawan'
            )
            ->leftJoin('ref_karyawan as k', 'k.id_karyawan', '=', 'c.id_karyawan')
            ->leftJoin('uxui_jenis_cuti as jc', 'c.id_jenis_cuti', '=', 'jc.id')
            ->where('c.id', '=', $id_cuti)
            ->first();
            
    }

    public function update($data_req)
    {
        return DB::transaction(function () use ($data_req) {
            
            $id_cuti = $data_req['id']; 
            $id_karyawan = $data_req['id_karyawan']; 
            $id_jenis_cuti = $data_req['id_jenis_cuti'];
            $tgl_mulai   = Carbon::parse($data_req['tgl_mulai']);
            $tgl_selesai = Carbon::parse($data_req['tgl_selesai']);

            $jumlah_hari = $tgl_mulai->diffInDays($tgl_selesai) + 1;

            $stok_cuti = $this->checkStokCuti($id_karyawan, $id_jenis_cuti);

            if ($stok_cuti->sisa < $jumlah_hari) {
                throw new \Exception('Sisa cuti tidak mencukupi');
            }

            $sisa_cuti = $stok_cuti->sisa - $jumlah_hari;

            DB::table('uxui_cuti')
            ->where('id', $id_cuti)
            ->update([
                'tgl_mulai'   => $tgl_mulai->toDateString(),
                'tgl_selesai' => $tgl_selesai->toDateString(),
                'jumlah_hari' => $jumlah_hari,
                'sisa_cuti'   => $sisa_cuti,
                'keterangan'  => $data_req['keterangan'],
                'updated_at'  => now(),
            ]);

            return true;
        });
    }

    public function delete($data_req) {
        try {
            $id = $data_req['data_sent'];

            DB::table('uxui_cuti')
                ->where('id', '=', $id)
                ->delete();

            return true;
        } catch (\Throwable $e) {
            throw $e;
        }
    }

    public function getListDataApprove($params = [])
    {
        $query = DB::table('uxui_cuti as c')
            ->select(
                'c.id_karyawan',
                'c.id',
                'c.keterangan',
                'c.tgl_pengajuan',
                'c.tgl_mulai',
                'c.tgl_selesai',
                'c.jumlah_hari',
                'c.sisa_cuti',
                'c.status',
                'c.current_level',
                'jc.nama as nm_jenis_cuti',
                'c.created_at',
                'k.nip',
                'k.nm_karyawan'
            )
            ->leftJoin('ref_karyawan as k', 'k.id_karyawan', '=', 'c.id_karyawan')
            ->leftJoin('uxui_jenis_cuti as jc', 'c.id_jenis_cuti', '=', 'jc.id')
            ->where('c.status', '=', 'approved')
            ->orderBy('c.created_at', 'desc');

        
        if (empty($params['is_super_admin']) || $params['is_super_admin'] === false) {
            $query->where('c.id_karyawan', $params['id_karyawan']);
        }

        
        if (!empty($params['search'])) {
            $query->where(function ($q) use ($params) {
                $q->where('k.nm_karyawan', 'like', '%'.$params['search'].'%')
                ->orWhere('k.nip', 'like', '%'.$params['search'].'%');
            });
        }

        return $query;
    }

    public function getListDataReject($params = [])
    {
        $query = DB::table('uxui_cuti as c')
            ->select(
                'c.id_karyawan',
                'c.id',
                'c.keterangan',
                'c.tgl_pengajuan',
                'c.tgl_mulai',
                'c.tgl_selesai',
                'c.jumlah_hari',
                'c.sisa_cuti',
                'c.status',
                'c.current_level',
                'jc.nama as nm_jenis_cuti',
                'c.created_at',
                'k.nip',
                'k.nm_karyawan'
            )
            ->leftJoin('ref_karyawan as k', 'k.id_karyawan', '=', 'c.id_karyawan')
            ->leftJoin('uxui_jenis_cuti as jc', 'c.id_jenis_cuti', '=', 'jc.id')
            ->where('c.status', '=', 'rejected')
            ->orderBy('c.created_at', 'desc');

        
        if (empty($params['is_super_admin']) || $params['is_super_admin'] === false) {
            $query->where('c.id_karyawan', $params['id_karyawan']);
        }

        
        if (!empty($params['search'])) {
            $query->where(function ($q) use ($params) {
                $q->where('k.nm_karyawan', 'like', '%'.$params['search'].'%')
                ->orWhere('k.nip', 'like', '%'.$params['search'].'%');
            });
        }

        return $query;
    }

    public function getCutiApprovedByRange($params = [])
    {
        $tgl_awal  = $params['tanggal'][0] ?? date('Y-m-01');
        $tgl_akhir = $params['tanggal'][1] ?? date('Y-m-t');

        $query = DB::table('uxui_cuti as c')
            ->select(
                'c.id_karyawan',
                'c.tgl_mulai',
                'c.tgl_selesai',
                'c.jumlah_hari',
                'c.keterangan',
                'k.nm_karyawan',
                'jc.nama as nm_jenis_cuti',
            )
            ->leftJoin('ref_karyawan as k', 'k.id_karyawan', '=', 'c.id_karyawan')
            ->leftJoin('uxui_jenis_cuti as jc', 'c.id_jenis_cuti', '=', 'jc.id')

            ->where('c.status', 'APPROVED')

            ->where(function ($q) use ($tgl_awal, $tgl_akhir) {
                $q->where('c.tgl_mulai', '<=', $tgl_akhir)
                    ->where('c.tgl_selesai', '>=', $tgl_awal);
            });

        $data = $query->get()->groupBy('id_karyawan');

        $result = [];

        foreach ($data as $id => $rows) {

            $first = $rows->first();

            $result[$id] = [
                'nm_karyawan' => $first->nm_karyawan,
                'waktu' => $rows->map(function ($r) {
                    return [
                        $r->tgl_mulai,
                        $r->tgl_selesai,
                        $r->jumlah_hari,
                        $r->nm_jenis_cuti,
                    ];
                })->values()
            ];
        }

        return $result;
    }

    public function getDataCutiApproved($params = [])
    {
        $tgl_awal  = $params['tanggal'][0] ?? date('Y-m-d');
        $tgl_akhir = $params['tanggal'][1] ?? date('Y-m-d');

        $query = DB::table('uxui_cuti as c')
            ->select(
                'c.id_karyawan',
                'c.tgl_mulai',
                'c.tgl_selesai',
                'jc.nama as nm_jenis_cuti'
            )
            ->leftJoin('uxui_jenis_cuti as jc', 'c.id_jenis_cuti', '=', 'jc.id')
            ->where('c.status', 'APPROVED') // penting
            ->where(function ($q) use ($tgl_awal, $tgl_akhir) {
                $q->whereBetween('c.tgl_mulai', [$tgl_awal, $tgl_akhir])
                    ->orWhereBetween('c.tgl_selesai', [$tgl_awal, $tgl_akhir]);
            });

        $data = $query->get();

        $result = [];

        foreach ($data as $row) {

            $start = new \DateTime($row->tgl_mulai);
            $end   = new \DateTime($row->tgl_selesai);

            while ($start <= $end) {

                $tgl = $start->format('Y-m-d');

                $result[$row->id_karyawan][$tgl] = $row->nm_jenis_cuti;

                $start->modify('+1 day');
            }
        }

        return $result;
    }
}