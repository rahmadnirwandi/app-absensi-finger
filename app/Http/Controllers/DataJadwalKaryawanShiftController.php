<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

use App\Services\GlobalService;

class DataJadwalKaryawanShiftController extends \App\Http\Controllers\MyAuthController
{
    public $part_view, $url_index, $url_name, $title, $breadcrumbs, $globalService;
    public $refKaryawanService;

    public function __construct()
    {
        $router_name = (new \App\Http\Traits\GlobalFunction)->getRouterIndex();
        $this->part_view = $router_name->path_base;
        $this->url_index = $router_name->uri;
        $this->url_name = $router_name->router_name;

        $this->title = 'Atur Waktu Jadwal Karyawan';
        $this->breadcrumbs = [
            ['title' => 'Data Karyawan', 'url' => url('/') . "/sub-menu?type=4"],
            ['title' => 'Jadwal Karyawan', 'url' => url('/') . "/data-jadwal-karyawan"],
            ['title' => $this->title, 'url' => url('/') . "/" . $this->url_index],
        ];

        $this->globalService = new GlobalService;
    }

    function actionIndex(Request $request)
    {
        $req = $request->all();
        $data_sent = !empty($req['data_sent']) ? $req['data_sent'] : '';
        $params = !empty($req['params']) ? $req['params'] : '';
        $params=json_decode($params);
        $exp=explode('@',$data_sent);
        $id_karyawan=!empty($exp[0]) ? $exp[0] : 0;
        $id_template_jadwal_shift=!empty($exp[1]) ? $exp[1] : 0;

        if($request->isMethod('post')){
            return $this->proses($request);
        }

        $breadcrumbs=$this->breadcrumbs;
        array_push($breadcrumbs,['title' => 'Atur Waktu Jadwal Karyawan', 'url' => url('/') . "/" . $this->url_index]);

        $paramater = [
            'id_karyawan' => $id_karyawan
        ];
        $data_karyawan = (new \App\Services\RefKaryawanService)->getList($paramater, 1)->first();

        $url_back_index=(new \App\Http\Traits\GlobalFunction)->set_paramter_url('data-jadwal-karyawan',$params);

        $filter_tahun_bulan=!empty($request->filter_tahun_bulan) ? $request->filter_tahun_bulan : date('Y-m');
        
        $export_date=new \DateTime($filter_tahun_bulan);
        $tahun_filter=$export_date->format('Y');
        $bulan_filter=$export_date->format('m');

        $get_tgl_per_bulan=(new \App\Http\Traits\AbsensiFunction)->get_tgl_per_bulan($filter_tahun_bulan);
        $list_tgl=!empty($get_tgl_per_bulan->list_tgl) ? $get_tgl_per_bulan->list_tgl : [];

        $model_shift=(new \App\Models\RefTemplateJadwalShift)->where(['id_template_jadwal_shift'=>$id_template_jadwal_shift])->first();

        $parameter=[
            'id_template_jadwal_shift'=>$id_template_jadwal_shift,
            'tahun'=>$tahun_filter,
            'bulan'=>$bulan_filter,
        ];

        $get_list_shift=(new \App\Services\DataPresensiService)->setListShift($parameter);
        $list_shift=!empty($get_list_shift->data) ? json_decode($get_list_shift->data,true)  : [];

        $paramater=[
            'id_template_jadwal_shift'=>$id_template_jadwal_shift,
        ];

        $list_data_jadwal = (new \App\Services\RefTemplateJadwalShiftService)->getListJadwalTemplate($paramater);

        $list_data_jadwal_karyawan_sendiri=(new \App\Models\RefKaryawanJadwalShiftWaktu)
        ->where(['id_karyawan'=>$id_karyawan,'id_template_jadwal_shift'=>$id_template_jadwal_shift])
        ->whereYear('tanggal', '=', $tahun_filter)
        ->whereMonth('tanggal', '=', $bulan_filter)
        ->get();

        $list_data_jadwal_sendiri=[];
        if(!empty($list_data_jadwal_karyawan_sendiri)){
            foreach($list_data_jadwal_karyawan_sendiri as $item){
                $list_data_jadwal_sendiri[$id_karyawan][$item->tanggal]=$item->data_item;
            }
        }

        $list_data_jadwal_library=[];
        if(!empty($list_data_jadwal_sendiri)){
            foreach($list_data_jadwal as $item){
                $kode=$item->type_jadwal.'@'.$item->id_jenis_jadwal;
                $list_data_jadwal_library[$kode]=$item;
            }
        }

        $parameter_view = [
            'title'=>$this->title,
            'breadcrumbs' => $this->breadcrumbs,
            'url_back_index' => $url_back_index,
            'data_sent'=>$data_sent,
            'params_json'=>json_encode($params),
            'data_karyawan' => $data_karyawan,
            'list_tgl'=>$list_tgl,
            'model_shift'=>$model_shift,
            'list_shift'=>$list_shift,
            'list_data_jadwal'=>$list_data_jadwal,
            'list_data_jadwal_library'=>$list_data_jadwal_library,
            'list_data_jadwal_sendiri'=>$list_data_jadwal_sendiri,
        ];

        return view($this->part_view . '.index', $parameter_view);

    }

    private function proses($request){
        $req = $request->all();
        $data_sent=!empty($req['data_sent']) ? $req['data_sent'] : '';
        $params=!empty($req['params']) ? $req['params'] : '';
        $link_back_redirect = $this->url_name;
        
        $link_back_param = [
            'data_sent' => $data_sent,
            'params' => $params,
            // 'filter_tahun_bulan'=>$req['filter_tahun_bulan']
        ];

        $message_default = [
            'success' => 'Data berhasil diubah',
            'error' => 'Data tidak berhasil diubah'
        ];

        DB::beginTransaction();

        try {
            $exp=explode('@',$data_sent);
            $id_karyawan=!empty($exp[0]) ? $exp[0] : 0;
            $id_template_jadwal_shift=!empty($exp[1]) ? $exp[1] : 0;
            $tanggal_ubah=!empty($req['tgl_ubah']) ? $req['tgl_ubah'] : '';
            $shift_sistem_tmp=!empty($req['jadwal_terpilih_sistem']) ? $req['jadwal_terpilih_sistem'] : '';
            $shift_terpilih_tmp=!empty($req['jadwal_terpilih_sendiri']) ? $req['jadwal_terpilih_sendiri'] : '';

            $shift_sistem=json_decode($shift_sistem_tmp);
            $shift_terpilih=json_decode($shift_terpilih_tmp);

            $get_check_data = (new \App\Models\RefKaryawanJadwalShiftWaktu)->where([
                'id_karyawan'=>$id_karyawan,
                'id_template_jadwal_shift'=>$id_template_jadwal_shift,
                'tanggal'=>$tanggal_ubah,
            ])->delete();

            if($shift_sistem_tmp==$shift_terpilih_tmp){
                DB::commit();
                return redirect()->route($link_back_redirect, $link_back_param)->with([ 'success' => $message_default['success']]);
            }

            $data_list=[];
            $data_list_short=[];
            if(!empty($shift_terpilih->item)){
                foreach($shift_terpilih->item as $value){
                    $exp=explode('@',$value);
                    $type_jadwal=!empty($exp[0]) ? $exp[0] : 0;
                    $id_jenis_jadwal=!empty($exp[1]) ? $exp[1] : 0;

                    if($type_jadwal==2){
                        $data_list['']=[
                            'id_jenis_jadwal'=>$id_jenis_jadwal,
                            'type'=>$type_jadwal
                        ];
                        $data_list_short['']=$type_jadwal.'@'.$id_jenis_jadwal;
                    }

                    if($type_jadwal==1){
                        if(!empty($id_jenis_jadwal)){
                            $data_jenis_jadwal=(new \App\Models\RefJenisJadwal)->where(['id_jenis_jadwal'=>$id_jenis_jadwal])->select('masuk_kerja')->first();
                            if(!empty($data_jenis_jadwal)){

                                $data_list[$data_jenis_jadwal->masuk_kerja]=[
                                    'id_jenis_jadwal'=>$id_jenis_jadwal,
                                    'type_jenis'=>$type_jadwal,
                                ];

                                $data_list_short[$data_jenis_jadwal->masuk_kerja]=$type_jadwal.'@'.$id_jenis_jadwal;
                            }
                            
                        }
                    }
                }
            }
            
            $is_save=0;
            if(!empty($data_list)){
                ksort($data_list);
                ksort($data_list_short);
                $data_list = array_values($data_list);
                $data_list_short = array_values($data_list_short);
    
                $data_save=[
                    'id_karyawan'=>$id_karyawan,
                    'id_template_jadwal_shift'=>$id_template_jadwal_shift,
                    'tanggal'=>$tanggal_ubah,
                    'data'=>json_encode($data_list),
                    'data_item'=>json_encode(['item'=>$data_list_short]),
                ];

                $model = (new \App\Models\RefKaryawanJadwalShiftWaktu);
                $model->set_model_with_data($data_save);
                if($model->save()){
                    $is_save=1;
                }
            }else{
                $is_save=1;
            }

            if ($is_save) {
                DB::commit();
                $pesan = ['success', $message_default['success'], 2];
            } else {
                DB::rollBack();
                $pesan = ['error', $message_default['error'], 3];
            }

        } catch (\Illuminate\Database\QueryException $e) {
            DB::rollBack();
            if ($e->errorInfo[1] == '1062') {
            }
            $pesan = ['error', $message_default['error'], 3];
        } catch (\Throwable $e) {
            DB::rollBack();
            $pesan = ['error', $message_default['error'], 3];
        }
        return redirect()->route($link_back_redirect, $link_back_param)->with([$pesan[0] => $pesan[1]]);

    }
}