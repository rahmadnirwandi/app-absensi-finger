<?php

namespace App\Services;

use Illuminate\Support\Facades\DB;

class GlobalService extends BaseService
{
    public function __construct(){
        parent::__construct(); 
    }

    public function toRawSql($query){
        $addSlashes = str_replace('?', "'?'", $query->toSql());
        return vsprintf(str_replace('?', '%s', $addSlashes), $query->getBindings());
    }
    
}