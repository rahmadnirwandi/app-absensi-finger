<?php

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

use App\Models\UserManagement\UxuiAuthRoutes;
use App\Models\UserManagement\UxuiAuthGroup;
use App\Models\UserManagement\UxuiAuthPermission;
use App\Models\UserManagement\UxuiAuthUsers;

class EmptyTabelSeeder extends Seeder
{

    public function run()
    {
        try {
            DB::statement('SET FOREIGN_KEY_CHECKS=0;');
            $db=UxuiAuthRoutes::truncate();
            $db=UxuiAuthGroup::truncate();
            $db=UxuiAuthPermission::truncate();
            $db=UxuiAuthUsers::truncate();
            DB::statement('SET FOREIGN_KEY_CHECKS=1;');

        } catch(\Illuminate\Database\QueryException $e){
            if($e->errorInfo[1] == '1062'){
                dd($e);
            }
            dd($e);
        } catch (\Throwable $e) {
            dd($e);
        }
    }
}