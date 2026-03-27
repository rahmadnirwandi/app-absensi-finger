<?php

namespace App\Services;

use App\Services\BaseService;
use Illuminate\Support\Facades\DB;
use App\Services\MasterPengajuanService;
use Carbon\Carbon;


class LemburService extends BaseService {
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
            ->leftJoin('ref_karyawan as k', 'k.id_karyawan', '=', 'l.id_karyawan')
            ->orderBy('l.created_at', 'desc');

        
        if (empty($params['is_super_admin']) || $params['is_super_admin'] === false) {
            $query->where('l.id_ruangan', $params['id_ruangan'])
                    ->where('l.status', 'pending');
        }

        
        if (!empty($params['search'])) {
            $query->where(function ($q) use ($params) {
                $q->where('k.nm_karyawan', 'like', '%'.$params['search'].'%')
                ->orWhere('k.nip', 'like', '%'.$params['search'].'%')
                ->orWhere('l.jenis_lembur', 'like', '%'.$params['search'].'%');
            });
        }

        return $query;
    }


    public function insert($data_req)
    {
        return DB::transaction(function () use ($data_req) {

            $id_karyawan = $data_req['id_karyawan']; 
            $karyawan = $this->getKaryawan($id_karyawan);
            $id_ruangan = $karyawan->id_ruangan;
            $filePath = null;

            $jam_mulai   = Carbon::parse($data_req['jam_mulai']);
            $jam_selesai = Carbon::parse($data_req['jam_selesai']);

            if ($jam_selesai->lessThanOrEqualTo($jam_mulai)) {
                $jam_selesai->addDay();
            }

            $total_menit = $jam_mulai->diffInMinutes($jam_selesai);
            $total_jam = intdiv($total_menit, 60);

            if (($total_menit % 60) >= 30) {
                $total_jam++;
            }

            if (!empty($data_req['file_dokumen'])) {

                $nip = $data_req['nip'];
                $file = $data_req['file_dokumen'];

                $directory = public_path('upload/lembur/' . $nip);

                if (!file_exists($directory)) {
                    mkdir($directory, 0755, true);
                }

                $filename = 'lembur_' . date('Ymd_His') . '_' . uniqid() . '.' . $file->getClientOriginalExtension();

                $file->move($directory, $filename);

                $filePath = 'upload/lembur/' . $nip . '/' . $filename;
            }

            DB::table('uxui_lembur')->insertGetId([
                'id_karyawan'   => $id_karyawan,
                'id_ruangan'   => $id_ruangan,
                'keterangan'    => $data_req['keterangan'],
                'tgl_pengajuan' => now()->toDateString(),
                'tgl_lembur' => $data_req['tgl_lembur'],
                'jenis_lembur' => $data_req['jenis_lembur'],
                'jam_mulai'     => $jam_mulai,
                'jam_selesai'   => $jam_selesai,
                'total_jam'   => $total_jam,
                'rupiah_per_jam'   => 0.00,
                'total_bayar'   => 0.00,
                'redeemed'   => 0,
                'deposit_jam'   => $data_req['jenis_lembur'] == 'Deposit' ? $total_jam : 0,
                'file_dokumen'=> $filePath ?? null,
                'current_level' => 2,
                'status'        => 'pending',
                'created_at'    => now(),
                'updated_at'    => now(),
            ]);

            $mapping = $this->masterPengajuanService->mappingApproved('lembur', $id_ruangan);

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

    public function getLembur($id_lembur)
    {
        return $query = DB::table('uxui_lembur as l')
            ->select(
                'l.id_karyawan',
                'l.id',
                'l.keterangan',
                'l.tgl_pengajuan',
                'l.jam_mulai',
                'l.tgl_lembur',
                'l.jam_selesai',
                'l.file_dokumen',
                'l.jenis_lembur',
                'l.id_ruangan',
                'l.rupiah_per_jam',
                'l.total_jam',
                'l.total_bayar',
                'l.deposit_jam',
                'l.redeemed',
                'l.status',
                'l.current_level',
                'l.created_at',
                'k.nip',
                'k.nm_karyawan',
                'k.id_departemen',
                'k.id_jabatan'
            )
            ->leftJoin('ref_karyawan as k', 'k.id_karyawan', '=', 'l.id_karyawan')
            ->where('l.id', '=', $id_lembur)
            ->first();
            
    }

    public function update($data_req)
    {
        return DB::transaction(function () use ($data_req) {

            $id_lembur   = $data_req['id'];
            $jam_mulai   = Carbon::parse($data_req['jam_mulai']);
            $jam_selesai = Carbon::parse($data_req['jam_selesai']);

            $lembur = DB::table('uxui_lembur')
                ->where('id', $id_lembur)
                ->lockForUpdate()
                ->first();

            if (!$lembur) {
                throw new \Exception('Data lembur tidak ditemukan');
            }

            if ($jam_selesai->lessThanOrEqualTo($jam_mulai)) {
                $jam_selesai->addDay();
            }

            $total_menit = $jam_mulai->diffInMinutes($jam_selesai);
            $total_jam   = intdiv($total_menit, 60);

            if (($total_menit % 60) >= 30) {
                $total_jam++;
            }

            if ($total_jam <= 0) {
                throw new \Exception('Durasi lembur tidak valid');
            }

            $update = [
                'keterangan'   => $data_req['keterangan'],
                'tgl_lembur'   => $data_req['tgl_lembur'],
                'jenis_lembur' => $data_req['jenis_lembur'],
                'jam_mulai'    => $jam_mulai->format('H:i'),
                'jam_selesai'  => $jam_selesai->format('H:i'),
                'total_jam'    => $total_jam,
                'deposit_jam'  => $data_req['jenis_lembur'] === 'Deposit' ? $total_jam : 0,
                'updated_at'   => now(),
            ];

            
            if (!empty($data_req['file_dokumen'])) {

                $nip  = $data_req['nip'];
                $file = $data_req['file_dokumen'];

                $directory = public_path('upload/lembur/' . $nip);
                if (!file_exists($directory)) {
                    mkdir($directory, 0755, true);
                }

                if (!empty($lembur->file_dokumen)) {
                    $oldFile = public_path($lembur->file_dokumen);
                    if (file_exists($oldFile)) {
                        unlink($oldFile);
                    }
                }

                $filename = 'lembur_' . now()->format('Ymd_His') . '_' . uniqid() . '.' . $file->getClientOriginalExtension();
                $file->move($directory, $filename);

                $update['file_dokumen'] = 'upload/lembur/' . $nip . '/' . $filename;
            }

            DB::table('uxui_lembur')
                ->where('id', $id_lembur)
                ->update($update);

            return true;
        });
    }


    public function delete($data_req) {
        try {
            $id = $data_req['data_sent'];

            DB::table('uxui_lembur')
                ->where('id', '=', $id)
                ->delete();

            return true;
        } catch (\Throwable $e) {
            throw $e;
        }
    }

    public function getListDataApprove($params = [])
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
            ->leftJoin('ref_karyawan as k', 'k.id_karyawan', '=', 'l.id_karyawan')
            ->where('l.status', '=', 'approved')
            ->orderBy('l.created_at', 'desc');

        
        if (empty($params['is_super_admin']) || $params['is_super_admin'] === false) {
            $query->where('l.id_ruangan', $params['id_ruangan']);
        }
        
        if (!empty($params['search'])) {
            $query->where(function ($q) use ($params) {
                $q->where('k.nm_karyawan', 'like', '%'.$params['search'].'%')
                ->orWhere('k.nip', 'like', '%'.$params['search'].'%')
                ->orWhere('l.jenis_lembur', 'like', '%'.$params['search'].'%');
            });
        }

        return $query;
    }

    public function getListDataReject($params = [])
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
            ->leftJoin('ref_karyawan as k', 'k.id_karyawan', '=', 'l.id_karyawan')
            ->where('l.status', '=', 'rejected')
            ->orderBy('l.created_at', 'desc');

        
        if (empty($params['is_super_admin']) || $params['is_super_admin'] === false) {
            $query->where('l.id_ruangan', $params['id_ruangan']);
        }

        
        if (!empty($params['search'])) {
            $query->where(function ($q) use ($params) {
                $q->where('k.nm_karyawan', 'like', '%'.$params['search'].'%')
                ->orWhere('k.nip', 'like', '%'.$params['search'].'%')
                ->orWhere('l.jenis_lembur', 'like', '%'.$params['search'].'%');
            });
        }

        return $query;
    }
}