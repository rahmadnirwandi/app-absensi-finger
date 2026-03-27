<?php

namespace App\Services;

use App\Models\PerjalananDinas;
use Illuminate\Support\Facades\DB;

class PerjalananDinasService extends BaseService
{
    public $perjalananDinas='';

    public function __construct(){
        parent::__construct();
        $this->perjalananDinas = new PerjalananDinas;
    }

    function getList($params=[],$type=''){
        $query = $this->perjalananDinas
            ->select('id_spd','ref_perjalanan_dinas.id_karyawan','ref_karyawan.nip','ref_karyawan.nm_karyawan','nm_jabatan','nm_departemen','nm_ruangan','ref_perjalanan_dinas.jenis_dinas','ref_perjalanan_dinas.tgl_mulai','ref_perjalanan_dinas.tgl_selesai','ref_perjalanan_dinas.jumlah','ref_perjalanan_dinas.uraian')
            ->Leftjoin('ref_karyawan','ref_perjalanan_dinas.id_karyawan','=','ref_karyawan.id_karyawan')
            ->Leftjoin('ref_jabatan','ref_jabatan.id_jabatan','=','ref_karyawan.id_jabatan')
            ->Leftjoin('ref_departemen','ref_departemen.id_departemen','=','ref_karyawan.id_departemen')
            ->Leftjoin('ref_ruangan','ref_ruangan.id_ruangan','=','ref_karyawan.id_ruangan')
            ->orderBy('nm_karyawan','ASC')
        ;

        $list_search=[
            'where_or'=>['ref_karyawan.nm_karyawan','ref_karyawan.alamat','nip','nm_jabatan','nm_departemen'],
        ];

        if($params){
            $query=(new \App\Models\MyModel)->set_where($query,$params,$list_search);
        }
        if(empty($type)){
            return $query->get();
        }else{
            return $query;
        }
    }

   public function getDataPerjalanDinas($params = [], $type)
    {
        ini_set("memory_limit", "800M");
        DB::statement("SET SESSION group_concat_max_len = 15000;");

        $tgl_awal = !empty($params['tanggal'][0]) ? $params['tanggal'][0] : date('Y-m-d');
        $tgl_akhir = !empty($params['tanggal'][1]) ? $params['tanggal'][1] : date('Y-m-d');

        $search = !empty($params['search']) ? $params['search'] : '';

        unset($params['tanggal']);
        unset($params['search']);

        // Step 1: Get raw data perjalanan dinas
        $query = DB::table('ref_perjalanan_dinas as utama')
            ->join('ref_karyawan as rk', 'rk.id_karyawan', '=', 'utama.id_karyawan')
            ->leftJoin('ref_departemen as rd', 'rd.id_departemen', '=', 'rk.id_departemen')
            ->leftJoin('ref_ruangan as rr', 'rr.id_ruangan', '=', 'rk.id_ruangan')
            ->where(function ($q) use ($tgl_awal, $tgl_akhir) {
                $q->whereBetween('tgl_mulai', [$tgl_awal, $tgl_akhir])
                ->orWhereBetween('tgl_selesai', [$tgl_awal, $tgl_akhir]);
            })
            ->select([
                'utama.id_karyawan',
                'rk.nm_karyawan',
                'rk.id_departemen',
                'rd.nm_departemen',
                'rk.id_ruangan',
                'rr.nm_ruangan',
                'utama.uraian',
                'utama.tgl_mulai',
                'utama.tgl_selesai',
                DB::raw('IF(tgl_selesai >= tgl_mulai, DATEDIFF(tgl_selesai, tgl_mulai), 0) as jml'),
                'utama.jenis_dinas',
            ])
            ->orderBy('utama.id_karyawan')
            ->orderBy('utama.tgl_mulai');

        // Apply search filter
        if (!empty($search)) {
            $query->where('rk.nm_karyawan', 'like', '%' . $search . '%');
        }

        // Jika butuh query builder
        if (!empty($type)) {
            if ($params) {
                $list_search = [];
                $query = (new \App\Models\MyModel)->set_where($query, $params, $list_search);
            }
            return $query;
        }

        // Step 2: Get data dan process di PHP
        $rawData = $query->get();

        // Step 3: Group by id_karyawan dan build JSON structure
        $hasil = [];

        $groupedByKaryawan = $rawData->groupBy('id_karyawan');

        foreach ($groupedByKaryawan as $id_karyawan => $karyawanData) {
            $first = $karyawanData->first();

            // Build waktu array
            $waktu = [];
            foreach ($karyawanData as $row) {
                $waktu[] = [
                    $row->tgl_mulai,
                    $row->tgl_selesai,
                    (int)$row->jml,
                    $row->uraian,
                    $row->jenis_dinas,
                ];
            }

            $hasil[$id_karyawan] = [
                'waktu'       => $waktu,
                'nm_karyawan' => $first->nm_karyawan,
            ];
        }

        // Step 4: Return dalam format yang sama dengan query lama
        $result = (object) [
            'hasil' => json_encode($hasil)
        ];

        return collect([$result]);
    }
}