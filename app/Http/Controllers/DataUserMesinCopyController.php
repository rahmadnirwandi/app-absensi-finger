<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

use App\Services\GlobalService;
use App\Services\UserMesinTmpService;
use App\Services\RefUserInfoService;

class DataUserMesinCopyController extends \App\Http\Controllers\MyAuthController
{

    public $part_view, $url_index, $url_name, $title, $breadcrumbs, $globalService;
    public $userMesinTmpService,$refUserInfoService;

    public function __construct()
    {
        $router_name = (new \App\Http\Traits\GlobalFunction)->getRouterIndex();
        $this->part_view = $router_name->path_base;
        $this->url_index = $router_name->uri;
        $this->url_name = $router_name->router_name;

        $this->title = 'Copy Data User ke Mesin';
        $this->breadcrumbs = [
            ['title' => 'Mesin Absensi', 'url' => url('/') . "/sub-menu?type=5"],
            ['title' => $this->title, 'url' => url('/') . "/" . $this->url_index],
        ];

        $this->globalService = new GlobalService;
        $this->userMesinTmpService = new UserMesinTmpService;
        $this->refUserInfoService = new RefUserInfoService;
    }

    function actionIndex(Request $request){

        $parameter_view = [
            'title' => $this->title,
            'breadcrumbs' => $this->breadcrumbs,
        ];

        if($request->isMethod('post')){
            if($request->get('proses')=='1'){
                return $this->proses($request);
            }
        }

        return view($this->part_view . '.index', $parameter_view);
    }

    // private function proses($request){
    //     $req = $request->all();
    //     dd($req);
    //     $id_mesin_tujuan = !empty($req['id_mesin_tujuan']) ? $req['id_mesin_tujuan'] : '';
        
    //     $pesan = [];
    //     $link_back_param = [];
    //     $message_default = [
    //         'success' => 'Data berhasil disinkronisasi',
    //         'error' => 'Maaf Data tidak berhasil disinkronisasi'
    //     ];

    //     try {

    //         $item_list_user=!empty($req['item_list_terpilih']) ? $req['item_list_terpilih'] : '';
    //         $item_list_user=!empty($item_list_user) ? json_decode($item_list_user,true) : [];

    //         if(empty($item_list_user)){
    //             return redirect()->route($this->url_name, $link_back_param)->with(['error'=>'List User Tidak ada']);
    //         }

    //         $data_mesin_tujuan=(new \App\Models\RefMesinAbsensi)->where(['id_mesin_absensi'=>$id_mesin_tujuan])->first();
    //         if(empty($data_mesin_tujuan)){
    //             return redirect()->route($this->url_name, $link_back_param)->with(['error'=>'Data Mesin Tujuan Tidak ditemukan']);
    //         }

    //         $model_mesin_tujuan=(new \App\Services\MesinFinger($data_mesin_tujuan->ip_address));
    //         $connect_tujuan=$model_mesin_tujuan->connect();
    //         $connect_tujuan=!empty($connect_tujuan[2]) ? $connect_tujuan[2] : '';
    //         if($connect_tujuan==2){
    //             return redirect()->route($this->url_name, [])->with(['error' => 'Maaf Mesin tujuan tidak connect']);
    //         }

    //         // $set_waktu=$model_mesin_tujuan->set_waktu_mesin();
    //         // $check_hasil=!empty($set_waktu[1]) ? $set_waktu[1] : '';
    //         // if($check_hasil==2){
    //         //     return redirect()->route($this->url_name, [])->with(['error' => 'Maaf Mesin tidak dapat di set waktu']);
    //         // }

    //         // $get_user_mesin_tujuan=$model_mesin_tujuan->get_user();
    //         // $check_hasil=!empty($get_user_mesin_tujuan[0]) ? $get_user_mesin_tujuan[0] : '';
    //         // if($check_hasil!='error'){
    //         //     $get_user_mesin_tujuan=json_decode($get_user_mesin_tujuan);
    //         //     foreach($get_user_mesin_tujuan as $value){
    //         //         $model_mesin_tujuan->delete_finger_to_mesin($value);
    //         //         $model_mesin_tujuan->delete_user_to_mesin($value);
    //         //     }
    //         // }
            
    //         $jml_user=0;
    //         $jml_user_detail=0;
    //         $jml_user_exist=0;
    //         foreach($item_list_user as $key_u => $user){
    //             $user['id_user']=$key_u;

    //             $get_user_exist=$model_mesin_tujuan->get_user($key_u);
                
    //             $check_get_user_exist=0;
    //             if(!empty($get_user_exist)){
    //                 if(strtolower( $get_user_exist[0] )==strtolower( 'error') ){
    //                     $check_get_user_exist=1;
    //                 }
    //             }
                
    //             if(!empty($check_get_user_exist)){

    //                 $get_user=( new \App\Models\RefUserInfo() )->where('id_user','=', $key_u)->first();
    //                 $user=(object)$get_user->getAttributes();

    //                 $hasil_user=$model_mesin_tujuan->upload_user_to_mesin($user);
    //                 $check_hasil=!empty($hasil_user[1]) ? $hasil_user[1] : '';
    //                 if($check_hasil==1){
    //                     $jml_user++;

    //                     $get_user_detail=( new \App\Models\RefUserInfoDetail() )->where('id_user','=', $user->id_user)->orderBy('id_user','ASC')->get();
    //                     if(!empty($get_user_detail[0])){
    //                         foreach($get_user_detail as $user_detail){
    //                             $hasil_user=$model_mesin_tujuan->upload_user_finger_to_mesin($user_detail);
    //                             $check_hasil=!empty($hasil_user[1]) ? $hasil_user[1] : '';
    //                             if($check_hasil==1){
    //                                 $model_mesin_tujuan->refresh_db_mesin();
    //                                 $jml_user_detail++;
    //                             }
    //                         }
    //                     }
    //                 }
    //             }else{
    //                 $jml_user_exist++;
    //             }
    //         }
            
    //         $is_save = 0;
    //         if(!empty($jml_user)){
    //             $is_save=1;
    //         }

    //         if ($is_save) {
    //             $link_back_param = $this->clear_request($link_back_param, $request);
    //             if(!empty($jml_user_exist)){
    //                 $message_default['success']=$message_default['success'].' ada '.$jml_user_exist.' memiliki ID USER yang sama dan '.$jml_user.' user berhasil di proses';
    //             }
    //             $pesan = ['success', $message_default['success'], 2];
    //         } else {
    //             $pesan = ['error', $message_default['error'], 3];
    //         }
    //     } catch (\Illuminate\Database\QueryException $e) {
    //         if ($e->errorInfo[1] == '1062') {
    //         }
    //         $pesan = ['error', $message_default['error'], 3];
    //     } catch (\Throwable $e) {
    //         $pesan = ['error', $message_default['error'], 3];
    //     }

    //     return redirect()->route($this->url_name, $link_back_param)->with([$pesan[0] => $pesan[1]]);
    // }

    private function proses($request){

    $req = $request->all();

    Log::info('=== START PROSES SINKRON KE MESIN TUJUAN ===', [
        'request' => $req,
        'time' => now()
    ]);

    $id_mesin_tujuan = $request->input('id_mesin_tujuan');

    if(empty($id_mesin_tujuan)){
        Log::warning('ID mesin tujuan kosong');
        return back()->with(['error'=>'ID Mesin tujuan kosong']);
    }

    try {

        $item_list_user = $request->input('item_list_terpilih');
        $item_list_user = !empty($item_list_user) ? json_decode($item_list_user,true) : [];

        if(empty($item_list_user)){
            Log::warning('List user kosong');
            return redirect()->route($this->url_name)->with(['error'=>'List User Tidak ada']);
        }

        Log::info('Total user diproses', [
            'total' => count($item_list_user)
        ]);

        $data_mesin_tujuan = (new \App\Models\RefMesinAbsensi)
                                ->where(['id_mesin_absensi'=>$id_mesin_tujuan])
                                ->first();

        if(empty($data_mesin_tujuan)){
            Log::error('Mesin tujuan tidak ditemukan', [
                'id_mesin_tujuan'=>$id_mesin_tujuan
            ]);
            return redirect()->route($this->url_name)->with(['error'=>'Data Mesin Tujuan Tidak ditemukan']);
        }

        Log::info('Koneksi ke mesin tujuan', [
            'ip'=>$data_mesin_tujuan->ip_address
        ]);

        $model_mesin_tujuan = new \App\Services\MesinFinger($data_mesin_tujuan->ip_address);

        $connect_tujuan = $model_mesin_tujuan->connect();

        if(is_array($connect_tujuan) && isset($connect_tujuan[2]) && $connect_tujuan[2]==2){
            Log::error('Mesin tujuan tidak connect');
            return redirect()->route($this->url_name)->with(['error' => 'Maaf Mesin tujuan tidak connect']);
        }

        $jml_user=0;
        $jml_user_detail=0;
        $jml_user_exist=0;

        foreach($item_list_user as $key_u => $user){

            Log::info('Proses upload user', [
                'id_user'=>$key_u
            ]);

            $get_user_exist = $model_mesin_tujuan->get_user($key_u);

            $check_get_user_exist=0;
            if(!empty($get_user_exist)){
                if(strtolower($get_user_exist[0]) == 'error'){
                    $check_get_user_exist=1;
                }
            }

            if(!empty($check_get_user_exist)){

                $get_user = (new \App\Models\RefUserInfo())
                                ->where('id_user','=', $key_u)
                                ->first();

                if(empty($get_user)){
                    Log::warning('User tidak ditemukan di DB lokal', [
                        'id_user'=>$key_u
                    ]);
                    continue;
                }

                $user=(object)$get_user->getAttributes();

                $hasil_user = $model_mesin_tujuan->upload_user_to_mesin($user);

                $check_hasil = $hasil_user[1] ?? '';

                if($check_hasil==1){

                    $jml_user++;

                    Log::info('User berhasil diupload', [
                        'id_user'=>$key_u
                    ]);

                    $get_user_detail = (new \App\Models\RefUserInfoDetail())
                                            ->where('id_user','=', $user->id_user)
                                            ->orderBy('id_user','ASC')
                                            ->get();

                    foreach($get_user_detail as $user_detail){

                        $hasil_finger = $model_mesin_tujuan->upload_user_finger_to_mesin($user_detail);

                        $check_hasil = $hasil_finger[1] ?? '';

                        if($check_hasil==1){
                            $jml_user_detail++;
                        }else{
                            Log::error('Gagal upload finger', [
                                'id_user'=>$key_u,
                                'finger_id'=>$user_detail->finger_id ?? null
                            ]);
                        }
                    }

                }else{
                    Log::error('Gagal upload user ke mesin', [
                        'id_user'=>$key_u,
                        'response'=>$hasil_user
                    ]);
                }

            }else{
                $jml_user_exist++;
                Log::warning('User sudah ada di mesin tujuan', [
                    'id_user'=>$key_u
                ]);
            }
        }

        Log::info('=== SUMMARY PROSES ===', [
            'user_berhasil'=>$jml_user,
            'finger_berhasil'=>$jml_user_detail,
            'user_exist'=>$jml_user_exist
        ]);

        if($jml_user > 0){

            $message = 'Data berhasil disinkronisasi';

            if($jml_user_exist > 0){
                $message .= ' ada '.$jml_user_exist.' memiliki ID USER yang sama dan '.$jml_user.' user berhasil di proses';
            }

            return redirect()->route($this->url_name)
                ->with(['success'=>$message]);
        }

        return redirect()->route($this->url_name)
            ->with(['error'=>'Maaf Data tidak berhasil disinkronisasi']);

    } catch (\Throwable $e) {

        Log::error('ERROR PROSES SINKRON KE MESIN TUJUAN', [
            'message'=>$e->getMessage(),
            'line'=>$e->getLine(),
            'file'=>$e->getFile(),
            'trace'=>$e->getTraceAsString()
        ]);

        return redirect()->route($this->url_name)
            ->with(['error'=>'Terjadi kesalahan sistem']);
    }
}


    function getListUser(Request $request){

        $hasil_data=[];
        try{

            $filter_kd_jenis='';
            $form_filter_text='';
            $search = !empty($request->search) ? $request->search : '';
            $filter=!empty($search['value']) ? $search['value'] : '';
            if($filter){
                $filter=json_decode($filter);
                if(!empty($filter)){
                    foreach($filter as $value){
                        if( trim(strtolower($value->name))=='filter_kd_jenis' ){
                            $filter_kd_jenis=$value->value;
                        }
                        if( trim(strtolower($value->name))=='form_filter_text' ){
                            $form_filter_text=$value->value;
                        }
                    }
                }
            }

            $start_page=!empty($request->start) ? $request->start : 0;
            $end_page=!empty($request->end) ? $request->end : 10;

            $paramater=[
                'search'=>$form_filter_text
            ];
            
            // if(!empty($filter_kd_jenis)){
            //     $paramater['jenis']=$filter_kd_jenis;
            // }

            // $paramater=[];
            // $data_tmp=( new \App\Services\BarangNonMedisService )->getBarangList($paramater,1)->where("ipsrsbarang.stok", ">", "0")->offset($start_page)->limit($end_page)->get();
            $data_tmp=$this->refUserInfoService->getList($paramater, 1)
            ->orderBy(DB::raw("CONVERT ( REPLACE (utama.id_user, '-', '' ), UNSIGNED INTEGER )"),'ASC')
            // ->orderBy(DB::raw('nm_karyawan'),'ASC')
            ->offset($start_page)->limit($end_page)->get();
            
            $data_count=$this->refUserInfoService->getList($paramater, 1)->count();
            if(!empty($data_tmp)){
                foreach($data_tmp as $item){

                    $get_privil=(new \App\Models\RefUserInfo())->get_privilege($item->privilege);

                    $nama_karyawan="<span style='color:RED'>Belum Disetting</span>";
                    if(!empty($item->status_id_user)){
                        $nama_karyawan="<span style='color:#128628'>".$item->nm_karyawan."</span>";
                    }

                    $div_un='';
                    $div_un.="<span>( ".( !empty($item->id_user) ? $item->id_user : '' )." )</span>";
                    $div_un.="<span> ".( !empty($item->name) ? $item->name : '' )."</span>";

                    $div_gp="<span>".( !empty($item->group) ? $item->group : '' )."</span>/<span>".$get_privil."</span>";

                    $data_json=[
                        $div_un,
                        $div_gp,
                        ( !empty($item->id_user) ? $item->id_user : '' ),
                        ( !empty($item->name) ? $item->name : '' ),
                        ( !empty($item->group) ? $item->group : '' ),
                        (!empty($item->nm_karyawan) ? $item->nm_karyawan : '' ),
                        !empty($item->nm_departemen) ? $item->nm_departemen : '',
                        !empty($item->nm_ruangan) ? $item->nm_ruangan : '',
                    ];

                    $data_json=json_encode($data_json);
                    
                    $data=[
                        $div_un,
                        $div_gp,
                        $nama_karyawan,
                        !empty($item->nm_departemen) ? $item->nm_departemen : '',
                        !empty($item->nm_ruangan) ? $item->nm_ruangan : '',
                        "<input class='form-check-input hover-pointer checked_b' style='border-radius: 0px;' type='checkbox' data-me='".$data_json."' data-kode='" . $item->id_user . "'>",
                    ];
                    $hasil_data[]=$data;
                }
            }

        } catch (\Throwable $e) {
            $hasil_data=[];
        }

        return [
            'data'=>!empty($hasil_data) ? $hasil_data : [],
            'recordsTotal'=>!empty($data_count) ? $data_count : 0,
            'recordsFiltered'=>!empty($data_count) ? $data_count : 0,
        ];
    }

    function ajax(Request $request){
        $get_req = $request->all();
        if(!empty($get_req['action'])){
            if($get_req['action']=='list_user'){
                $hasil=$this->getListUser($request);
                if($request->ajax()){
                    $return='';
                    if($hasil){
                        $return =json_encode($hasil);
                    }
                    return $return;
                    // return response()->json(array('success' => true, 'content'=>$hasil));
                }
                return $hasil;
            }
        }
    }
}