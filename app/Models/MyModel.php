<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;

class MyModel extends Model
{
    public $timestamps = false;
    public $incrementing = false;

    public function __construct(array $attributes = [])
    {
        parent::__construct();
    }

    protected function setKeysForSaveQuery(\Illuminate\Database\Eloquent\Builder $query)
    {
        $keys = $this->getKeyName();
         if(!is_array($keys)){
            return parent::setKeysForSaveQuery($query);
        }

        foreach($keys as $keyName){
            $query->where($keyName, '=', $this->getKeyForSaveQuery($keyName));
        }

        return $query;
    }

    protected function getKeyForSaveQuery($keyName = null)
    {
        if(is_null($keyName)){
            $keyName = $this->getKeyName();
        }

        if (isset($this->original[$keyName])) {
            return $this->original[$keyName];
        }

        return $this->getAttribute($keyName);
    }

    public function set_primary(){
        try{
            $pri_key='';
            $data=DB::select( DB::raw('SHOW KEYS FROM '.$this->getTable().'  WHERE Key_name = "PRIMARY"') );
            if(empty($data)){
                $data=DB::select( DB::raw('SHOW INDEXES FROM '.$this->getTable().'  where Non_unique=0') );
            }
            if($data){
                $pri_key=[];
                foreach($data as $item){
                    $pri_key[]=$item->Column_name;
                }
            }

            if($pri_key){
                if(count($pri_key)>=2){
                    $this->primaryKey = $pri_key;
                }else{
                    $this->primaryKey = $pri_key[0];
                }
            }
        } catch (\Throwable $e) {
            dd($e);
        }
    }

    function get_columns_table($params_where=[]){
        $sql='SHOW COLUMNS FROM '.$this->getTable();

        $where=[];
        if(!empty($params_where)){
            foreach($params_where as $key => $value){
                $type=is_numeric($value) ? '=' : 'like';
                if($type=='like'){
                    $value="'".$value."'";
                }
                $where[]=$key.' '.$type.' '.$value;
            }
        }
        if(!empty($where)){
            $where=' where '.implode(' and ',$where);
            $sql.=$where;
        }
        
        return DB::select( DB::raw($sql) );
    }

    function set_columns_table_with_data($data_pas){
        try{
            $data_return=[];
            $get_columns=$this->get_columns_table();

            if(!empty($data_pas)){
                if(is_object($data_pas)){
                    $data_pas=(array)$data_pas;
                }
                foreach($get_columns as $value){
                    if(!empty($data_pas[$value->Field])){
                        $data_return[$value->Field]=$data_pas[$value->Field];
                    }else{
                        $data_return[$value->Field]='';
                    }
                }
            }

            return $data_return;
        } catch (\Throwable $e) {
            dd($e);
        }
    }


    function set_where($query,$params,$list_search=[]){

        /*
        cara mengunakan
            $paramater=[
                'where_or'=>['tangga'=>['=',1 ],'wow'=>1 ],
                'where_between'=>['tangga'=>[tanggal,tanggal ]],
                'where_in'=>['tgl_registrasi'=>['1', 2,'adfaf'],'wow'=>['1', 2,'adfaf']],
                'where_raw'=>[ 'tgl_registrasi'=>['is not',2 ]  ],
                'search'=>'tes',
                'ipsrsbarang.status'=>['=','1']
            ];
        */

        $ignore=['where_in','where_between','where_raw','search','where_or','where_and_or'];
        $param_tmp=[];

        if(!empty($params)){
            $param_tmp=$params;
            foreach($params as $key => $value){
                if(in_array($key,$ignore)){
                    unset($params[$key]);
                }
            }

            foreach($params as $key => $value){
                if(is_array($value)){
                    $query=$query->where($key,$value[0],$value[1]);
                }else{
                    $type=is_numeric($value) ? '=' : 'like';
                    $query=$query->where($key,$type,$value);
                }
            }
        }

        if(!empty($param_tmp['where_in'])){
            foreach($param_tmp['where_in'] as $key => $value){
                $query=$query->whereIn($key, $value);
            }
        }

        if(!empty($param_tmp['where_between'])){
            foreach($param_tmp['where_between'] as $key => $value){
                $query=$query->whereBetween($key, $value);
            }
        };

        if(!empty($param_tmp['where_raw'])){
            foreach($param_tmp['where_raw'] as $key => $value){
                if(!empty($value[0])){
                    $query=$query->whereRaw($key.' '.$value[0].' ', [$value[1]]);
                }
            }
        };

        if(!empty($param_tmp['search'])){
            $search=$param_tmp['search'];
            if(!empty($list_search)){
                $query=$query->where(function ($qb2) use ($search,$list_search) {
                    if(!empty($list_search['where_or'])){
                        foreach($list_search['where_or'] as $key => $value){
                            if($key==0){
                                $qb2->where($value, 'LIKE', '%' . $search . '%');
                            }else{
                                $qb2->orWhere($value, 'LIKE', '%' . $search . '%');
                            }
                        }
                    };
                    if(!empty($list_search['where_and'])){
                        foreach($list_search['where_and'] as $key => $value){
                            $qb2->where($value, 'LIKE', '%' . $search . '%');
                        }
                    };
                });
            }
        }


        if(!empty($param_tmp['where_or'])){
            foreach($param_tmp['where_or'] as $key => $value){
                if(is_array($value)){
                    $query=$query->orWhere($key,$value[0],$value[1]);
                }else{
                    $type=is_numeric($value) ? '=' : 'like';
                    $query=$query->orWhere($key,$type,$value);
                }
            }
        }

        if(!empty($param_tmp['where_and_or'])){
            $search=$param_tmp['where_and_or'];
            $query=$query->where(function ($qb2) use ($search) {
                foreach($search as $key => $value){
                    if(is_array($value)){
                        $qb2->orWhere($key,$value[0],$value[1]);
                    }else{
                        $type=is_numeric($value) ? '=' : 'like';
                        $qb2->orWhere($key,$type,$value);
                    }
                }
            });
        }

        return $query;
    }

    function set_model_with_data($passing_data){
        try{
            $get_columns=$this->get_columns_table();

            if(!empty($passing_data)){
                if(is_object($passing_data)){
                    $passing_data=(array)$passing_data;
                }

                foreach($get_columns as $item){
                    if(array_key_exists($item->Field,$passing_data)){
                        $this->attributes[$item->Field]=$passing_data[$item->Field];
                    }
                }
            }

        } catch (\Throwable $e) {
            dd($e);
        }
    }

    function restrukturisasi(){
        try{
            $this->set_primary();
            $get_columns=$this->get_columns_table();

            $check_field=[];
            foreach($get_columns as $item){
                $check_field[$item->Field]=$item->Field;
                $this->attributes[$item->Field]=isset($this->attributes[$item->Field]) ? $this->attributes[$item->Field] : $item->Default;

                $type_null=$item->Null;
                if(trim(strtolower($type_null))=='no'){
                    $this->attributes[$item->Field]=isset($this->attributes[$item->Field]) ? $this->attributes[$item->Field] : '';
                }
                if (strpos($item->Type, 'enum') !== false) {
                    $this->attributes[$item->Field]=isset($this->attributes[$item->Field]) ? (string)$this->attributes[$item->Field] : '';
                }

                if (strpos($item->Type, 'double') !== false) {
                    $this->attributes[$item->Field]=isset($this->attributes[$item->Field]) ? $this->attributes[$item->Field] : '';
                }

                if (strpos($item->Type, 'float') !== false) {
                    $this->attributes[$item->Field]=isset($this->attributes[$item->Field]) ? $this->attributes[$item->Field] : '';
                }
            }

            foreach($this->attributes as $key => $item){
                if(!array_key_exists($key,$check_field)){
                    unset($this->attributes[$key]);
                }
            }

        } catch (\Throwable $e) {
            dd($e);
        }
    }

    public function validator(){
        // return Validator::make($request,$rules,$message);
    }

    protected static function boot(){
        parent::boot();

        // self::creating(function($model){
        //     $model->restrukturisasi();
        // });

        // self::created(function($model){
        //     $model->restrukturisasi();
        // });

        // self::updating(function($model){
        //     $model->restrukturisasi();
        // });

        // self::updated(function($model){
        //     $model->restrukturisasi();
        // });

        self::deleting(function($model){
            $model->restrukturisasi();
        });

        self::deleted(function($model){
            $model->restrukturisasi();
        });

        static::saving(function ($model) {
            $model->restrukturisasi();
        });
    }
}
