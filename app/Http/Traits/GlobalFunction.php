<?php

namespace App\Http\Traits;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;

use Illuminate\Pagination\Paginator;
use Illuminate\Support\Collection;
use Illuminate\Pagination\LengthAwarePaginator;

trait GlobalTraits {

    public function filterValidasiField($field_not_empty,$data){
        foreach($data as $key => $value){
            if(!isset($value) or strlen(trim($value)) <= 0 ){
                if(in_array($key,$field_not_empty)){
                    return ['error',$key];
                }
            }
        }
        return ['success',$data];
    }

    public function is_decimal( $val )
    {
        return is_numeric( $val ) && floor( $val ) != $val;
    }

    public function digit_decimal( $val )
    {
        if($this->is_decimal($val)){
            return strlen(substr(strrchr($val, "."), 1));
        }else{
            return 0;
        }
    }

    public function formatMoney($nominal, $currency = "",$format=[',','.'])
    {
        if(is_numeric($nominal)){
            $digit_decimal=$this->digit_decimal($nominal);
            return $currency." ".(!empty($nominal) ? number_format($nominal,$digit_decimal,$format[0],$format[1]) : '');
        }
        return $currency. " " .$nominal;
    }

    public function formatMoneyToSystem($nominal,$replace=['.'=>'',','=>'.'])
    {
        foreach($replace as $key =>$value){
            $nominal=str_replace($key,$value,$nominal);
        }
        return $nominal;
    }

    public function getSentForm($params=[])
    {
        if(empty($params)){
            return '';
        }
        $item_form=json_encode($params);
        return $item_form;
    }

    public function generateLink($data=[],$url_new=null){

        $return=[];
        if($data){
            foreach($data as $key => $value){
                $return[]=$key.'='.$value;
            }
        }

        $link = explode("?", (isset($_SERVER['HTTPS']) && $_SERVER['HTTPS']=== 'on' ? 'https' : 'http') . '://' .$_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI']);
        if($url_new){
            $link[0]=$url_new;
        }
        return $link[0].'?'.implode('&',$return);
    }

    public function makeJson($data=[]){
        return json_encode($data);
    }

    public function check_akses($id_user)
    {
        $user_login=Auth::user();
        if($user_login->id == $id_user){
            return 1;
        }
        return 0;
    }


    public function delete_session($name){
        if(\Session::has($name)){
            \Session::forget($name);
            if(!(\Session::has($name))){
                return true;
            }else{
                return false;
            }
        }
    }

    public function remove_multiplespace($data){
        return preg_replace('/(\s\s+|\t|\n)/', ' ', $data);
    }


    public function getRouterIndex($url=''){

        $url_index='';
        if(empty($url)){
            $route = \Route::current();
            $uri_tmp=explode('/',$route->uri);
            $url_index=!empty($uri_tmp[0]) ? $uri_tmp[0] : '';
        }else{
            $url_index=$url;
        }
        $route = app('router')->getRoutes()->match(app('request')->create($url_index));
        $router_name=$route->getName();

        if(empty($router_name)){
            echo('default url '.$route->uri().' harus memiliki name');
        }

        $controller=($route->getAction())['controller'];
        $base_controller_tmp=explode('@',$controller);
        $base_controller=!empty($base_controller_tmp[0]) ? $base_controller_tmp[0] : '';
        $url_path_base=resource_path().'/views/'.$route->uri();
        $path_base=$route->uri();

        return (object)[
            'uri'=>$route->uri(),
            'router_name'=>$router_name,
            'base_controller'=>$base_controller,
            'url_path_base'=>$url_path_base,
            'path_base'=>$path_base
        ];
    }

    public function hari($hari='',$type=''){
        $data=[];
        if(empty($type)){
            $data_tmp=[
                'Mon'=>"Senin",
                'Tue'=>"Selasa",
                'Wed'=>"Rabu",
                'Thu'=>"Kamis",
                'Fri'=>"Jumat",
                'Sat'=>"Sabtu",
                'Sun'=>"Minggu",
            ];
        }else{
            $data_tmp=[
                'Mon'=>"Sen",
                'Tue'=>"Sel",
                'Wed'=>"Rab",
                'Thu'=>"Kam",
                'Fri'=>"Jum",
                'Sat'=>"Sab",
                'Sun'=>"Min",
            ];
        }
        $data=$data_tmp;
        if(!empty($hari)){
            return !empty($data[$hari]) ? $data[$hari] : "Tidak di ketahui";
        }else{
            return $data;
        }
    }

    public function get_bulan($id=''){
        $data=[
            1=>"Januari",
            2=>"Februari",
            3=>"Maret",
            4=>"April",
            5=>"Mei",
            6=>"Juni",
            7=>"Juli",
            8=>"Agustus",
            9=>"September",
            10=>"Oktober",
            11=>"November",
            12=>"Desember",
        ];
        if(!empty($id)){
            return !empty($data[$id]) ? $data[$id] : "Tidak di ketahui";
        }else{
            return $data;
        }
    }

    public function set_format_tanggal($tanggal,$format=[]){

        $return_waktu='';
        $return_tanggal='';
        $return_jam='';

        $format_jam='H:i:s';
        if(!empty($format['jam'])){
            $format_jam=$format['jam'];
        }

        $format_tanggal='d-m-Y';
        if(!empty($format['tanggal'])){
            $format_tanggal=$format['tanggal'];
        }

        if(!empty($tanggal)){
            $waktu = new \DateTime($tanggal);
            $tgl=$waktu->format($format_tanggal);
            $jam=$waktu->format($format_jam);

            $return_waktu='<span>'.$tgl.' '.$jam.'</span>';

            $return_tanggal=$tgl;
            $return_jam=$jam;
        }

        return (object)[
            'tanggal_jam'=>$return_waktu,
            'tanggal'=>$return_tanggal,
            'jam'=>$return_jam,
        ];
    }

    public function set_paramter_url($url,$parameter=[]){
        $tmp_para=[];
        foreach($parameter as $key => $value){
            $tmp_para[]=$key.'='.$value;
        }
        $parameter=implode('&',$tmp_para);
        return $url.'?'.$parameter;
    }

    public function validator($request=[],$rules=[],$message=[]){
        $check_validasi=Validator::make($request,$rules,$message);
        $list_error=[];
        if ( $check_validasi->fails() ) {
            $get_errors=$check_validasi->errors();
            foreach($get_errors->getMessages() as $key => $value){
                foreach($value as $key_1 => $value_1){
                    $list_error[]=$value_1;
                }
            }
            $list_error=implode(',',$list_error);
        }

        return (object)[
            'validasi'=>$check_validasi,
            'list_error'=>$list_error,
        ];
    }

    public function paginate($items, $perPage = 5, $page = null, $options = [],$option_custom=[])
    {
        $page = $page ?: (Paginator::resolveCurrentPage() ?: 1);
        $items = $items instanceof Collection ? $items : Collection::make($items);

        $total_list_data=!empty($option_custom['total_list_data']) ? $option_custom['total_list_data'] : 0;
        $total_list_data=!empty($total_list_data) ? $total_list_data : $items->count();

        $page_if_limit_query=!empty($option_custom['page_if_limit_query']) ? $option_custom['page_if_limit_query'] : 0;
        $page_loop=$page;
        if(!empty($page_if_limit_query)){
            $page_loop=1;
        }
        return new LengthAwarePaginator($items->forPage($page_loop, $perPage), $total_list_data, $perPage, $page, $options);
    }

    public function limit_mysql_manual($limit_data)
    {
        if(!empty($limit_data[0])){
            if($limit_data[0]<0){
                $limit_data[0]=0;
            }
        }

        if(!empty($limit_data[0]) && !empty($limit_data[1]) ){
            $limit_data[0]=$limit_data[0] * $limit_data[1];
        }

        if(empty($limit_data[0]) && empty($limit_data[1])){
            $limit_data=[];
        }
        return $limit_data;
    }

    function remove_special_char($str) {
        return  preg_replace('/[^a-zA-Z0-9_ -]/s','', $str);
    }

    function remove_special_char_json($str) {
        $data=["'","/",'\\','[',']','<','>'];
        return str_replace($data,'',$str);
    }
}

class GlobalFunction {
    use globalTraits;
}

?>