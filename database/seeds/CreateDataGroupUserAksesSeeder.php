<?php

use App\Classes\ListRoutes;
use App\Models\UserManagement\UxuiAuthGroup;
use App\Models\UserManagement\UxuiAuthPermission;
use App\Models\UserManagement\UxuiAuthRoutes;
use App\Models\UserManagement\UxuiAuthUsers;
use App\Services\UserManagement\UserAksesAppService;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class CreateDataGroupUserAksesSeeder extends Seeder
{
    public $userAksesAppService;
    public function __construct()
    {
        $this->userAksesAppService = new UserAksesAppService;
    }
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        try {
            $check = UxuiAuthRoutes::count();
            if ($check == 0) {
                DB::statement('SET FOREIGN_KEY_CHECKS=0;');
                $db = UxuiAuthRoutes::truncate();
                DB::statement('SET FOREIGN_KEY_CHECKS=1;');

                $list_routes = (new ListRoutes)->getDataAuth();
                if (!empty($list_routes)) {
                    foreach ($list_routes as $key_list => $value_list) {
                        if (!empty($value_list['item'])) {
                            $item = $value_list['item'];

                            foreach ($item as $key => $value) {
                                $value = (object) $value;
                                if (!empty($value->method)) {
                                    $data_save = [
                                        'title' => trim($value_list['title']),
                                        'url' => trim($value->url),
                                        'controller' => trim($value->controller),
                                        'type' => trim($value->type),
                                    ];

                                    UxuiAuthRoutes::create($data_save);
                                }
                            }
                        }
                    }
                }
            }

            $check = UxuiAuthGroup::count();
            if ($check == 0) {
                DB::statement('SET FOREIGN_KEY_CHECKS=0;');
                $db = UxuiAuthGroup::truncate();
                DB::statement('SET FOREIGN_KEY_CHECKS=1;');

                UxuiAuthGroup::create(['name' => 'Super Admin', 'alias' => 'group_super_admin']);
                UxuiAuthGroup::create(['name' => 'Admin', 'alias' => 'group_admin']);
                UxuiAuthGroup::create(['name' => 'karyawan', 'alias' => 'group_karyawan']);
            }

            $check = UxuiAuthPermission::count();
            if ($check == 0) {
                DB::statement('SET FOREIGN_KEY_CHECKS=0;');
                $db = UxuiAuthPermission::truncate();
                DB::statement('SET FOREIGN_KEY_CHECKS=1;');

                $list_routes = UxuiAuthRoutes::select('url')->get();
                if (!empty($list_routes)) {
                    $get_group = UxuiAuthGroup::select('alias')->whereIn('alias', ['group_super_admin'])->get();
                    if (!empty($get_group)) {

                        foreach ($get_group as $value_g) {
                            $alias = $value_g->alias;
                            foreach ($list_routes as $list) {
                                $data_save = [
                                    'alias_group' => trim($alias),
                                    'url' => trim($list->url),
                                ];

                                UxuiAuthPermission::insertOrIgnore($data_save);
                            }
                        }

                    }
                }
            }

            $username_default = 'superadmin';
            $check_user_default = (new \App\Models\UserManagement\UxuiUsers)->select(['id'])->where('username', '=', $username_default)->first();
            if (!empty($check_user_default)) {
                $check_group = UxuiAuthUsers::where('id', '=', $check_user_default->id)->first();
                if (empty($check_group)) {
                    $model = new UxuiAuthUsers;
                    $model->id = $check_user_default->id;
                    $model->id_user = $check_user_default->id;
                    $model->alias_group = 'group_super_admin';
                    $model->save();
                }
            }

        } catch (\Illuminate\Database\QueryException $e) {
            if ($e->errorInfo[1] == '1062') {
                dd($e);
            }
            dd($e);
        } catch (\Throwable $e) {
            dd($e);
        }
    }
}
