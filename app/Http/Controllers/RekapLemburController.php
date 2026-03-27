<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Services\GlobalService;
use App\Services\RefKaryawanService;
use App\Services\RekapLemburService;

class   RekapLemburController extends Controller
{
    public $part_view, $url_index, $url_name, $title, $breadcrumbs, $globalService, $rekapLemburService;
    protected $masterPengajuanService;
    public function __construct()
    {
        $router_name = (new \App\Http\Traits\GlobalFunction)->getRouterIndex();
        $this->part_view = $router_name->path_base;
        $this->url_index = $router_name->uri;
        $this->url_name = $router_name->router_name;
        $this->rekapLemburService = new RekapLemburService;

        $this->title = 'Rekap Lembur';
        $this->breadcrumbs = [
            ['title' => 'Manajemen Absensi', 'url' => url('/') . "/sub-menu?type=6"],
            ['title' => $this->title, 'url' => url('/') . "/" . $this->url_index],
        ];

        $this->globalService = new GlobalService;
        $this->refKaryawanService = new RefKaryawanService;
    }

    function actionIndex(Request $request){
        $user_auth = (new \App\Http\Traits\AuthFunction)->getUser();
        $role = $user_auth->group_user[0] ?? null;

        $form_filter_text = $request->form_filter_text ?? '';
        $filter_nm_departemen = $request->filter_nm_departemen ?? '';
        $filter_nm_ruangan = $request->filter_nm_ruangan ?? '';
        $form_filter_start = $request->form_filter_start ?? date('Y-m-d');
        $form_filter_end = $request->form_filter_end ?? date('Y-m-d');

        $paramater = [
            'search' => $form_filter_text,
            'filter_nm_departemen' => $filter_nm_departemen,
            'filter_nm_ruangan' => $filter_nm_ruangan,
            'form_filter_start' => $form_filter_start,
            'form_filter_end' => $form_filter_end,
        ];

        $list_data = $this->rekapLemburService
            ->getRekapLembur($paramater)
            ->paginate($request->per_page ?? 10);
    
        $parameter_view = [
            'title' => $this->title,
            'breadcrumbs' => $this->breadcrumbs,
            'list_data' => $list_data
        ];

        return view('rekap-lembur.index', $parameter_view);
    }


}
