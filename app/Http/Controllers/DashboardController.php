<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Services\DashboardService;



class DashboardController extends Controller
{
    protected $dashboardService;

    public function __construct(DashboardService $dashboardService)
    {
        $this->dashboardService = $dashboardService;
    }

    public function index(Request $request)
    {
        if ((new \App\Http\Traits\AuthFunction)->checkAkses('/dashboard')) {

            $summary       = $this->dashboardService->getSummary();

            $dataBulanan   = $this->dashboardService->getDataBulanan();

            $dataMingguan  = $this->dashboardService->getDataMingguan();

            return view('dashboard.index', array_merge(
                $summary,
                [
                    'dataBulanan'  => $dataBulanan,
                    'dataMingguan' => $dataMingguan,
                ]
            ));

        } else {
            return redirect('/presensi');
        }
    }
}