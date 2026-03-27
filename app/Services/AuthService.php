<?php

namespace App\Services;


use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class AuthService extends BaseService
{
    public function __construct(){
    }

    public function key_encripsi(){
        return 'jiwana';
    }

    public function getUserByCredential(string $id_user, string $password){
        try{
            if(!empty($id_user) &&  !empty($password)){

                $key_encripsi=$this->key_encripsi();

                $model = (new \App\Models\UserManagement\UxuiUsers)->select(['id','username',DB::raw("AES_DECRYPT(password,'".$key_encripsi."') password")])->where('username', '=', $id_user)->where('status', '=', 1)->first();
                if(empty($model)){
                    return ['error','User tidak ditemukan',2];
                }
                if(!empty($model->password)){
                    $check_password=Hash::check($password,$model->password);
                    if($check_password==true){
                        return ['success',$model,1];    
                    }else{
                        return ['error','Password anda salah',2];    
                    }
                }else{
                    return ['error','Password anda salah',2];
                }
            }
            die;
        } catch (\Illuminate\Database\QueryException $e) {
            dd($e);
        } catch (\Throwable $e) {
            dd($e);
        }
    }
}
