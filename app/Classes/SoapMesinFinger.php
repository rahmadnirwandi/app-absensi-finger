<?php

namespace App\Classes;

use TADPHP\TAD;
use TADPHP\TADFactory;
use Illuminate\Support\Facades\DB;

class SoapMesinFinger
{
    public $ip = null;
    public $port = 80;
    public $comm_key = 0;

    public function __construct($ip = null,$comm_key = null, $port = null ){
        if ($ip != null) {
            $this->ip = $ip;
        }
        if ($port != null) {
            $this->port = $port;
        }

        if ($comm_key != null) {
            $this->comm_key = $comm_key;
        }
    }

    // function connect_sock($ip = null,$port=null){
    //     if ($ip != null) {
    //         $this->ip = $ip;
    //     }
    //     if ($port != null) {
    //         $this->port = $port;
    //     }
    //     if ($this->ip == null || $this->port == null) {
    //         return ['error','error code',3];
    //     }

    //     try {
    //         return fsockopen($this->ip, $this->port, $errno, $errstr, 1);
    //     } catch (\Throwable $e) {
    //         return ['error','error code',3];
    //     }
    // }

    function connect_sock($ip = null, $port = null)
    {
        if ($ip != null) {
            $this->ip = $ip;
        }

        if ($port != null) {
            $this->port = $port;
        }

        if ($this->ip == null || $this->port == null) {
            return [
                'status' => false,
                'code' => 3,
                'message' => 'IP atau Port tidak valid'
            ];
        }

        try {

            $socket = @fsockopen($this->ip, $this->port, $errno, $errstr, 3);

            if (!$socket) {
                return [
                    'status' => false,
                    'code' => 3,
                    'message' => $errstr ?: 'Tidak terkoneksi'
                ];
            }

            $this->socket = $socket;

            return [
                'status' => true,
                'code' => 1,
                'message' => 'Terkoneksi'
            ];

        } catch (\Throwable $e) {

            return [
                'status' => false,
                'code' => 3,
                'message' => $e->getMessage()
            ];
        }
    }
    function connect($ip = null,$port=null){
        $connect_ip=[];
        $connect='';

        try {
            $connect = $this->connect_sock($ip,$port);
            $check_connect=!empty($connect[0]) ? $connect[0] : '';
            $is_connect=1;
            if($check_connect=='error'){
                $is_connect=0;
            }
            if($is_connect) {
                $connect_ip=['success','ip connect',1];
            }else{
                $connect_ip=['error','ip tidak connect',2];
            }
        } catch (\Throwable $e) {
            $connect_ip=['error','error code',3];
        }

        return $connect_ip;
    }

    function connect_tad($ip = null,$comm_key=null){
        if ($ip != null) {
            $this->ip = $ip;
        }
        if ($comm_key != null) {
            $this->comm_key = $comm_key;
        }

        return (new TADFactory((['ip'=>$this->ip , 'com_key'=>$this->comm_key])))->get_instance();
    }

    function parse_data($data, $p1, $p2) {
        $data = " " . $data;
        $hasil = "";
        $awal = strpos($data, $p1);
        if($awal != "") {
            $akhir = strpos(strstr($data, $p1), $p2);
            if($akhir != "") {
                $hasil = substr($data, $awal + strlen($p1), $akhir - strlen($p1));
            }
        }
        return $hasil;
    }

    function set_fputs($connect,$soap_request){
        $newLine = "\r\n";
        fputs($connect, "POST /iWsService HTTP/1.0" . $newLine);
        fputs($connect, "Content-Type: text/xml" . $newLine);
        fputs($connect, "Content-Length: " . strlen($soap_request) . $newLine . $newLine);
        fputs($connect, $soap_request . $newLine);
        $buffer = "";
        while($Response = fgets($connect, 1024)) {
            $buffer = $buffer . $Response;
        }

        return $buffer;
    }

    public function ping($timeout = 1){
        $time1 = microtime(true);
        $pfile = fsockopen($this->ip, $this->port, $errno, $errstr, $timeout);
        if (!$pfile) {
            return 'down';
        }
        $time2 = microtime(true);
        fclose($pfile);
        return round((($time2 - $time1) * 1000), 0);
    }

    public function lib($key){
        return (object)[
            'arg_com_key'=>"<ArgComKey Xsi:type=\"xsd:integer\">" . $key . "</ArgComKey>",
            'pin'=>"<PIN>" . $key . "</PIN>",
        ];
    }
}