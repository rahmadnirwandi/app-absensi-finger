<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Services\GlobalService;
use App\Services\DataLaporanPresensiService;
use App\Services\KalenderKerjaKaryawanService;
use DateTime;

class KalenderKerjaKaryawanController extends Controller
{
    public $part_view, $url_index, $url_name, $title, $breadcrumbs, $globalService, $kalenderKerjaKaryawanService;
    public $dataLaporanPresensiService;

    public function __construct()
    {
        $router_name = (new \App\Http\Traits\GlobalFunction)->getRouterIndex();
        $this->part_view = $router_name->path_base;
        $this->url_index = $router_name->uri;
        $this->url_name = $router_name->router_name;

        $this->title = 'Kalender Kerja Karyawan';
        $this->breadcrumbs = [
            ['title' => $this->title, 'url' => url('/') . "/" . $this->url_index],
        ];

        $this->globalService = new GlobalService;
        $this->dataLaporanPresensiService = new DataLaporanPresensiService;
        $this->kalenderKerjaKaryawanService = new KalenderKerjaKaryawanService;
    }

    public function actionIndex(Request $request){

        $user_auth = (new \App\Http\Traits\AuthFunction)->getUser();

        $get_karyawan = $this->kalenderKerjaKaryawanService->getKaryawan($user_auth->id_user);
        $id_karyawan = $get_karyawan->id_karyawan;
        
        $form_filter_text=!empty($request->form_filter_text) ? $request->form_filter_text : '';
        $filter_tahun_bulan=!empty($request->filter_tahun_bulan) ? $request->filter_tahun_bulan : date('Y-m');

        $get_tgl_per_bulan=(new \App\Http\Traits\AbsensiFunction)->get_tgl_per_bulan($filter_tahun_bulan);
        $list_tgl=!empty($get_tgl_per_bulan->list_tgl) ? $get_tgl_per_bulan->list_tgl : [];

        $filter_tgl=!empty($get_tgl_per_bulan->tgl_start_end) ? $get_tgl_per_bulan->tgl_start_end : [];
        $filter_tgl[0]=!empty($filter_tgl[0]) ? $filter_tgl[0] : date('Y-m-d');
        $filter_tgl[1]=!empty($filter_tgl[1]) ? $filter_tgl[1] : date('Y-m-d');

        $filter_id_departemen=!empty($request->filter_id_departemen) ? $request->filter_id_departemen : '';
        $filter_id_ruangan=!empty($request->filter_id_ruangan) ? $request->filter_id_ruangan : '';

        $exp_tahun_bulan=new \DateTime($filter_tahun_bulan);
        $tahun_filter=$exp_tahun_bulan->format('Y');
        $bulan_filter=$exp_tahun_bulan->format('m');


        $list_data=$collection = collect([]);
        if(!empty($request->isMethod('get'))){

            $paramter_search=[
                'tanggal'=>$filter_tgl,
                'search'=>$form_filter_text,
            ];

            $list_data=(new \App\Services\KalenderKerjaKaryawanService())->get_data_karyawan_absensi($paramter_search, $id_karyawan);
            
            // ->orderBy('id_departemen','ASC')
            // ->orderBy('id_ruangan','ASC')
            // ->orderBy('id_status_karyawan','ASC')
            // ->orderBy('nm_karyawan','ASC')
        }

        $page = isset($request->page) ? $request->page : 1;
        $option=['path' => $request->url(), 'query' => $request->query()];
        $max_page=!empty($list_data->count()) ? $list_data->count() : 2;
        $list_data = (new \App\Http\Traits\GlobalFunction)->paginate($list_data,$max_page,$page,$option);

        $get_template_id=(new \App\Models\RefTemplateJadwalShift())->get();

        $get_tamplate_default=[];
        if(!empty($get_template_id)){
            foreach($get_template_id as $item){
                $parameter=[
                    'id_template_jadwal_shift'=>$item->id_template_jadwal_shift,
                    'tahun'=>$tahun_filter,
                    'bulan'=>$bulan_filter,
                ];

                $get_list_shift=(new \App\Services\DataPresensiService)->setListShift($parameter);
                $list_shift=!empty($get_list_shift->data) ? json_decode($get_list_shift->data,true)  : [];
                $get_tamplate_default[$item->id_template_jadwal_shift]=$list_shift;
            }
        }

        $data_jadwal_shift_by_sistem=[];
        $get_data_jadwal=(new \App\Services\RefJadwalService())->getListJadwal(['type_jenis'=>2,'status_jadwal'=>1],1)->orderBy('kd_jadwal','ASC')->get();
        foreach($get_data_jadwal as $item){
            $data_jadwal_shift_by_sistem[$item->id_jenis_jadwal][$item->kd_jadwal]=$item;
        }

        $paramter_search_karyawan=[
            'tahun'=>$tahun_filter,
            'bulan'=>$bulan_filter,
            'search'=>$form_filter_text,
        ];

        if(!empty($filter_id_departemen)){
            $paramter_search_karyawan['id_departemen']=$filter_id_departemen;
        }

        if(!empty($filter_id_ruangan)){
            $paramter_search_karyawan['id_ruangan']=$filter_id_ruangan;
        }

        if(!empty($filter_id_status_karyawan)){
            $paramter_search_karyawan['id_status_karyawan']=$filter_id_status_karyawan;
        }

        $parameter_cuti = [
            'tanggal' => $filter_tgl
        ];

        $list_cuti = (new \App\Services\CutiService)
            ->getDataCutiApproved($parameter_cuti);

        $list_tamplate_user=(new \App\Services\RefKaryawanJadwalShiftWaktuService())->getDataList($paramter_search_karyawan);
        $jenis_jadwal = $this->kalenderKerjaKaryawanService->getJenisJadwal();
        
        $parameter_view = [
            'title' => $this->title,
            'breadcrumbs' => $this->breadcrumbs,
            'list_tgl'=>$list_tgl,
            'list_data'=>$list_data,
            'list_cuti' => $list_cuti,
            'list_shift' => $jenis_jadwal,
            'get_tamplate_default'=>$get_tamplate_default,
            'list_tamplate_user'=>$list_tamplate_user,
            'data_jadwal_shift_by_sistem'=>$data_jadwal_shift_by_sistem
        ];

        return view($this->part_view . '.index', $parameter_view);
    }

    

}
