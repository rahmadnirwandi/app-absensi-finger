<?php

namespace App\Services;

use App\Services\BaseService;
use Illuminate\Support\Facades\DB;
use App\Services\MasterPengajuanService;
use App\Models\PengajuanIzin;


class IzinService extends BaseService {
    protected $masterPengajuanService;
    public $pengajuanIzin;

    public function __construct() {
        $this->masterPengajuanService = new MasterPengajuanService;
        $this->pengajuanIzin  = new PengajuanIzin;
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
        $query = $this->pengajuanIzin
            ->from('pengajuan_izin as pi')
            ->select(
                'pi.id_karyawan',
                'pi.id',
                'pi.keterangan',
                'pi.jenis_pengajuan',
                'pi.tgl_mulai',
                'pi.tgl_selesai',
                'pi.status',
                'pi.current_level',
                'pi.file_pendukung',
                'pi.created_at',
                'k.nip',
                'k.nm_karyawan'
            )
            ->leftJoin('ref_karyawan as k', 'k.id_karyawan', '=', 'pi.id_karyawan')
            ->orderBy('pi.created_at', 'desc');

        
        if (empty($params['is_super_admin']) || $params['is_super_admin'] === false) {
            $query->where('pi.id_ruangan', $params['id_ruangan'])
                    ->where('pi.status', 'pending');
        }

        
        if (!empty($params['search'])) {
            $query->where(function ($q) use ($params) {
                $q->where('k.nm_karyawan', 'like', '%'.$params['search'].'%')
                ->orWhere('k.nip', 'like', '%'.$params['search'].'%');
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

            if (!empty($data_req['file_pendukung'])) {

                $nip = $data_req['nip'];
                $file = $data_req['file_pendukung'];

                $directory = public_path('upload/izin/' . $nip);

                if (!file_exists($directory)) {
                    mkdir($directory, 0755, true);
                }

                $filename = 'izin_' . date('Ymd_His') . '_' . uniqid() . '.' . $file->getClientOriginalExtension();

                $file->move($directory, $filename);

                $filePath = 'upload/izin/' . $nip . '/' . $filename;

            }

            DB::table('pengajuan_izin')->insertGetId([
                'id_karyawan'   => $id_karyawan,
                'id_ruangan'   => $id_ruangan,
                'keterangan'    => $data_req['keterangan'],
                'jenis_pengajuan' => $data_req['jenis_izin'],
                'tgl_mulai'     => $data_req['tgl_mulai'],
                'tgl_selesai'   => $data_req['tgl_selesai'],
                'file_pendukung'=> $filePath ?? null,
                'current_level' => 2,
                'status'        => 'pending',
                'created_at'    => now(),
                'updated_at'    => now(),
            ]);

            $mapping = $this->masterPengajuanService->mappingApproved('izin', $id_ruangan);

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

    public function getPengajuan($id_pengajuan) {
        return DB::table('pengajuan_izin as pi')
                ->select([
                    'pi.id',
                    'pi.id_karyawan',
                    'pi.id_ruangan',
                    'pi.keterangan',
                    'pi.jenis_pengajuan',
                    'pi.tgl_selesai',
                    'pi.tgl_mulai',
                    'pi.file_pendukung',
                    'rk.nm_karyawan',
                    'rk.nip',
                    'rk.id_jabatan',
                ])
                ->leftJoin('ref_karyawan as rk', 'pi.id_karyawan', '=', 'rk.id_karyawan')
                ->where('pi.id', '=', $id_pengajuan)
                ->first();
    }

    public function update($data_req)
    {
        DB::beginTransaction();

        try {

            $izin = DB::table('pengajuan_izin')
                ->where('id', $data_req['id'])
                ->first();

            if (!$izin) {
                throw new \Exception('Data izin tidak ditemukan');
            }

            $updateData = [
                'keterangan'      => $data_req['keterangan'],
                'jenis_pengajuan' => $data_req['jenis_izin'],
                'tgl_mulai'       => $data_req['tgl_mulai'],
                'tgl_selesai'     => $data_req['tgl_selesai'],
                'updated_at'      => now(),
            ];

            if (!empty($data_req['file_pendukung'])) {

                $nip  = $data_req['nip'];
                $file = $data_req['file_pendukung'];

                $directory = public_path('upload/izin/' . $nip);

                if (!file_exists($directory)) {
                    mkdir($directory, 0755, true);
                }

                if (!empty($izin->file_pendukung)) {
                    $oldFile = public_path($izin->file_pendukung);
                    if (file_exists($oldFile)) {
                        unlink($oldFile);
                    }
                }

                $filename = 'izin_' . date('Ymd_His') . '_' . uniqid() . '.' .
                            $file->getClientOriginalExtension();

                $file->move($directory, $filename);

                $updateData['file_pendukung'] = 'upload/izin/' . $nip . '/' . $filename;
            }

            DB::table('pengajuan_izin')
                ->where('id', $data_req['id_pengajuan'])
                ->update($updateData);

            DB::commit();
            return true;

        } catch (\Throwable $e) {
            DB::rollBack();
            throw $e;
        }
    }

    public function delete($data_req)
    {
        DB::beginTransaction();

        try {

            $id_pengajuan = $data_req['data_sent'];

            $izin = DB::table('pengajuan_izin')
                ->where('id', $id_pengajuan)
                ->first();

            if (!$izin) {
                throw new \Exception('Data izin tidak ditemukan');
            }

            if (!empty($izin->file_pendukung)) {
                $filePath = public_path($izin->file_pendukung);
                if (file_exists($filePath)) {
                    unlink($filePath);
                }
            }

            DB::table('Pengajuan_izin')
                ->where('id', $id_pengajuan)
                ->delete();

            DB::commit();
            return true;

        } catch (\Throwable $e) {
            DB::rollBack();
            throw $e;
        }
    }

    public function getListDataApprove($params = [])
    {
        $query = $this->pengajuanIzin
            ->from('pengajuan_izin as pi')
            ->select(
                'pi.id_karyawan',
                'pi.id',
                'pi.keterangan',
                'pi.jenis_pengajuan',
                'pi.tgl_mulai',
                'pi.tgl_selesai',
                'pi.status',
                'pi.current_level',
                'pi.file_pendukung',
                'pi.created_at',
                'k.nip',
                'k.nm_karyawan'
            )
            ->leftJoin('ref_karyawan as k', 'k.id_karyawan', '=', 'pi.id_karyawan')
            ->where('pi.status', 'approved') 
            ->orderBy('pi.created_at', 'desc');

        
        if (empty($params['is_super_admin']) || $params['is_super_admin'] === false) {
            $query->where('pi.id_ruangan', $params['id_ruangan']);
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
        $query = $this->pengajuanIzin
            ->from('pengajuan_izin as pi')
            ->select(
                'pi.id_karyawan',
                'pi.id',
                'pi.keterangan',
                'pi.jenis_pengajuan',
                'pi.tgl_mulai',
                'pi.tgl_selesai',
                'pi.status',
                'pi.current_level',
                'pi.file_pendukung',
                'pi.created_at',
                'k.nip',
                'k.nm_karyawan'
            )
            ->leftJoin('ref_karyawan as k', 'k.id_karyawan', '=', 'pi.id_karyawan')
            ->where('pi.status', 'rejected') 
            ->orderBy('pi.created_at', 'desc');

        
        if (empty($params['is_super_admin']) || $params['is_super_admin'] === false) {
            $query->where('pi.id_ruangan', $params['id_ruangan']);
        }

        if (!empty($params['search'])) {
            $query->where(function ($q) use ($params) {
                $q->where('k.nm_karyawan', 'like', '%'.$params['search'].'%')
                ->orWhere('k.nip', 'like', '%'.$params['search'].'%');
            });
        }

        return $query;
    }
}