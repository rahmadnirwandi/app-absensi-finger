<?php

namespace App\Http\Traits;

use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Session;

use App\Models\UserManagement\UxuiAuthUsers;
use App\Models\UserManagement\UxuiAuthPermission;

trait AuthTraits {

    public $id_user;
    public function __construct()
    {
        if(Auth::check()){
            $this->id_user=Session::get('get_id_user');
        }else{
            $this->id_user='';
        }
    }

    public function getUser(){

        $data_user=[];
        if($this->id_user){
            $data_user['auth']=Auth::user();
            $data_user['id_user']=$this->id_user;
            
            $get_group = collect(UxuiAuthUsers::select('alias_group')->where('id_user', '=', $this->id_user)->get())->map(function ($value) {
                return $value->alias_group;
            });

            $data_user['group_user']=$get_group;
            $data_user['auth_user']=[];

            if(!empty($get_group)){
                $list=[];
                foreach($get_group as $group){
                    $col=(new UxuiAuthPermission)->getPermission(['where'=>[ ['alias_group', '=', $group],['alias_group', '=', $group] ]])->select('uxui_auth_permission.url')->get();
                    foreach($col as $value){
                        $list[$value->url]=$value->url;
                    }
                }

                $data_user['auth_user']=$list;
            }

            $get_data_karyawan = (new \App\Models\UxuiUsersKaryawan)->where('id_uxui_users', '=', $this->id_user)->first();
            if(!empty($get_data_karyawan)){
                $paramater_search=[
                    'ref_karyawan.id_karyawan'=>$get_data_karyawan->id_karyawan,
                ];
                $get_karyawan=(new \App\Services\RefKaryawanService)->getListKaryawanJadwal($paramater_search, 1)->first();
                $get_role_karyawan=(new \App\Services\RefKaryawanService)->getRoleKaryawan($this->id_user);
                
                $data_user_sistem=[
                    'id_karyawan'=>$get_data_karyawan->id_karyawan,
                    'nm_karyawan'=>$get_karyawan->nm_karyawan,
                    'id_jenis_jadwal'=>$get_karyawan->id_jenis_jadwal,
                    'id_user_mesin'=>$get_karyawan->id_user,
                    'nama_role' => $get_role_karyawan->name
                ];
                $data_user['data_user_sistem']=(object)$data_user_sistem;
            }
        }

        if(empty($data_user['auth']) && empty($data_user['auth_user'])){
            if (Auth::check()) {
                Auth::logout();
                Session::invalidate();
                Session::regenerateToken();
            }
            Session::flush();
            return redirect('login')->send();
        }
        return (object)$data_user;
    }

    public function checkAkses($routes){
        $return=0;
        $get_list_user=$this->getUser()->auth_user;

        $ignore_akses=[];
        $ignore_akses_tmp=(new \App\Models\UserManagement\UxuiAuthRoutes)->getIgnoreRoutes();
        if(!empty($ignore_akses_tmp)){
            foreach($ignore_akses_tmp as $value){
                $ignore_akses[$value]=$value;
            }
        }

        $get_list_user=array_merge($get_list_user,$ignore_akses);
        if(is_array($routes)){
            foreach($routes as $value){
                $value=( substr($value,0,1) =='/') ? $value : '/'.$value;
            }
        }else{
            $routes=( substr($routes,0,1) =='/') ? $routes : '/'.$routes;
            if(in_array($routes,$get_list_user)){
                $return=1;
            }
        }

        return $return;
    }

    public function checkMenuAkses($menus){
        
        if(!empty($menus)){
            foreach($menus as $key => $menu){
                $menu=(array)$menu;
                if(!empty($menu['key_active'])){
                    $jlh_check=0;
                    foreach($menu['key_active'] as $value){
                        $check=$this->checkAkses($value);
                        if($check==1){
                            $jlh_check++;
                        }
                    }
                    if($jlh_check<=0){
                        unset($menus[$key]);
                    }
                }else{
                    $check=$this->checkAkses($menu['key']);
                    if($check==0){
                        unset($menus[$key]);
                    }
                }
            }
        }
        return !empty($menus) ? $menus : [];
    }

    public function setPermissionButton($html_parameter=[],$modal_tmp=[]){
        $url=$html_parameter[0];
        $paramater=!empty($html_parameter[1]) ? $html_parameter[1] : '';
        $type=!empty($html_parameter[2]) ? $html_parameter[2] : '';
        $paramater_html=!empty($html_parameter[3]) ? $html_parameter[3] : [];
        $icon=!empty($html_parameter[4]) ? $html_parameter[4] : '';
        $tamplate='';
        if(!empty($url)){
            $check_url=$this->checkAkses($url);
            if(!empty($check_url)){
                $modal=!empty($modal_tmp[0]) ? trim(strtolower($modal_tmp[0])) : '';
                $modal=(trim(strtolower($modal)) === 'modal' ) ? 1 : 0;
                $modal_parameter=!empty($modal_tmp[1]) ? $modal_tmp[1] : ['data-modal-width'=>'50%','data-modal-title'=>''];

                $type=( trim(strtolower($type)) );

                $paramater_tmp=$paramater;
                $paramater='';

                if($modal===1){
                    $tmp_para=[];
                    foreach($paramater_tmp as $value){
                        $tmp_para[]=$value;
                    }
                    $paramater=implode('@',$tmp_para);
                    $paramater="'".$paramater."'";
                }else{
                    $tmp_para=[];
                    foreach($paramater_tmp as $key => $value){
                        $tmp_para[]=$key.'='.$value;
                    }
                    $paramater=implode('&',$tmp_para);
                }

                $action_exsist=0;
                if($type=='update'){
                    $icon_default='<i class="fa-solid fa-pencil"></i>';
                    $icon=!empty($icon) ? $icon : $icon_default;
                    if(empty($modal_parameter['data-modal-title'])){
                        $modal_parameter['data-modal-title']='Form Ubah Data';
                    }


                    $class_html='btn btn-kecil btn-warning';
                    if($modal===1){
                        $class_html.=' modal-remote';
                    }

                    $action_exsist=1;

                }elseif($type=='delete'){
                    $icon_default='<i class="fa-solid fa-trash"></i>';
                    $icon=!empty($icon) ? $icon : $icon_default;
                    if(empty($modal_parameter['data-modal-title'])){
                        $modal_parameter['data-modal-title']='Hapus Data';
                    }

                    if(empty($modal_parameter['data-confirm-message'])){
                        $modal_parameter['data-confirm-message']='Apakah anda yakin menghapus data ini ?';
                    }

                    $class_html='btn btn-kecil btn-danger';
                    if($modal===1){
                        $class_html.=' modal-remote-delete';
                    }

                    $action_exsist=1;

                }elseif($type=='view'){

                    $icon_default='<i class="fa-solid fa-circle-info"></i>';
                    $icon=!empty($icon) ? $icon : $icon_default;
                    if(empty($modal_parameter['data-modal-title'])){
                        $modal_parameter['data-modal-title']='Detail Data';
                    }

                    $class_html='btn btn-kecil btn-info';
                    if($modal===1){
                        $class_html.=' modal-remote';
                    }
                    $action_exsist=1;
                }
                if(!empty($action_exsist)){
                    if(empty($paramater_html['class'])){
                        $paramater_html['class']=$class_html;
                    }else{
                        $paramater_html['class']=$class_html.' '.$paramater_html['class'];
                    }

                    $paramater_html=implode(' ',array_map(function ($value, $index) {
                        return $index.'='."'".$value."'";
                    },$paramater_html,array_keys($paramater_html)));

                    if($modal===1){
                        $modal_parameter=implode(' ',array_map(function ($value, $index) {
                            return $index.'='."'".$value."'";
                        },$modal_parameter,array_keys($modal_parameter)));

                        $tamplate="<a href='".url($url)."' ".$paramater_html." data-modal-key=".$paramater." ".$modal_parameter." >".$icon."</a>";
                    }else{
                        $url=$url.'?'.$paramater;
                        $tamplate="<a href='".url($url)."' ".$paramater_html." >".$icon."</a>";
                    }
                }
            }
        }

        return $tamplate;
    }
}

class AuthFunction {
    use AuthTraits;
}
