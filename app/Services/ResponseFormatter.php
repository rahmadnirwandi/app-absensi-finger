<?php

namespace App\Services;

use Illuminate\Support\Facades\DB;

class ResponseFormatter extends BaseService
{
    public function __construct(){
        parent::__construct();
    }
    protected static $response =[
        'metaData' =>[
            'kode' => 200,
            'status' => 'success',
            'message' => null
        ],
        'response' => null
    ];
    
    public static function success($data = null, $message=null){
        self::$response['metaData']['message'] = $message;
        // self::$response['data'] = $data;
        if($data==null){
            unset(self::$response['response']);
        }else{
            self::$response['response'] = $data;
        }
        return response()->json(self::$response, self::$response['metaData']['kode']);
    }


    public static function error($data = null, $message=null, $code=400||$code=403){
        self::$response['metaData']['status'] = 'error';
        self::$response['metaData']['kode'] = $code;
        self::$response['metaData']['message'] = $message;
        if($data==null){
            unset(self::$response['response']);
        }else{
            self::$response['response'] = $data;
        }
        
        return response()->json(self::$response, self::$response['metaData']['kode']);
    }
}
