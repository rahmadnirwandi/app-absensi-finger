<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use PhpOffice\PhpSpreadsheet\IOFactory;

use App\Services\GlobalService;
use App\Services\DataLaporanPresensiService;
use DateTime;

class ReportAbsensiKaryawanController extends \App\Http\Controllers\MyAuthController
{
    public $part_view, $url_index, $url_name, $title, $breadcrumbs, $globalService;
    public $dataLaporanPresensiService;

    public function __construct()
    {
        $router_name = (new \App\Http\Traits\GlobalFunction)->getRouterIndex();
        $this->part_view = $router_name->path_base;
        $this->url_index = $router_name->uri;
        $this->url_name = $router_name->router_name;

        $this->title = 'Report Absensi Karyawan';
        $this->breadcrumbs = [
            ['title' => 'Manajemen Absensi', 'url' => url('/') . "/sub-menu?type=6"],
            ['title' => $this->title, 'url' => url('/') . "/" . $this->url_index],
        ];

        $this->globalService = new GlobalService;
        $this->dataLaporanPresensiService = new DataLaporanPresensiService;
    }

    private function karyawan_rutin($request){

        $form_filter_text=!empty($request->form_filter_text) ? $request->form_filter_text : '';
        $filter_tahun_bulan=!empty($request->filter_tahun_bulan) ? $request->filter_tahun_bulan : date('Y-m');

        $get_tgl_per_bulan=(new \App\Http\Traits\AbsensiFunction)->get_tgl_per_bulan($filter_tahun_bulan);
        $list_tgl=!empty($get_tgl_per_bulan->list_tgl) ? $get_tgl_per_bulan->list_tgl : [];

        
        $filter_tgl=!empty($get_tgl_per_bulan->tgl_start_end) ? $get_tgl_per_bulan->tgl_start_end : [];

        $filter_tgl[0]=!empty($filter_tgl[0]) ? $filter_tgl[0] : date('Y-m-d');
        $filter_tgl[1]=!empty($filter_tgl[1]) ? $filter_tgl[1] : date('Y-m-d');

        $filter_id_departemen=!empty($request->filter_id_departemen) ? $request->filter_id_departemen : '';
        $filter_id_ruangan=!empty($request->filter_id_ruangan) ? $request->filter_id_ruangan : '';
        
        $list_data=$collection = collect([]);
        if($request->isMethod('get')){

            $paramter_search=[
                'tanggal'=>$filter_tgl,
                'search'=>$form_filter_text,
            ];

            if(!empty($filter_id_departemen)){
                $paramter_search['id_departemen']=$filter_id_departemen;
            }

            if(!empty($filter_id_ruangan)){
                $paramter_search['id_ruangan']=$filter_id_ruangan;
            }

            if(!empty($filter_id_status_karyawan)){
                $paramter_search['id_status_karyawan']=$filter_id_status_karyawan;
            }

            // $list_data=(new \App\Services\DataPresensiService)->get_data_karyawan_absensi_rutin($paramter_search,1)
            // ->get();

            $list_data=(new \App\Services\DataPresensiService)->get_data_karyawan_absensi_rutin($paramter_search,1);

            $parameter_cuti=$paramter_search;
            unset($parameter_cuti['id_jenis_jadwal']);
            unset($parameter_cuti['id_departemen']);
            unset($parameter_cuti['id_ruangan']);

//            $list_cuti=(new \App\Services\CutiKaryawanService)->getDataCuti($parameter_cuti,1)->first();
//            $list_cuti=!empty($list_cuti->hasil) ? json_decode($list_cuti->hasil,true) : [];
            $list_cuti=(new \App\Services\CutiService)->getCutiApprovedByRange($parameter_cuti);

            $parameter_pd=$paramter_search;
            unset($parameter_pd['id_jenis_jadwal']);
            unset($parameter_pd['id_departemen']);
            unset($parameter_pd['id_ruangan']);

            $list_pd=(new \App\Services\PerjalananDinasService)->getDataPerjalanDinas($parameter_pd,1)->first();
            $list_dinasluar=!empty($list_pd->hasil) ? json_decode($list_pd->hasil,true) : [];
        }

        $page = isset($request->page) ? $request->page : 1;
        $option=['path' => $request->url(), 'query' => $request->query()];
        $max_page=!empty($list_data->count()) ? $list_data->count() : 2;
        $list_data = (new \App\Http\Traits\GlobalFunction)->paginate($list_data,$max_page,$page,$option);

        $paramater_where=[
            'tanggal'=>[$filter_tgl[0],$filter_tgl[1]],
        ];

        $list_hari_libur=(new \App\Services\DataPresensiService)->get_data_hari_libur($paramater_where);

        $data_jadwal_rutin=(new \App\Http\Traits\PresensiHitungRutinFunction)->get_jadwal_rutin();
        
        $list_simbol_text=(new \App\Http\Traits\AbsensiFunction)->get_list_simbol_text();


        
        $parameter_view = [
            'title' => $this->title,
            'breadcrumbs' => $this->breadcrumbs,
            'list_tgl'=>$list_tgl,
            'list_data'=>$list_data,
            'list_hari_libur'=>$list_hari_libur,
            'data_jadwal_rutin'=>$data_jadwal_rutin,
            'list_simbol_text'=>$list_simbol_text,
            'list_cuti'=>!empty($list_cuti) ? $list_cuti : '',
            'list_dinasluar'=>!empty($list_dinasluar) ? $list_dinasluar : '',
        ];

        return view($this->part_view . '.index_rutin', $parameter_view);
    }

    private function karyawan_shift($request){
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

            if(!empty($filter_id_departemen)){
                $paramter_search['id_departemen']=$filter_id_departemen;
            }

            if(!empty($filter_id_ruangan)){
                $paramter_search['id_ruangan']=$filter_id_ruangan;
            }

            if(!empty($filter_id_status_karyawan)){
                $paramter_search['id_status_karyawan']=$filter_id_status_karyawan;
            }

            $list_data=(new \App\Services\DataPresensiService)->get_data_karyawan_absensi_shift($paramter_search,1);
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

        $list_tamplate_user=(new \App\Services\RefKaryawanJadwalShiftWaktuService())->getDataList($paramter_search_karyawan);

        $parameter_view = [
            'title' => $this->title,
            'breadcrumbs' => $this->breadcrumbs,
            'list_tgl'=>$list_tgl,
            'list_data'=>$list_data,
            'get_tamplate_default'=>$get_tamplate_default,
            'list_tamplate_user'=>$list_tamplate_user,
            'data_jadwal_shift_by_sistem'=>$data_jadwal_shift_by_sistem
        ];

        return view($this->part_view . '.index_shift', $parameter_view);
    }

    function actionIndex(Request $request){
        ini_set("memory_limit","800M");
        set_time_limit(0);

        $type_modul=!empty($request->type_link) ? $request->type_link : '';

        $type_modul=!empty($type_modul) ? $type_modul : 1;
        
        if($type_modul==1){
            return $this->karyawan_rutin($request);
        }

        if($type_modul==2){
            return $this->karyawan_shift($request);
        }
    }

    public function cetak_rutin($request){
        ini_set("memory_limit","500M");

        $data_sent = !empty($request->data_sent) ? $request->data_sent : '';
        $data_sent = !empty($data_sent) ? json_decode($data_sent) : '';
        
        $form_filter_text=!empty($data_sent->form_filter_text) ? $data_sent->form_filter_text : '';
        $filter_tahun_bulan=!empty($data_sent->filter_tahun_bulan) ? $data_sent->filter_tahun_bulan : date('Y-m');

        $get_tgl_per_bulan=(new \App\Http\Traits\AbsensiFunction)->get_tgl_per_bulan($filter_tahun_bulan);
        $list_tgl=!empty($get_tgl_per_bulan->list_tgl) ? $get_tgl_per_bulan->list_tgl : [];

        $filter_tgl=!empty($get_tgl_per_bulan->tgl_start_end) ? $get_tgl_per_bulan->tgl_start_end : [];
        $filter_tgl[0]=!empty($filter_tgl[0]) ? $filter_tgl[0] : date('Y-m-d');
        $filter_tgl[1]=!empty($filter_tgl[1]) ? $filter_tgl[1] : date('Y-m-d');

        $paramter_search=[
            'tanggal'=>$filter_tgl,
            'search'=>$form_filter_text,
        ];

        $get_nm_departemen='';
        $filter_id_departemen=!empty($data_sent->filter_id_departemen) ? $data_sent->filter_id_departemen : '';
        if(!empty($filter_id_departemen)){
            $paramter_search['id_departemen']=$filter_id_departemen;
            $get_nm_departemen=(new \App\Models\RefDepartemen())->where('id_departemen',$filter_id_departemen)->first();
            $get_nm_departemen=!empty($get_nm_departemen->nm_departemen) ? $get_nm_departemen->nm_departemen : '';
        }

        $get_nm_ruangan='';
        $filter_id_ruangan=!empty($data_sent->filter_id_ruangan) ? $data_sent->filter_id_ruangan : '';
        if(!empty($filter_id_ruangan)){
            $paramter_search['id_ruangan']=$filter_id_ruangan;
            $get_nm_ruangan=(new \App\Models\RefRuangan())->where('id_ruangan',$filter_id_ruangan)->first();
            $get_nm_ruangan=!empty($get_nm_ruangan->nm_ruangan) ? $get_nm_ruangan->nm_ruangan : '';
        }

        $list_data=(new \App\Services\DataPresensiService)->get_data_karyawan_absensi_rutin($paramter_search,1);

        $parameter_cuti=$paramter_search;
        unset($parameter_cuti['id_jenis_jadwal']);
        unset($parameter_cuti['id_departemen']);
        unset($parameter_cuti['id_ruangan']);

//        $list_cuti=(new \App\Services\CutiKaryawanService)->getDataCuti($parameter_cuti,1)->first();
//        $list_cuti=!empty($list_cuti->hasil) ? json_decode($list_cuti->hasil,true) : [];

        $list_cuti=(new \App\Services\CutiService)->getCutiApprovedByRange($parameter_cuti);

        $parameter_pd=$paramter_search;
        unset($parameter_pd['id_jenis_jadwal']);
        unset($parameter_pd['id_departemen']);
        unset($parameter_pd['id_ruangan']);

        $list_pd=(new \App\Services\PerjalananDinasService)->getDataPerjalanDinas($parameter_pd,1)->first();
        $list_dinasluar=!empty($list_pd->hasil) ? json_decode($list_pd->hasil,true) : [];

        $paramater_where=[
            'tanggal'=>[$filter_tgl[0],$filter_tgl[1]],
        ];

        $list_hari_libur=(new \App\Services\DataPresensiService)->get_data_hari_libur($paramater_where);

//        $data_jadwal_rutin=(new \App\Http\Traits\AbsensiFunction)->getJadwalRutin();

        $list_simbol_text=(new \App\Http\Traits\AbsensiFunction)->get_list_simbol_text();

        if($list_data){
            $get_tb=new \DateTime($filter_tahun_bulan.'-01');
            $tahun=$get_tb->format('Y');
            $bulan=(int)$get_tb->format('m');
            $bulan_text=(new \App\Http\Traits\GlobalFunction)->get_bulan($bulan);

            $file_='report_absensi_karyawan_rutin.xlsx';

            $nama_excel="report_absensi_jadwal_rutin";
            $nama_excel.="_".$tahun."_".$bulan;
            $nama_excel.=!empty($get_nm_departemen) ? "_".strtolower( str_replace(" ","_",$get_nm_departemen) ) : '';
            $nama_excel.=!empty($get_nm_ruangan) ? "_".strtolower( str_replace(" ","_",$get_nm_ruangan) ) : '';
            $nama_excel.=".xlsx";

            $objPHPExcel = IOFactory::load(resource_path('views/'.$this->part_view . '/'.$file_));
            
            $fill_solid=\PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID;

//            $hari_kerja_tmp=!empty($data_jadwal_rutin->data_jadwal_presensi->hari_kerja) ? $data_jadwal_rutin->data_jadwal_presensi->hari_kerja : '';
            $hari_kerja=[];
            $get_hari_minggu=[];
            $data_tgl=[];
            $data_hari_e=[];
            $data_hari_indo=[];
//            $jml_hari_kerja=0;
            $jml_hari_kerja_bulan=0;
            $jml_hari_libur=0;
            $hari_minggu=[];

            $column_awal_tgl='E';
            $position_awal_tgl=9;

            if(!empty($hari_kerja_tmp)){
                $hari_kerja_t=explode(',',$hari_kerja_tmp);
                if($hari_kerja_t){
                    foreach($hari_kerja_t as $hk){
                        $hari_kerja[$hk]=$hk;
                    }
                }
            }

            $jml_hari_kerja=count($hari_kerja);
//            $total_kerja_sec_sistem_sec=!empty($data_jadwal_rutin->total_waktu_kerja_sec) ? $data_jadwal_rutin->total_waktu_kerja_sec : 0;
            $total_kerja_sec_sistem=!empty($total_kerja_sec_sistem_sec) ? (new \App\Http\Traits\AbsensiFunction)->change_format_waktu_indo($total_kerja_sec_sistem_sec,":") : '00::00:00';

            $data_libur_format=[];
            if(!empty($list_hari_libur)){
                foreach($list_hari_libur as $key_l => $val_l){
                    if(empty($data_libur_format[$val_l->asal_tanggal])){
                        $data_libur_format[$val_l->asal_tanggal]=[
                            'uraian'=>$val_l->uraian,
                            'tgl_mulai'=>$val_l->asal_tanggal,
                            'tgl_akhir'=>$val_l->asal_tanggal,
                        ];
                    }else{
                        $data_libur_format[$val_l->asal_tanggal]['tgl_akhir']=$key_l;
                    }
                }
            }

            $column_index_tgl=[];
            $column_index_hari_libur_kerja=[];
            $column_index_hari_libur_nasional=[];

            foreach($list_tgl as $key_tgl => $item_tgl){
                $column_index_tgl[$item_tgl]=$column_awal_tgl;

                $total_waktu_hari_ini = 0;
                $is_off = false;
                foreach($list_data as $item){

                    $data_presensi = json_decode($item->presensi, true);

                    $get_presensi_user = $data_presensi[$item_tgl] ?? null;

                    if(!empty($get_presensi_user['jadwal'])){

                        $jadwal_per_tanggal = $get_presensi_user['jadwal'];

                        $alias = strtoupper($jadwal_per_tanggal['nm_jenis_jadwal'] ?? '');

                        if($alias == 'OFF'){
                            $is_off = true;
                        }
                    }
                }


                $tgl_format_tmp = new \DateTime($item_tgl);
                $tgl_format=$tgl_format_tmp->format('d/m');
                $hari_format=$tgl_format_tmp->format('D');
        
                $data_tgl[$key_tgl]=$tgl_format;
                $data_hari_e[$key_tgl]=$hari_format;
                $data_hari_indo[$key_tgl]=(new \App\Http\Traits\GlobalFunction)->hari($hari_format,1);
        
                $nm_hari=!empty($data_hari_indo[$key_tgl]) ? $data_hari_indo[$key_tgl] : '';
        
                if(!empty($hari_kerja[$hari_format])){
                    $jml_hari_kerja_bulan++;
                }

                if(!empty($list_hari_libur[$item_tgl])){
                    $jml_hari_libur++;
                    $column_index_hari_libur_nasional[$column_awal_tgl]=$column_awal_tgl;
                }

                if($key_tgl>=28){
                    $objPHPExcel->setActiveSheetIndex(0)->insertNewColumnBefore($column_awal_tgl);
                    $objPHPExcel->getActiveSheet()->getColumnDimension($column_awal_tgl)->setWidth(9);
                }
                $objPHPExcel->setActiveSheetIndex(0)->setCellValue($column_awal_tgl.$position_awal_tgl, $tgl_format);
                $objPHPExcel->setActiveSheetIndex(0)->setCellValue($column_awal_tgl.($position_awal_tgl+1), $nm_hari);
                $column_awal_tgl++;
            }

            $hari_libur_text=[];
            if($data_libur_format){
                foreach($data_libur_format as $val_dlf){
                    $text_hasil='';
                    $val_dlf=(object)$val_dlf;
                    $text_hasil.=$val_dlf->uraian.' : ';
                    if($val_dlf->tgl_mulai!=$val_dlf->tgl_akhir){
                        $text_hasil.=$val_dlf->tgl_mulai.' s/d '.$val_dlf->tgl_akhir;
                    }else{
                        $text_hasil.=$val_dlf->tgl_mulai;
                    }
                    $hari_libur_text[]=$text_hasil;
                }
            }
            $hari_libur_text=!empty($hari_libur_text) ? implode(', ',$hari_libur_text) : '';

            $set_end_column_index=end($column_index_tgl);
            for ($i=0; $i < 2; $i++) {
                $set_end_column_index++;
            }

            $total_jml_hari_kerja_bulan=$jml_hari_kerja_bulan-$jml_hari_libur;

//            $total_kerja_bulan_sec=$total_kerja_sec_sistem_sec * $total_jml_hari_kerja_bulan;

            $total_kerja_bulan_sec = 0;

            if(!empty($list_data)){
                $sample = $list_data[0];
                $data_presensi_sample = json_decode($sample->presensi, true);

                foreach($list_tgl as $item_tgl){

                    $get_presensi_user = $data_presensi_sample[$item_tgl] ?? null;

                    if(!empty($get_presensi_user['jadwal'])){

                        $jadwal = $get_presensi_user['jadwal'];

                        $alias = strtoupper($jadwal['nm_jenis_jadwal'] ?? '');

                        if($alias != 'OFF'){

                            $total_kerja_bulan_sec +=
                                $jadwal['total_waktu_kerja_sec'] ?? 0;
                        }
                    }
                }
            }


            $hari_minggu=!empty($hari_minggu) ? implode(',',$hari_minggu) : '';

            $objPHPExcel->setActiveSheetIndex(0)->setCellValue("D4",$total_jml_hari_kerja_bulan.' Hari' );
            $objPHPExcel->setActiveSheetIndex(0)->setCellValue("D5",(new \App\Http\Traits\AbsensiFunction)->change_format_waktu_indo($total_kerja_bulan_sec) );
            $objPHPExcel->setActiveSheetIndex(0)->setCellValue("J4",$hari_minggu );
            $objPHPExcel->setActiveSheetIndex(0)->setCellValue("J5",$hari_libur_text );

            $header_text="Rekap Absensi Karyawan Tahun ".$tahun." Bulan ".$bulan_text;
            $header_text.=" ".$get_nm_departemen;
            $header_text.=" ".$get_nm_ruangan;
            $header_1=strtoupper($header_text);
            $header_1_index='A';
            $header_1_posi=2;
            $objPHPExcel->setActiveSheetIndex(0)->setCellValue($header_1_index.$header_1_posi,$header_1 );
            $objPHPExcel->getActiveSheet()->mergeCells($header_1_index.$header_1_posi.':'.$set_end_column_index.$header_1_posi );

            $position_= 10;
            $position_awal_=$position_;

            $list_departemen=[];
            $list_departemen_first=[];
            $list_ruangan=[];
            $list_ruangan_first=[];
            $list_status_karyawan=[];
            $list_status_karyawan_first=[];

            $jml_item=0;
            
            $style_column_nama=[];
            $column_ada_data=[];
            $column_total_data=[];
            $color_selisih_positif="358f00";
            $color_selisih_negatif="8f1300";

            foreach($list_data as $key => $item){
                $jml_item=$key+1;
                $data_presensi=!empty($item->presensi) ? (array)json_decode($item->presensi) : [];


                $list_cuti_karyawan=[];
                $total_cuti=0;
                if(!empty($list_cuti[$item->id_karyawan])){
                    $data_cuti=(object)$list_cuti[$item->id_karyawan];
                    if(!empty($data_cuti->waktu)){
                        foreach($data_cuti->waktu as $wc){
                            if(!empty($wc[0])){
                                $parameter_get_cuti=[
                                    'tgl_awal'=>!empty($wc[0]) ? $wc[0] : '',
                                    'tgl_akhir'=>!empty($wc[1]) ? $wc[1] : '',
                                    'data_sent'=>!empty($wc[3]) ? $wc[3] : '',
                                    'list_libur_kerja'=>!empty($get_hari_minggu) ? $get_hari_minggu : '',
                                    'list_libur_nasional'=>!empty($list_hari_libur) ? $list_hari_libur : '',
                                ];
                                $hasil_cuti_tmp=(new \App\Http\Traits\AbsensiFunction)->get_tgl_khusus_with_data($parameter_get_cuti);
                                $hasil_cuti=!empty($hasil_cuti_tmp['hasil_data']) ? $hasil_cuti_tmp['hasil_data'] : [];
                                $total_cuti+=!empty($hasil_cuti_tmp['jml_hari']) ? $hasil_cuti_tmp['jml_hari'] : 0;
                                $list_cuti_karyawan=array_merge($list_cuti_karyawan,$hasil_cuti);
                            }
                        }
                    }
                }

                $list_dinasluar_karyawan=[];
                $total_dinasluar=0;
                if(!empty($list_dinasluar[$item->id_karyawan])){
                    $data_dinasluar=(object)$list_dinasluar[$item->id_karyawan];
                    if(!empty($data_dinasluar->waktu)){
                        foreach($data_dinasluar->waktu as $wc){
                            if(!empty($wc[0])){
                                $parameter_get_dinasluar=[
                                    'tgl_awal'=>!empty($wc[0]) ? $wc[0] : '',
                                    'tgl_akhir'=>!empty($wc[1]) ? $wc[1] : '',
                                    'data_sent'=>!empty($wc[3]) ? $wc[3] : '',
                                    'list_libur_kerja'=>!empty($get_hari_minggu) ? $get_hari_minggu : '',
                                    'list_libur_nasional'=>!empty($list_hari_libur) ? $list_hari_libur : '',
                                ];
                                $hasil_dinasluar_tmp=(new \App\Http\Traits\AbsensiFunction)->get_tgl_khusus_with_data($parameter_get_dinasluar);
                                $hasil_dinasluar=!empty($hasil_dinasluar_tmp['hasil_data']) ? $hasil_dinasluar_tmp['hasil_data'] : [];
                                $total_dinasluar+=!empty($hasil_dinasluar_tmp['jml_hari']) ? $hasil_dinasluar_tmp['jml_hari'] : 0;
                                $list_dinasluar_karyawan=array_merge($list_dinasluar_karyawan,$hasil_dinasluar);
                            }
                        }
                    }
                }

                if(empty($list_departemen[$item->id_departemen])){
                    $list_departemen[$item->id_departemen]=1;
                    $cell_me='A';
                    $objPHPExcel->setActiveSheetIndex(0)->setCellValue($cell_me.$position_, !empty($item->nm_departemen) ? $item->nm_departemen : '' );
                    $merge_me=$cell_me.$position_.':'.$set_end_column_index.$position_;
                    $objPHPExcel->getActiveSheet()->mergeCells($merge_me);

                    if(empty($list_departemen_first)){
                        $list_departemen_first[]=$cell_me.$position_;
                    }else{
                        $this_style=$objPHPExcel->getActiveSheet()->getStyle($list_departemen_first[0]);
                        $objPHPExcel->getActiveSheet()->duplicateStyle($this_style,$cell_me.$position_);
                    }

                    $position_++;
                }

                if(empty($list_ruangan[$item->id_ruangan])){
                    $list_ruangan[$item->id_ruangan]=1;
                    $cell_me='A';
                    $objPHPExcel->setActiveSheetIndex(0)->setCellValue($cell_me.$position_, !empty($item->nm_ruangan) ? $item->nm_ruangan : '' );
                    $merge_me=$cell_me.$position_.':'.$set_end_column_index.$position_;
                    $objPHPExcel->getActiveSheet()->mergeCells($merge_me);

                    if(empty($list_ruangan_first)){
                        $list_ruangan_first[]=$cell_me.$position_;
                    }else{
                        $this_style=$objPHPExcel->getActiveSheet()->getStyle($list_ruangan_first[0]);
                        $objPHPExcel->getActiveSheet()->duplicateStyle($this_style,$cell_me.$position_);
                    }

                    $position_++;
                }

                if(empty($list_status_karyawan[$item->id_ruangan][$item->id_status_karyawan])){
                    $list_status_karyawan[$item->id_ruangan][$item->id_status_karyawan]=1;
                    $cell_me='B';
                    $objPHPExcel->setActiveSheetIndex(0)->setCellValue($cell_me.$position_, !empty($item->nm_status_karyawan) ? $item->nm_status_karyawan : '' );
                    $merge_me=$cell_me.$position_.':'.$set_end_column_index.$position_;
                    $objPHPExcel->getActiveSheet()->mergeCells($merge_me);

                    if(empty($list_status_karyawan_first)){
                        $list_status_karyawan_first[]=$cell_me.$position_;
                    }else{
                        $this_style=$objPHPExcel->getActiveSheet()->getStyle($list_status_karyawan_first[0]);
                        $objPHPExcel->getActiveSheet()->duplicateStyle($this_style,$cell_me.$position_);
                    }

                    $position_++;
                }

                $nm_id_karyawan=( !empty($item->id_user) ? "( ".$item->id_user." ) " : '' ).''.( !empty($item->nm_karyawan) ? $item->nm_karyawan : '' );
                $objPHPExcel->setActiveSheetIndex(0)->setCellValue('A'.$position_,$jml_item );
                $objPHPExcel->setActiveSheetIndex(0)->setCellValue('B'.$position_,$nm_id_karyawan);
                $set_merge_='B'.$position_.':'.'D'.$position_;
                $objPHPExcel->getActiveSheet()->mergeCells($set_merge_);

                $grand_twjk_user_sec=0;
                $grand_twjk_sistem_sec=0;
                foreach($list_tgl as $key_tgl => $item_tgl){
                    if(!empty($column_index_tgl[$item_tgl])){
                        $column_me=$column_index_tgl[$item_tgl];

                        $presensi_user_text='';
                        $status_kerja_alias='';
                        $total_wjk_user_perhari_sec=0;

                        $jadwal_per_tanggal = null;

//                        $get_presensi_user=!empty($data_presensi[$item_tgl]) ? $data_presensi[$item_tgl] : '';
//                        if(!empty($get_presensi_user)){
//
//                            if($item->jadwal_rutin==1){
//                                $data_proses=[
//                                    'list_presensi'=>!empty($get_presensi_user->presensi) ? implode(',',$get_presensi_user->presensi) : '',
//                                    'data_jadwal_kerja'=>!empty($data_jadwal_rutin->data_jadwal_presensi) ? $data_jadwal_rutin->data_jadwal_presensi  : ''
//                                ];
//                                $hasil_proses=(object)(new \App\Http\Traits\PresensiHitungRutinFunction)->getHitungRutin($data_proses);
//                                $total_wjk_user_perhari_sec=!empty($hasil_proses->total_waktu_kerja_user_sec) ? $hasil_proses->total_waktu_kerja_user_sec : 0;
//
//                                $presensi_filter=!empty($hasil_proses->presensi_user) ? $hasil_proses->presensi_user : '';
//                                $presensi_filter_tmp=!empty($presensi_filter) ? explode(',',$presensi_filter) : [];
//                                if($presensi_filter_tmp){
//                                    $presensi_user_text=implode(',',$presensi_filter_tmp);
//                                    $presensi_user_text=str_replace(',',','.PHP_EOL,$presensi_user_text);
//                                }
//
//                                $status_kerja_alias=!empty($hasil_proses->status_kerja) ? $hasil_proses->status_kerja->alias : '';
//
//                                if($status_kerja_alias=='A'){
//                                    $presensi_user_text='';
//                                }
//                            }
//                        }
                        $get_presensi_user=!empty($data_presensi[$item_tgl]) ? $data_presensi[$item_tgl] : '';

                        if(!empty($get_presensi_user)){

                            $jadwal_per_tanggal = !empty($get_presensi_user->jadwal)
                                ? $get_presensi_user->jadwal
                                : null;

                            if(!empty($jadwal_per_tanggal)){

                                $data_proses=[
                                    'list_presensi'=>!empty($get_presensi_user->presensi)
                                        ? implode(',',$get_presensi_user->presensi)
                                        : '',
                                    'data_jadwal_kerja'=>$jadwal_per_tanggal
                                ];


                                $hasil_proses=(object)(new \App\Http\Traits\PresensiHitungRutinFunction)
                                    ->getHitungRutin($data_proses);

                                $total_wjk_user_perhari_sec=
                                    !empty($hasil_proses->total_waktu_kerja_user_sec)
                                        ? $hasil_proses->total_waktu_kerja_user_sec
                                        : 0;

                                $presensi_filter=
                                    !empty($hasil_proses->presensi_user)
                                        ? $hasil_proses->presensi_user
                                        : '';

                                $presensi_filter_tmp=
                                    !empty($presensi_filter)
                                        ? explode(',',$presensi_filter)
                                        : [];

                                if($presensi_filter_tmp){
                                    $presensi_user_text=implode(',',$presensi_filter_tmp);
                                    $presensi_user_text=str_replace(',',','.PHP_EOL,$presensi_user_text);
                                }

                                $status_kerja_alias=
                                    !empty($hasil_proses->status_kerja)
                                        ? $hasil_proses->status_kerja->alias
                                        : '';
                            }
                        }

//                        $total_wjk_sistem_perhari_sec=!empty($data_jadwal_rutin->total_waktu_kerja_sec) ? $data_jadwal_rutin->total_waktu_kerja_sec : 0;
                        $total_wjk_sistem_perhari_sec =
                            (!empty($jadwal_per_tanggal) && !empty($jadwal_per_tanggal->total_waktu_kerja_sec))
                                ? $jadwal_per_tanggal->total_waktu_kerja_sec
                                : 0;
                        $total_wjk_user_perhari_sec=!empty($total_wjk_user_perhari_sec) ? $total_wjk_user_perhari_sec : 0;

                        $total_wjku_perhari_sec=$total_wjk_user_perhari_sec;

                        $grand_twjk_user_sec+=$total_wjku_perhari_sec;
                        $grand_twjk_sistem_sec+=$total_wjk_sistem_perhari_sec;
//
//                        if(empty($status_kerja_alias)){
//                            $status_kerja_alias='A';
//                        }
//
//                        if(!empty($status_kerja_alias)){
//                            $status_kerja_alias="(".$status_kerja_alias.")";
//                        }

                        if(!empty($list_cuti_karyawan[$item_tgl])){
                            $presensi_user_text=$list_cuti_karyawan[$item_tgl];
                            $get_column_cuti[]=$column_me.$position_;
                            if(!empty($jadwal_per_tanggal->total_waktu_kerja_sec)){
                                $grand_twjk_user_sec+=$jadwal_per_tanggal->total_waktu_kerja_sec;
                                $status_kerja_alias='';
                                if(!empty($get_hari_minggu[$item_tgl])){
                                    $grand_twjk_user_sec-=$jadwal_per_tanggal->total_waktu_kerja_sec;
                                    $total_wjku_perhari_sec='';
                                    $presensi_user_text='';
                                }

                                if(!empty($list_hari_libur[$item_tgl])){
                                    $grand_twjk_user_sec-=$jadwal_per_tanggal->total_waktu_kerja_sec;
                                    $total_wjku_perhari_sec='';
                                    $presensi_user_text='';
                                }
                            }
                        }

                        if(!empty($list_dinasluar_karyawan[$item_tgl])){
                            $presensi_user_text=$list_dinasluar_karyawan[$item_tgl];
                            $get_column_dinasluar[]=$column_me.$position_;
                            if(!empty($jadwal_per_tanggal->total_waktu_kerja_sec)){
                                $grand_twjk_user_sec+=$jadwal_per_tanggal->total_waktu_kerja_sec;
                                $status_kerja_alias='';
                            }
                        }

                        if(!empty($jadwal_per_tanggal)){
                            $alias_jadwal = strtoupper($jadwal_per_tanggal->nm_jenis_jadwal ?? '');

                            if($alias_jadwal == 'OFF'){
                                $objPHPExcel->getActiveSheet()
                                    ->getStyle($column_me.$position_)
                                    ->applyFromArray([
                                        'fill' => [
                                            'fillType' => \PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID,
                                            'startColor' => [
                                                'rgb' => 'F39791'
                                            ]
                                        ]
                                    ]);
                            }
                        }

                        if(!empty($get_hari_minggu[$item_tgl])){
                            if(empty($presensi_user_text)){
                                $status_kerja_alias='';
                            }
                            $grand_twjk_sistem_sec-=$total_wjk_sistem_perhari_sec;
                        }

                        if(!empty($list_hari_libur[$item_tgl])){
                            if(empty($presensi_user_text)){
                                $status_kerja_alias='';
                            }
                            $grand_twjk_sistem_sec-=$total_wjk_sistem_perhari_sec;
                        }

                        $tgl_data = new \DateTime($item_tgl);
                        $tgl_now = new \DateTime("now");
                        $tgl_now = new \DateTime($tgl_now->format('Y-m-d'));

                        if($tgl_data>$tgl_now){
                            $status_kerja_alias='';
                        }

                        $is_off = false;

                        if(!empty($jadwal_per_tanggal)){
                            $alias_jadwal = strtoupper($jadwal_per_tanggal->nm_jenis_jadwal ?? '');
                            if($alias_jadwal == 'OFF'){
                                $is_off = true;
                            }
                        }

                        $is_minggu = !empty($get_hari_minggu[$item_tgl]);
                        $is_libur_nasional = !empty($list_hari_libur[$item_tgl]);
                        $is_cuti = !empty($list_cuti_karyawan[$item_tgl]);
                        $is_dinasluar = !empty($list_dinasluar_karyawan[$item_tgl]);

                        if(
                            empty($status_kerja_alias)
                            && !$is_off
                            && !$is_minggu
                            && !$is_libur_nasional
                            && !$is_cuti
                            && !$is_dinasluar
                        ){
                            $status_kerja_alias = 'A';
                        }

                        if($is_off || $is_minggu || $is_libur_nasional){
                            $status_kerja_alias = '';
                        }

                        if(!empty($status_kerja_alias)){
                            $status_kerja_alias = "($status_kerja_alias)";
                        }

                        $presensi_user_text=$presensi_user_text.PHP_EOL.$status_kerja_alias;

                        $objPHPExcel->setActiveSheetIndex(0)->setCellValue($column_me.$position_,$presensi_user_text );
                        if(!empty($presensi_user_text)){
                            $column_ada_data[$column_me.$position_]=$column_me.$position_;
                        }
                    }
                }

                $grand_twjk_user_sec_text=(new \App\Http\Traits\AbsensiFunction)->change_format_waktu_indo($grand_twjk_user_sec,':');
                $grand_twjk_sistem_sec_text=(new \App\Http\Traits\AbsensiFunction)->change_format_waktu_indo($grand_twjk_sistem_sec,':');

                $selisih_grand_twjk=$grand_twjk_sistem_sec-$grand_twjk_user_sec;
                $tanda_selisih_grand_twjk=($selisih_grand_twjk<0) ? '+' : '-';
                $hasil_positif=
                $class_tanda_selisih_grand_twjk=($selisih_grand_twjk<0) ? $color_selisih_positif : $color_selisih_negatif;
                if($selisih_grand_twjk==0){
                    $tanda_selisih_grand_twjk='';
                    $class_tanda_selisih_grand_twjk=$color_selisih_positif;
                }

                $selisih_grand_twjk_text=$tanda_selisih_grand_twjk.' '.(new \App\Http\Traits\AbsensiFunction)->change_format_waktu_indo(abs($selisih_grand_twjk),':');

                $end_index_tgl=end($column_index_tgl);
                $end_index_tgl++;
                $objPHPExcel->setActiveSheetIndex(0)->setCellValue($end_index_tgl.$position_, $grand_twjk_user_sec_text );
                $column_total_data[$end_index_tgl.$position_]=$end_index_tgl.$position_;
                $end_index_tgl++;
                $objPHPExcel->setActiveSheetIndex(0)->setCellValue($end_index_tgl.$position_, $selisih_grand_twjk_text );
                $objPHPExcel->setActiveSheetIndex(0)->getStyle($end_index_tgl.$position_)->getFont()->getColor()->setARGB($class_tanda_selisih_grand_twjk);
                $column_total_data[$end_index_tgl.$position_]=$end_index_tgl.$position_;

                $position_++;
            }

            $position_--;

            $end_position=$position_;

            if(!empty($get_column_cuti)){
                foreach($get_column_cuti as $item){
                    $objPHPExcel->getActiveSheet()->getStyle($item)
                    ->getFill()
                    ->setFillType($fill_solid)
                    ->getStartColor()
                    ->setARGB('79f6eb');
                }
            }

            if(!empty($get_column_dinasluar)){
                foreach($get_column_dinasluar as $item){
                    $objPHPExcel->getActiveSheet()->getStyle($item)
                    ->getFill()
                    ->setFillType($fill_solid)
                    ->getStartColor()
                    ->setARGB('9cec6d');
                }
            }


            $cell_t=4;
//            $check_array_first=[];
//            foreach($column_index_hari_libur_kerja as $item){
//                if(empty($check_array_first)){
//                    $check_array_first[]=[$item,$position_awal_tgl,$end_position];
//                    $objPHPExcel->getActiveSheet()->getStyle($item.$position_awal_tgl.':'.$item.$end_position)
//                    ->getFill()
//                    ->setFillType($fill_solid)
//                    ->getStartColor()
//                    ->setARGB('F39791');
//                }else{
//                    $plus_=1;
//                    $header_=$check_array_first[0][0].$check_array_first[0][1].":".$check_array_first[0][0].($check_array_first[0][1]+$plus_);
//                    $data_=$check_array_first[0][0].($check_array_first[0][1]+$cell_t).":".$check_array_first[0][0].$check_array_first[0][2];
//
//                    $header_item_=$item.$check_array_first[0][1].':'.$item.($check_array_first[0][1]+$plus_);
//                    $data_item_=$item.$position_awal_tgl.':'.$item.$end_position;
//                    $data_item_=$item.($check_array_first[0][1]+$cell_t).":".$item.$check_array_first[0][2];
//
//                    $this_style=$objPHPExcel->getActiveSheet()->getStyle($header_);
//                    $objPHPExcel->getActiveSheet()->duplicateStyle($this_style,$header_item_);
//
//                    $this_style=$objPHPExcel->getActiveSheet()->getStyle($data_);
//                    $objPHPExcel->getActiveSheet()->duplicateStyle($this_style,$data_item_);
//                }
//            }

            $check_array_first=[];
            foreach($column_index_hari_libur_nasional as $item){
                if(empty($check_array_first)){
                    $check_array_first[]=[$item,$position_awal_tgl,$end_position];
                    $objPHPExcel->getActiveSheet()->getStyle($item.$position_awal_tgl.':'.$item.$end_position)
                    ->getFill()
                    ->setFillType($fill_solid)
                    ->getStartColor()
                    ->setARGB('F7D44D');
                }else{
                    $plus_=1;
                    $header_=$check_array_first[0][0].$check_array_first[0][1].":".$check_array_first[0][0].($check_array_first[0][1]+$plus_);
                    $data_=$check_array_first[0][0].($check_array_first[0][1]+$cell_t).":".$check_array_first[0][0].$check_array_first[0][2];

                    $header_item_=$item.$check_array_first[0][1].':'.$item.($check_array_first[0][1]+$plus_);
                    $data_item_=$item.$position_awal_tgl.':'.$item.$end_position;
                    $data_item_=$item.($check_array_first[0][1]+$cell_t).":".$item.$check_array_first[0][2];

                    $this_style=$objPHPExcel->getActiveSheet()->getStyle($header_);
                    $objPHPExcel->getActiveSheet()->duplicateStyle($this_style,$header_item_);

                    $this_style=$objPHPExcel->getActiveSheet()->getStyle($data_);
                    $objPHPExcel->getActiveSheet()->duplicateStyle($this_style,$data_item_);
                }
            }

            if(!empty($column_ada_data)){
                $a_tmp=array_values($column_ada_data);
                $first_column_ada_data=$a_tmp[0];
                $end_column_ada_data=end($a_tmp);
                $objPHPExcel->setActiveSheetIndex(0)->getStyle($first_column_ada_data.':'.$end_column_ada_data)->getAlignment()->setWrapText(true)
                ->setHorizontal('center');
                $objPHPExcel->setActiveSheetIndex(0)->getStyle($first_column_ada_data.':'.$end_column_ada_data)->getFont()->setSize(12);
            }

            if(!empty($column_total_data)){
                $a_tmp=array_values($column_total_data);
                $first_column_ada_data=$a_tmp[0];
                $end_column_ada_data=end($a_tmp);
                $objPHPExcel->setActiveSheetIndex(0)->getStyle($first_column_ada_data.':'.$end_column_ada_data)->getAlignment()->setHorizontal('right');
                $objPHPExcel->setActiveSheetIndex(0)->getStyle($first_column_ada_data.':'.$end_column_ada_data)->getFont()->setBold(true);
            }

            $cell_border="A".$position_awal_.':'.$end_index_tgl.$end_position;
            $objPHPExcel->getActiveSheet()->getStyle($cell_border)->getBorders()->getAllBorders()->setBorderStyle(\PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN);

            $position_keterangan=$end_position+2;

            $objPHPExcel->setActiveSheetIndex(0)->setCellValue('B'.$position_keterangan,"Keterangan" );
            $position_keterangan++;
            $objPHPExcel->setActiveSheetIndex(0)->setCellValue('B'.$position_keterangan,$list_simbol_text );
            $set_merge_='B'.$position_keterangan.':'.'H'.$position_keterangan;
            $objPHPExcel->getActiveSheet()->mergeCells($set_merge_);
            $objPHPExcel->getActiveSheet()->getStyle($set_merge_)->getAlignment()->setWrapText(true);
            $objPHPExcel->getActiveSheet()->getRowDimension($position_keterangan)->setRowHeight(50);
            
            $objWriter  = IOFactory::createWriter($objPHPExcel, "Xlsx");

            if (ob_get_contents()) ob_end_clean();
            header('Content-type: application/vnd.ms-excel');
            header('Content-Disposition: attachment; filename="'.$nama_excel.'"');
            header('Cache-Control: max-age=0');
            $writer = IOFactory::createWriter($objPHPExcel, 'Xlsx');
            $writer->save('php://output');
        }else{
            die;
        }
    }

    public function actionCetak(Request $request){
        ini_set("memory_limit","500M");

        $data_sent = !empty($request->data_sent) ? $request->data_sent : '';
        $data_sent = !empty($data_sent) ? json_decode($data_sent) : '';
        
        $type_modul=!empty($request->type_link) ? $request->type_link : '';

        $type_modul=!empty($type_modul) ? $type_modul : 1;
        
        if($type_modul==1){
            return $this->cetak_rutin($request);
        }

        if($type_modul==2){
            die;
        }

    }
}