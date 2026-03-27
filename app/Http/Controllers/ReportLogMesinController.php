<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use PhpOffice\PhpSpreadsheet\IOFactory;

use App\Services\GlobalService;
use App\Services\DataLaporanPresensiService;

class ReportLogMesinController extends \App\Http\Controllers\MyAuthController
{
    public $part_view, $url_index, $url_name, $title, $breadcrumbs, $globalService;
    public $dataLaporanPresensiService;

    public function __construct()
    {
        $router_name = (new \App\Http\Traits\GlobalFunction)->getRouterIndex();
        $this->part_view = $router_name->path_base;
        $this->url_index = $router_name->uri;
        $this->url_name = $router_name->router_name;

        $this->title = 'Report Absensi Histori Dari Mesin';
        $this->breadcrumbs = [
            ['title' => 'Manajemen Absensi', 'url' => url('/') . "/sub-menu?type=6"],
            ['title' => $this->title, 'url' => url('/') . "/" . $this->url_index],
        ];

        $this->globalService = new GlobalService;
        $this->dataLaporanPresensiService = new DataLaporanPresensiService;
    }

    function actionIndex(Request $request){
        ini_set("memory_limit","800M");
        set_time_limit(0);

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
            
            $list_data=(new \App\Services\DataPresensiService)->get_log_mesin_by_histori($paramter_search,1);
            // ->orderBy('id_departemen','ASC')
            // ->orderBy('id_ruangan','ASC')
            // ->orderBy('id_status_karyawan','ASC')
            // ->orderBy('nm_karyawan','ASC')
            // ->get();

        }

        $page = isset($request->page) ? $request->page : 1;
        $option=['path' => $request->url(), 'query' => $request->query()];
        $max_page=!empty($list_data->count()) ? $list_data->count() : 2;
        $list_data = (new \App\Http\Traits\GlobalFunction)->paginate($list_data,$max_page,$page,$option);

        $parameter_view = [
            'title' => $this->title,
            'breadcrumbs' => $this->breadcrumbs,
            'list_tgl'=>$list_tgl,
            'list_data'=>$list_data
        ];

        return view($this->part_view . '.index', $parameter_view);
    }

    public function actionCetak(Request $request){
        ini_set("memory_limit","800M");
        set_time_limit(0);

        $data_sent = !empty($request->data_sent) ? $request->data_sent : '';
        $data_sent = !empty($data_sent) ? json_decode($data_sent) : '';

        $form_filter_text=!empty($data_sent->form_filter_text) ? $data_sent->form_filter_text : '';
        $filter_tahun_bulan=!empty($data_sent->filter_tahun_bulan) ? $data_sent->filter_tahun_bulan : date('Y-m');

        $get_tgl_per_bulan=(new \App\Http\Traits\AbsensiFunction)->get_tgl_per_bulan($filter_tahun_bulan);
        $list_tgl=!empty($get_tgl_per_bulan->list_tgl) ? $get_tgl_per_bulan->list_tgl : [];

        $filter_tgl=!empty($get_tgl_per_bulan->tgl_start_end) ? $get_tgl_per_bulan->tgl_start_end : [];
        $filter_tgl[0]=!empty($filter_tgl[0]) ? $filter_tgl[0] : date('Y-m-d');
        $filter_tgl[1]=!empty($filter_tgl[1]) ? $filter_tgl[1] : date('Y-m-d');

        $get_nm_departemen='';
        $filter_id_departemen=!empty($data_sent->filter_id_departemen) ? $data_sent->filter_id_departemen : '';
        if(!empty($filter_id_departemen)){
            $get_nm_departemen=(new \App\Models\RefDepartemen())->where('id_departemen',$filter_id_departemen)->first();
            $get_nm_departemen=!empty($get_nm_departemen->nm_departemen) ? $get_nm_departemen->nm_departemen : '';
        }

        $get_nm_ruangan='';
        $filter_id_ruangan=!empty($data_sent->filter_id_ruangan) ? $data_sent->filter_id_ruangan : '';
        if(!empty($filter_id_ruangan)){
            $get_nm_ruangan=(new \App\Models\RefRuangan())->where('id_ruangan',$filter_id_ruangan)->first();
            $get_nm_ruangan=!empty($get_nm_ruangan->nm_ruangan) ? $get_nm_ruangan->nm_ruangan : '';
        }

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
        
        $list_data=(new \App\Services\DataPresensiService)->get_log_mesin_by_histori($paramter_search,1)
        ->orderBy('id_departemen','ASC')
        ->orderBy('id_ruangan','ASC')
        ->orderBy('id_status_karyawan','ASC')
        ->orderBy('nm_karyawan','ASC')
        ->get();

        if($list_data->count()>200){
            $link_back_param=(array)$data_sent;
            return redirect()->route($this->url_name, $link_back_param)->with(['error' => 'Maaf Data Terlalu banyak untuk di tampilkan,silahkan gunakan filter departemen/bidang,ruangan dll']);
        }
        if($list_data){

            $get_tb=new \DateTime($filter_tahun_bulan.'-01');
            $tahun=$get_tb->format('Y');
            $bulan=(int)$get_tb->format('m');
            $bulan_text=(new \App\Http\Traits\GlobalFunction)->get_bulan($bulan);

            $file_='format_report_log_mesin.xlsx';

            $nama_excel="rekap_log_mesin";
            $nama_excel.="_".$tahun."_".$bulan;
            $nama_excel.=!empty($get_nm_departemen) ? "_".strtolower( str_replace(" ","_",$get_nm_departemen) ) : '';
            $nama_excel.=!empty($get_nm_ruangan) ? "_".strtolower( str_replace(" ","_",$get_nm_ruangan) ) : '';
            $nama_excel.=".xlsx";

            $objPHPExcel = IOFactory::load(resource_path('views/'.$this->part_view . '/'.$file_));

            $data_tgl=[];
            $data_hari_e=[];
            $data_hari_indo=[];
            $jml_hari_kerja_bulan=0;
            
            $column_awal_tgl='E';
            $start_index_tgl=$column_awal_tgl;
            $position_awal_tgl=4;

            $column_index_tgl=[];
            $column_index_hari_libur_kerja=[];
            $column_index_hari_libur_nasional=[];
            foreach($list_tgl as $key_tgl => $item_tgl){
                $column_index_tgl[$item_tgl]=$column_awal_tgl;

                $tgl_format_tmp = new \DateTime($item_tgl);
                $tgl_format=$tgl_format_tmp->format('d/m');
                $hari_format=$tgl_format_tmp->format('D');

                $data_tgl[$key_tgl]=$tgl_format;
                $data_hari_e[$key_tgl]=$hari_format;
                $data_hari_indo[$key_tgl]=(new \App\Http\Traits\GlobalFunction)->hari($hari_format,1);

                $nm_hari=!empty($data_hari_indo[$key_tgl]) ? $data_hari_indo[$key_tgl] : '';

                if($key_tgl>=28){
                    $objPHPExcel->setActiveSheetIndex(0)->insertNewColumnBefore($column_awal_tgl);
                    $objPHPExcel->getActiveSheet()->getColumnDimension($column_awal_tgl)->setWidth(9);
                }
                $objPHPExcel->setActiveSheetIndex(0)->setCellValue($column_awal_tgl.$position_awal_tgl, $tgl_format);
                $objPHPExcel->setActiveSheetIndex(0)->setCellValue($column_awal_tgl.($position_awal_tgl+1), $nm_hari);
                $column_awal_tgl++;
            }

            $set_end_column_index=end($column_index_tgl);
            for ($i=0; $i < 1; $i++) {
                $set_end_column_index++;
            }

            $header_text="Rekap Absensi Karyawan Tahun ".$tahun." Bulan ".$bulan_text;
            $header_text.=" ".$get_nm_departemen;
            $header_text.=" ".$get_nm_ruangan;
            $header_1=strtoupper($header_text);
            $header_1_index='A';
            $header_1_posi=2;
            $objPHPExcel->setActiveSheetIndex(0)->setCellValue($header_1_index.$header_1_posi,$header_1 );
            $objPHPExcel->getActiveSheet()->mergeCells($header_1_index.$header_1_posi.':'.$set_end_column_index.$header_1_posi );

            $position_=6;
            $position_awal_=$position_;

            $list_departemen=[];
            $list_departemen_first=[];
            $list_ruangan=[];
            $list_ruangan_first=[];
            $list_status_karyawan=[];
            $list_status_karyawan_first=[];

            $jml_item=0;

            foreach($list_data as $key => $item){
                $jml_item=$key+1;

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
                $objPHPExcel->setActiveSheetIndex(0)->setCellValue('B'.$position_,$nm_id_karyawan  );
                
                $set_merge_='B'.$position_.':'.'D'.$position_;
                $objPHPExcel->getActiveSheet()->mergeCells($set_merge_);

                $data_presensi=!empty($item->presensi) ? (array)json_decode($item->presensi) : [];

                $type_jadwal_text='';
                if(!empty($item->ada_jadwal)){
                    if(!empty($item->jadwal_rutin)){
                        $type_jadwal_text='Rutin';
                    }
                    
                    if(!empty($item->jadwal_shift)){
                        $type_jadwal_text='Shift';
                    }
                }

                foreach($list_tgl as $key_tgl => $item_tgl){
                    if(!empty($column_index_tgl[$item_tgl])){
                        $column_me=$column_index_tgl[$item_tgl];

                        $presensi_user_text='';
                        $get_presensi_user=!empty($data_presensi[$item_tgl]) ? $data_presensi[$item_tgl] : '';
                        if(!empty($get_presensi_user)){
                            $presensi_user=!empty($get_presensi_user->presensi) ? $get_presensi_user->presensi : [];
                            $presensi_user_text=implode(','.PHP_EOL,$presensi_user);
                        }
                        
                        $objPHPExcel->setActiveSheetIndex(0)->setCellValue($column_me.$position_,$presensi_user_text );
                    }
                }
                $end_index_tgl=end($column_index_tgl);
                $end_index_tgl++;
                $objPHPExcel->setActiveSheetIndex(0)->setCellValue($end_index_tgl.$position_, $type_jadwal_text );

                $position_++;
            }

            $position_--;

            $end_position=$position_;

            $objPHPExcel->getActiveSheet()->getStyle($start_index_tgl.$position_awal_tgl.":".$end_index_tgl.$end_position)->getAlignment()
            ->setWrapText(true)
            ->setVertical(\PhpOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_CENTER)
            ->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER)
            ;

            $cell_border="A".$position_awal_.':'.$end_index_tgl.$end_position;
            $objPHPExcel->getActiveSheet()->getStyle($cell_border)->getBorders()->getAllBorders()->setBorderStyle(\PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN);

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

        die;


    }
}