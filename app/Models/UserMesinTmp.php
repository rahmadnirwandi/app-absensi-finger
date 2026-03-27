<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class UserMesinTmp extends \App\Models\MyModel
{
    public $table = 'user_mesin_tmp';
    public $timestamps = false;
    public $primaryKey = ['id_mesin_absensi', 'id_user', 'name', 'group', 'privilege'];
}