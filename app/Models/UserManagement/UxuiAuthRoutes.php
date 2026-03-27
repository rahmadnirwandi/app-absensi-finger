<?php

namespace App\Models\UserManagement;

use Illuminate\Database\Eloquent\Model;

class UxuiAuthRoutes extends Model
{
    public $table = 'uxui_auth_routes';
    public $timestamps = false;

    public function getIgnoreRoutes(){
        return [
            '/',
            'login',
            '/logout',
            '/ajax',
            '/sub-menu',
            '/sub-menu/list_akses',
        ];
    }

    public function getIgnoreSubRoutes(){
        return [
            'ajax',
            'form',
        ];
    }
}
