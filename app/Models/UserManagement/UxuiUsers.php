<?php

namespace App\Models\UserManagement;

use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class UxuiUsers extends Authenticatable
{
    use Notifiable;
    /* 
    * 1= aktif
    * 0=tidak aktif
    */
    public $table = 'uxui_users';
    protected $primaryKey = 'id';
    public $timestamps = false;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'id','remember_token'
    ];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
        'remember_token',
    ];

    public function getValidation ($id=0) {
        $me=(new self);
        $usename_min=5;
        $usename_max=100;
        
        $rules = [
            'username'  => 'required|min:"'.$usename_min.'"|max:"'.$usename_max.'"|unique:'.$me->table.',username' . ($id ? ",$id" : ''),
        ];

        $messages = [
            'unique' => 'Data sudah digunakan',
            'required' => 'Form Masih Kosong',
            'username.unique' => 'Username sudah digunakan',
            'username.min'=>'Username minimal '.$usename_min.' karakter',
            'username.max'=>'Username maksimal '.$usename_max.' karakter',
        ];

        return (object)[
            'rules'=>$rules,
            'message'=>$messages,
        ];
    }
}