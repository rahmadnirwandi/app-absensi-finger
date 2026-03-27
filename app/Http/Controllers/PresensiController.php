<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Services\PresensiService;

class PresensiController extends Controller
{
    protected $presensiService;

    public function __construct(PresensiService $presensiService)
    {
        $this->presensiService = $presensiService;
    }

    public function index(Request $request)
    {
        $user_auth = (new \App\Http\Traits\AuthFunction)->getUser();
        $id_karyawan = $user_auth->data_user_sistem->id_karyawan;

        $bulanan = $this->presensiService->getPresensiBulanan($id_karyawan);
        $log = $this->presensiService->getLogTerakhir($id_karyawan);
        $hariIni = $this->presensiService->getStatusHariIni($id_karyawan);

        return view('presensi.index', compact('bulanan', 'log', 'hariIni'));
    }
}
