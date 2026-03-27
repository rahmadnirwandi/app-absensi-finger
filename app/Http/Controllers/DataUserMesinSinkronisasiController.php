<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

use App\Services\GlobalService;
use App\Services\UserMesinTmpService;
use Illuminate\Support\Facades\Log;


class DataUserMesinSinkronisasiController extends \App\Http\Controllers\MyAuthController
{

    public $part_view, $url_index, $url_name, $title, $breadcrumbs, $globalService;
    public $userMesinTmpService;

    public function __construct()
    {
        $router_name = (new \App\Http\Traits\GlobalFunction)->getRouterIndex();
        $this->part_view = $router_name->path_base;
        $this->url_index = $router_name->uri;
        $this->url_name = $router_name->router_name;

        $this->title = 'Sikronisasi Data Mesin & Database';
        $this->breadcrumbs = [
            ['title' => 'Mesin Absensi', 'url' => url('/') . "/sub-menu?type=5"],
            ['title' => $this->title, 'url' => url('/') . "/" . $this->url_index],
        ];

        $this->globalService = new GlobalService;
        $this->userMesinTmpService = new UserMesinTmpService;
    }

    function actionIndex(Request $request){

        if($request->isMethod('post')){
            return $this->proses_data($request);
        }

        $paramater_search=[];
        if(!empty($request->searchbymesin)){
            $id_mesin_absensi = !empty($request->filter_id_mesin) ? $request->filter_id_mesin : '';
            if(!empty($id_mesin_absensi)){
                $data_mesin=(new \App\Models\RefMesinAbsensi)->where(['id_mesin_absensi'=>$id_mesin_absensi])->first();
            }

            if(!empty($data_mesin)){
                $mesin=(new \App\Services\MesinFinger($data_mesin->ip_address));
                $connect=$mesin->connect();
                $connect=!empty($connect[2]) ? $connect[2] : '';
                if($connect==2){
                    return redirect()->route($this->url_name, [])->with(['error' => 'Maaf Mesin tidak connect']);
                }

                $hasil=$this->proses_tmp($data_mesin);
                $check_hasil=!empty($hasil[0]) ? $hasil[0] : '';
                if($check_hasil=='error'){
                    return redirect()->route($this->url_name, [])->with(['error' => $hasil[1]]);
                }
            }
        }
        
        if(!empty($request->searchbydb)){
            $form_filter_text = !empty($request->form_filter_text) ? $request->form_filter_text : '';
            $filter_id_mesin_asal = !empty($request->filter_id_mesin_asal) ? $request->filter_id_mesin_asal : '';
            $filter_simpan_db = !empty($request->filter_simpan_db) ? $request->filter_simpan_db : '';
            $filter_duplicate_data = !empty($request->filter_duplicate_data) ? $request->filter_duplicate_data : '';

            $paramater_search = [
                'search' => $form_filter_text
            ];

            $id_mesin_absensi = !empty($filter_id_mesin_asal) ? $filter_id_mesin_asal : '';
            if(!empty($id_mesin_absensi)){
                $data_mesin=(new \App\Models\RefMesinAbsensi)->where(['id_mesin_absensi'=>$id_mesin_absensi])->first();
                $hasil_ip_asal=!empty($data_mesin->ip_address) ? $data_mesin->ip_address : '';
                $paramater_search['id_mesin_absensi']=$id_mesin_absensi;
            }

            if(!empty($filter_simpan_db)){
                $hasil=0;
                if($filter_simpan_db==1){
                    $hasil=1;
                }
                $paramater_search['db']=$hasil;
            }

            if(!empty($filter_duplicate_data)){
                $hasil=0;
                if($filter_duplicate_data==2){
                    $hasil=1;
                }
                $paramater_search['where_and_or']=['duplicate_id'=>$hasil,'duplicate_name'=>$hasil ];
            }
        }

        $list_data = $this->userMesinTmpService->getList($paramater_search, 1)
        ->orderBy(DB::raw("CONVERT ( REPLACE (id_user, '-', '' ), UNSIGNED INTEGER )"),'ASC')
        ->paginate(!empty($request->per_page) ? $request->per_page : 30);

        $parameter_view = [
            'title' => $this->title,
            'breadcrumbs' => $this->breadcrumbs,
            'list_data' => $list_data,
            'data_mesin'=> !empty($data_mesin) ? $data_mesin : [],
            'hasil_ip_asal'=>!empty($hasil_ip_asal) ? $hasil_ip_asal : '',
        ];

        return view($this->part_view . '.index', $parameter_view);
    }

    private function proses_tmp($data_mesin){
        DB::beginTransaction();
        $pesan = [];
        $message_default = [
            'success' => !empty($kode) ? 'Data berhasil diubah' : 'Data berhasil disimpan',
            'error' => !empty($kode) ? 'Data tidak berhasil diubah' : 'Data berhasil disimpan'
        ];

        try {

            $is_save = 0;
            $mesin=(new \App\Services\MesinFinger($data_mesin->ip_address));
            $get_user=$mesin->get_user_tad();

            $check_hasil=!empty($get_user[0]) ? $get_user[0] : '';
            if($check_hasil=='error'){
                return ['error',$get_user[1]];
            }
            if($get_user){
                $get_user=json_decode($get_user);

                (new \App\Models\UserMesinTmp)->truncate();

                $jml_save=0;
                foreach($get_user as $value){
                    $model = (new \App\Models\UserMesinTmp);

                    $model->id_mesin_absensi = $data_mesin->id_mesin_absensi;
                    $model->id_user = !empty($value->PIN2) ? $value->PIN2 : '';
                    $model->name = !empty($value->Name) ? trim( (new \App\Http\Traits\GlobalFunction)->remove_special_char_json($value->Name)) : '';
                    $model->group = !empty($value->Group) ? $value->Group : '';
                    $model->privilege = !empty($value->Privilege) ? $value->Privilege : '';
                    $model->pin = !empty($value->PIN) ? $value->PIN : '';
                    $model->pin2 = !empty($value->PIN2) ? $value->PIN2 : '';

                    if ($model->save()) {
                        $jml_save++;
                    }
                }

                if($jml_save>0){
                    $is_save = 1;
                }
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

        return $pesan;
    }

    // function actionSinkron(Request $request){
    //     ini_set("memory_limit","800M");
    //     set_time_limit(0);

    //     Log::info('REQUEST DATA', $request->all());


    //     $req = $request->all();
    //     $id_mesin_absensi = !empty($req['key']) ? $req['key'] : '';
    //     DB::beginTransaction();
    //     $pesan = [];
    //     $link_back_redirect=$this->url_name;
    //     $link_back_param = ['filter_id_mesin' => $id_mesin_absensi];
    //     $message_default = [
    //         'success' => 'Data berhasil di proses',
    //         'error' => 'Data tidak berhasil di proses'
    //     ];

    //     try {
    //         $data_mesin=(new \App\Models\RefMesinAbsensi)->where(['id_mesin_absensi'=>$id_mesin_absensi])->first();

    //         $mesin=(new \App\Services\MesinFinger($data_mesin->ip_address));
    //         // $get_user=$mesin->get_user();
    //         $get_user=$mesin->get_user_tad();
    //         if($get_user){
    //             $get_user=json_decode($get_user);
    //             $jml_save_user=0;
    //             $jml_save_finger=0;
    //             foreach($get_user as $value_user){
    //                 $data_item=[];

    //                 $data_item=[
    //                     'name'=> !empty($value_user->Name) ? trim( (new \App\Http\Traits\GlobalFunction)->remove_special_char_json($value_user->Name)) : '',
    //                     'password'=>!empty($value_user->Password) ? $value_user->Password : '',
    //                     'group'=>!empty($value_user->Group) ? $value_user->Group : '',
    //                     'privilege'=>!empty($value_user->Privilege) ? $value_user->Privilege : '',
    //                     'card'=>!empty($value_user->Card) ? $value_user->Card : '',
    //                     'pin'=>!empty($value_user->pin) ? $value_user->PIN : '',
    //                     'pin2'=>!empty($value_user->PIN2) ? $value_user->PIN2 : '',
    //                     'tz1'=>!empty($value_user->TZ1) ? $value_user->TZ1 : '',
    //                     'tz2'=>!empty($value_user->TZ2) ? $value_user->TZ2 : '',
    //                     'tz3'=>!empty($value_user->TZ3) ? $value_user->TZ3 : '',
    //                 ];

    //                 // $model_user=(new \App\Models\RefUserInfo())->where(['id_user'=>$value_user->id])->first();
    //                 $model_user=(new \App\Models\RefUserInfo())->where(['id_user'=>$value_user->PIN2])->first();
    //                 if(empty($model_user)){
    //                     $model_user=(new \App\Models\RefUserInfo());
    //                     $model_user->id_user=$value_user->PIN2;
    //                 }
    //                 $model_user->set_model_with_data($data_item);
    //                 if($model_user->save()){

    //                     // $get_user_finger=$mesin->get_user_tamplate($value_user->pin);
    //                     $get_user_finger=$mesin->get_user_tamplate_tad($value_user->PIN2);
                        
    //                     if($get_user_finger){
    //                         $get_user_finger=json_decode($get_user_finger);

    //                         (new \App\Models\RefUserInfoDetail())->where(['id_user'=>$value_user->PIN2])->delete();
    //                         foreach($get_user_finger as $value_finger){

    //                             if(!empty($value_finger->PIN) && !empty($value_finger->FingerID) && !empty($value_finger->Template) ){
    //                                 $data_item=[
    //                                     'id_user'=>!empty($value_finger->PIN) ? $value_finger->PIN : '',
    //                                     'finger_id'=>!empty($value_finger->FingerID) ? $value_finger->FingerID : '',
    //                                     'size'=>!empty($value_finger->Size) ? $value_finger->Size : '',
    //                                     'valid'=>!empty($value_finger->Valid) ? $value_finger->Valid : '',
    //                                     'finger'=>!empty($value_finger->Template) ? $value_finger->Template : '',
    //                                     'pin'=>!empty($value_finger->PIN) ? $value_finger->PIN : '',
    //                                 ];

    //                                 $model_finger=(new \App\Models\RefUserInfoDetail());
    //                                 $model_finger->id_user=$value_user->PIN2;

    //                                 $model_finger->set_model_with_data($data_item);

    //                                 if($model_finger->save()){
    //                                     $jml_save_user++;
    //                                 }
    //                             }
    //                         }
    //                     }
    //                 }
    //             }

    //             $is_save=0;
    //             if($jml_save_user>0){
    //                 $is_save=1;
    //             }

    //             if ($is_save) {
    //                 DB::commit();
    //                 $pesan = ['success', $message_default['success'], 2];
    //             }else{
    //                 DB::rollBack();
    //                 $pesan = ['error', $message_default['error'], 3];
    //             }
    //         }else{
    //             $pesan = ['success', 'Tidak ada yang di proses' , 2];
    //         }
    //     } catch (\Illuminate\Database\QueryException $e) {
    //         DB::rollBack();
    //         if ($e->errorInfo[1] == '1062') {
    //         }
    //         $pesan = ['error', $message_default['error'], 3];
    //     } catch (\Throwable $e) {
    //         DB::rollBack();
    //         $pesan = ['error', $message_default['error'], 3];
    //     }
    //     return redirect()->route($link_back_redirect, $link_back_param)->with([$pesan[0] => $pesan[1]]);
    // }

    function actionSinkron(Request $request){
        ini_set("memory_limit","800M");
        set_time_limit(0);

        $req = $request->all();
        $id_mesin_absensi = !empty($req['key']) ? $req['key'] : '';

        Log::info('=== START SINKRON MESIN ===', [
            'id_mesin' => $id_mesin_absensi,
            'request_time' => now()
        ]);

        DB::beginTransaction();

        try {

            $data_mesin = (new \App\Models\RefMesinAbsensi)
                            ->where(['id_mesin_absensi'=>$id_mesin_absensi])
                            ->first();

            if(!$data_mesin){
                Log::error('Mesin tidak ditemukan', ['id_mesin'=>$id_mesin_absensi]);
                throw new \Exception('Mesin tidak ditemukan');
            }

            Log::info('Koneksi ke mesin', [
                'ip' => $data_mesin->ip_address
            ]);

            $mesin = new \App\Services\MesinFinger($data_mesin->ip_address);

            $get_user = $mesin->get_user_tad();

            if(!$get_user){
                Log::warning('Tidak ada data user dari mesin');
                return back()->with(['success' => 'Tidak ada yang di proses']);
            }

            $get_user = json_decode($get_user);

            $jml_save_user = 0;
            $jml_save_finger = 0;

            foreach($get_user as $value_user){

                Log::info('Proses User', [
                    'pin2' => $value_user->PIN2 ?? null,
                    'name' => $value_user->Name ?? null
                ]);

                $data_item = [
                    'name' => $this->safeString($value_user->Name ?? ''),
                    'password' => $this->safeString($value_user->Password ?? ''),
                    'group' => $this->safeString($value_user->Group ?? ''),
                    'privilege' => $this->safeString($value_user->Privilege ?? ''),
                    'card' => $this->safeString($value_user->Card ?? ''),
                    'pin' => $this->safeString($value_user->PIN ?? ''),
                    'pin2' => $this->safeString($value_user->PIN2 ?? ''),
                    'tz1' => $this->safeString($value_user->TZ1 ?? ''),
                    'tz2' => $this->safeString($value_user->TZ2 ?? ''),
                    'tz3' => $this->safeString($value_user->TZ3 ?? ''),
                ];

                $model_user = (new \App\Models\RefUserInfo())
                                ->where(['id_user'=>$value_user->PIN2])
                                ->first();

                if(empty($model_user)){
                    Log::info('User baru dibuat', ['id_user'=>$value_user->PIN2]);
                    $model_user = new \App\Models\RefUserInfo();
                    $model_user->id_user = $value_user->PIN2;
                } else {
                    Log::info('User update', ['id_user'=>$value_user->PIN2]);
                }

                $model_user->set_model_with_data($data_item);

                if($model_user->save()){

                    $jml_save_user++;

                    $get_user_finger = $mesin->get_user_tamplate_tad($value_user->PIN2);

                    if($get_user_finger){

                        $get_user_finger = json_decode($get_user_finger);

                        (new \App\Models\RefUserInfoDetail())
                            ->where(['id_user'=>$value_user->PIN2])
                            ->delete();

                        foreach($get_user_finger as $value_finger){

                            if(!empty($value_finger->Template)){

                                $data_finger = [
                                    'id_user'=> $value_finger->PIN ?? '',
                                    'finger_id'=> $value_finger->FingerID ?? '',
                                    'size'=> $value_finger->Size ?? '',
                                    'valid'=> $value_finger->Valid ?? '',
                                    'finger'=> $value_finger->Template ?? '',
                                    'pin'=> $value_finger->PIN ?? '',
                                ];

                                $model_finger = new \App\Models\RefUserInfoDetail();
                                $model_finger->id_user = $value_user->PIN2;
                                $model_finger->set_model_with_data($data_finger);

                                if($model_finger->save()){
                                    $jml_save_finger++;
                                }
                            }
                        }

                        Log::info('Fingerprint tersimpan', [
                            'id_user'=>$value_user->PIN2,
                            'total_finger'=>$jml_save_finger
                        ]);
                    }
                }else{
                    Log::error('Gagal save user', [
                        'id_user'=>$value_user->PIN2
                    ]);
                }
            }

            Log::info('SUMMARY SINKRON', [
                'total_user_saved'=>$jml_save_user,
                'total_finger_saved'=>$jml_save_finger
            ]);

            if($jml_save_user > 0){
                DB::commit();
                Log::info('=== SINKRON SUCCESS ===');
                return back()->with(['success'=>'Data berhasil di proses']);
            }else{
                DB::rollBack();
                Log::warning('Tidak ada data tersimpan, rollback');
                return back()->with(['error'=>'Data tidak berhasil di proses']);
            }

        } catch (\Throwable $e) {

            DB::rollBack();

            Log::error('ERROR SINKRON MESIN', [
                'message'=>$e->getMessage(),
                'line'=>$e->getLine(),
                'file'=>$e->getFile()
            ]);

            return back()->with(['error'=>'Data tidak berhasil di proses']);
        }
    }


    private function proses_data($request){
        $req = $request->all();
        $kode = !empty($req['pk']) ? $req['pk'] : '';
        $exp=explode('@',$kode);
        $id_mesin_absensi=!empty($exp[0]) ? $exp[0] : '';
        $id_user=!empty($exp[1]) ? $exp[1] : '';
        $type=!empty($exp[2]) ? $exp[2] : '';
        $data_change = !empty($req['value']) ? $req['value'] : '';

        DB::beginTransaction();
        $pesan = [];

        $message_default = [
            'success' => 'Data berhasil diubah',
            'error' => 'Data tidak berhasil diubah'
        ];

        if ($request->ajax()) {
            try {
                $model=( new \App\Models\UserMesinTmp() )->where('id_mesin_absensi','=',$id_mesin_absensi)->where('id_user','=',$id_user)->first();
                if(!empty($model)){
                    if($type=='id'){
                        $model->id_user=$data_change;
                    }
                    if($type=='name'){
                        $model->name=$data_change;
                    }

                    if ($model->save()) {
                        $is_save = 1;
                    }
                }else{
                    $is_save=0;
                }

                if (!empty($is_save)) {
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

            return response()->json($pesan);
        }
    }

    function safeString($value){
        if(is_array($value)){
            return implode(',', $value);
        }
        return (string) $value;
    }

}