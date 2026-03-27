<?php

namespace App\Http\Controllers;

use App\Http\Traits\GlobalFunction;
use App\Services\UserManagement\UserAksesAppService;
use App\Services\UserManagement\UserGroupAppService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;

class UserAksesController extends Controller
{

    public $part_view, $url_index, $url_name, $title, $breadcrumbs, $globalService;
    public $userAksesAppService, $userGroupAppService;

    public function __construct()
    {

        $router_name = (new \App\Http\Traits\GlobalFunction)->getRouterIndex();
        $this->part_view = $router_name->path_base;
        $this->url_index = $router_name->uri;
        $this->url_name = $router_name->router_name;

        $this->title = 'User akses';
        $this->breadcrumbs = [
            ['title' => 'Manajemen User', 'url' => url('/') . "/sub-menu?type=2"],
            ['title' => $this->title, 'url' => url('/') . "/user-akses"],
        ];

        $this->userAksesAppService = new UserAksesAppService;
        $this->userGroupAppService = new UserGroupAppService;
    }

    public function actionIndex(Request $request)
    {

        $form_filter_text = !empty($request->form_filter_text) ? $request->form_filter_text : '';
        $filter_level_akses = !empty($request->filter_level_akses) ? $request->filter_level_akses : '';
        $filter_status_akses = !empty($request->filter_status_akses) ? $request->filter_status_akses : '';

        $paramater = [
            'search' => $form_filter_text,
        ];

        if (!empty($filter_level_akses)) {
            $paramater['alias_group'] = $filter_level_akses;
        }

        if (!empty($filter_status_akses)) {
            $paramater['status_tersedia'] = $filter_status_akses;
        }

        $list_data = $this->userGroupAppService->getUserKaryawanGroup($paramater, 1)->paginate(!empty($request->per_page) ? $request->per_page : 15);
        $level_akses_list = $this->userGroupAppService->getUxuiAuthGroup();

        $parameter_view = [
            'title' => $this->title,
            'breadcrumbs' => $this->breadcrumbs,
            'level_akses_list' => $level_akses_list,
            'list_data' => $list_data,
        ];

        return view('user-management.user-akses.index', $parameter_view);
    }

    private function form(Request $request)
    {
        $kode = !empty($request->data_sent) ? $request->data_sent : '';
        $paramater = [
            'id_karyawan' => $kode,
        ];
        $model = $this->userGroupAppService->getUserKaryawanGroup($paramater, 1)->first();
        if ($model) {
            $action_form = $this->part_view . '/update';
        } else {
            $action_form = $this->part_view . '/create';
        }

        $level_akses_list = $this->userGroupAppService->getUxuiAuthGroup();
        $parameter_view = [
            'action_form' => $action_form,
            'level_akses_list' => $level_akses_list,
            'model' => $model,
        ];

        return view('user-management.user-akses.form', $parameter_view);
    }

    public function actionUpdate(Request $request)
    {
        if ($request->isMethod('get')) {
            $bagan_form = $this->form($request);

            $parameter_view = [
                'title' => $this->title,
                'breadcrumbs' => $this->breadcrumbs,
                'bagan_form' => $bagan_form,
                'url_back' => $this->url_name,
            ];

            return view('layouts.index_bagan_form', $parameter_view);
        }

        if ($request->isMethod('post')) {
            return $this->proses($request);
        }
    }

    private function proses($request)
    {
//        DB::statement("ALTER TABLE " . (new \App\Models\UserManagement\UxuiUsers)->table . " AUTO_INCREMENT = 1");
//        DB::statement("ALTER TABLE " . (new \App\Models\UxuiUsersKaryawan)->table . " AUTO_INCREMENT = 1");
        $req = $request->all();

        $id_karyawan = !empty($req['id_karyawan']) ? $req['id_karyawan'] : '';
        $id_uxui_users = !empty($req['id_uxui_users']) ? $req['id_uxui_users'] : '';
        $link_back_redirect = $this->url_name . '/update';
        DB::beginTransaction();
        $pesan = [];
        $link_back_param = ['data_sent' => $id_karyawan];
        $link_back_param = array_merge($link_back_param, $request->all());
        $message_default = [
            'success' => !empty($kode) ? 'Data berhasil diubah' : 'Data berhasil disimpan',
            'error' => !empty($kode) ? 'Data tidak berhasil diubah' : 'Data berhasil disimpan',
        ];

        try {
            $data_form = (object) $req;

            $username = !empty($data_form->username) ? $data_form->username : '';
            $username = trim($username);
            $password = !empty($data_form->password) ? $data_form->password : '';
            $level_akses = !empty($data_form->level_akses) ? $data_form->level_akses : '';
            if (!$username) {
                return redirect()->route($link_back_redirect, $link_back_param)->with(['error' => 'Username masih kosong']);
            }

            if (empty($id_uxui_users)) {
                if (!$password) {
                    return redirect()->route($link_back_redirect, $link_back_param)->with(['error' => 'Password masih kosong']);
                }
            }

            if (!$level_akses) {
                return redirect()->route($link_back_redirect, $link_back_param)->with(['error' => 'Level Akses Masih Kosong']);
            }

            if (empty($id_uxui_users)) {
                $check_users = (new \App\Models\UxuiUsersKaryawan)->where('id_karyawan', '=', $id_karyawan)->first();
                $id_uxui_users = !empty($check_users->id_uxui_users) ? $check_users->id_uxui_users : '';
            }

            $user_baru = 0;
            $model_uxui_user = (new \App\Models\UserManagement\UxuiUsers)->where('id', '=', $id_uxui_users)->first();
            if (empty($model_uxui_user)) {
                $model_uxui_user = (new \App\Models\UserManagement\UxuiUsers);
                $user_baru = 1;
            }

            if (!empty($data_form->status_user)) {
                $model_uxui_user->status = 1;
            } else {
                $model_uxui_user->status = 0;
            }

            $key_encripsi = (new \App\Services\AuthService)->key_encripsi();

            $model_uxui_user->username = $username;
            $ganti_pass = 0;
            $ubah_pass = 0;
            if (!empty($model_uxui_user->password) and !empty($password)) {
                $ubah_pass = 1;
            }

            if (($user_baru == 1) || ($ubah_pass == 1)) {
                $pass_tmp = $password;
                $password = Hash::make($password);
                $model_uxui_user->password = DB::raw("AES_ENCRYPT('" . $password . "','" . $key_encripsi . "')");
                $ganti_pass = 1;
            }

            $is_save = 0;

            $data_model_attribut = $model_uxui_user->getAttributes();
            $id_primary = !empty($id_uxui_users) ? $id_uxui_users : '';

            $get_rules = (new \App\Models\UserManagement\UxuiUsers)->getValidation($id_primary);

            $validator = (new \App\Http\Traits\GlobalFunction)->validator($data_model_attribut, $get_rules->rules, $get_rules->message);

            if (!empty($validator->list_error)) {
                return redirect()->route($link_back_redirect, $link_back_param)->with(['error' => $validator->list_error]);
            }

            if ($model_uxui_user->save()) {
                $id_uxui_users = $model_uxui_user->id;
                $is_save_11 = 0;

                if ($ganti_pass) {
                    (new \App\Models\UxuiUuTmp)->where('id', '=', $id_uxui_users)->delete();
                    $model_uu_tmp = (new \App\Models\UxuiUuTmp)->where('id', '=', $id_uxui_users)->first();
                    if (empty($model_uu_tmp)) {
                        $model_uu_tmp = (new \App\Models\UxuiUuTmp);
                        $model_uu_tmp->id = $id_uxui_users;
                        $model_uu_tmp->uraian = DB::raw("AES_ENCRYPT('" . $pass_tmp . "','" . $key_encripsi . "')");
                    }
                    if ($model_uu_tmp->save()) {
                        $is_save_11 = 1;
                    } else {
                        $is_save_11 = 0;
                    }
                } else {
                    $is_save_11 = 1;
                }

                if ($is_save_11) {
                    $model_user_karyawan = (new \App\Models\UxuiUsersKaryawan)->where('id_uxui_users', '=', $id_uxui_users)->where('id_karyawan', '=', $id_karyawan)->first();
                    if (empty($model_user_karyawan)) {
                        $model_user_karyawan = (new \App\Models\UxuiUsersKaryawan);
                        $model_user_karyawan->id_uxui_users = $id_uxui_users;
                        $model_user_karyawan->id_karyawan = $id_karyawan;
                    }
                    if ($model_user_karyawan->save()) {
                        $is_save = 1;
                    }
                }
            }

            $model_auth_users = (new \App\Models\UserManagement\UxuiAuthUsers)->where('id_user', '=', $id_uxui_users)->first();
            if (empty($model_auth_users)) {
                $model_auth_users = (new \App\Models\UserManagement\UxuiAuthUsers);
                $model_auth_users->id = $id_uxui_users;
                $model_auth_users->id_user = $id_uxui_users;
            }
            $model_auth_users->alias_group = $level_akses;

            if ($model_auth_users->save()) {
                $is_save = 1;
            } else {
                $is_save = 0;
            }

            if ($is_save) {
                DB::commit();
                $pesan = ['success', $message_default['success'], 2];
            } else {
                DB::rollBack();
                $pesan = ['error', $message_default['error'], 3];
            }
        } catch (\Illuminate\Database\QueryException $e) {
            DB::rollBack();
            \Log::info($e->getMessage());
            if ($e->errorInfo[1] == '1062') {
            }
            $pesan = ['error', $message_default['error'], 3];
        } catch (\Throwable $e) {
            DB::rollBack();
            \Log::info($e->getMessage());
            $pesan = ['error', $message_default['error'], 3];
        }
        return redirect()->route($link_back_redirect, $link_back_param)->with([$pesan[0] => $pesan[1]]);
    }

    public function actionDelete(Request $request)
    {

        $req = $request->all();
        DB::beginTransaction();
        $pesan = [];
        $link_back_param = [];
        $message_default = [
            'success' => 'Data berhasil dihapus',
            'error' => 'Maaf data tidak berhasil dihapus',
        ];

        $kode = !empty($request->data_sent) ? $request->data_sent : null;

        try {
            $model = (new \App\Models\UserManagement\UxuiUsers)->where('id', '=', $kode)->first();
            if (empty($model)) {
                return redirect()->route($this->url_name, $link_back_param)->with(['error', 'Data tidak ditemukan']);
            }

            $is_save = 0;
            if ($model->delete()) {
                $model_auth_user = (new \App\Models\UserManagement\UxuiAuthUsers)->where('id', '=', $model->id)->first();
                if ($model_auth_user->delete()) {
                    $is_save = 1;
                }
            }

            if ($is_save) {
                DB::commit();
                $pesan = ['success', $message_default['success'], 2];
            } else {
                DB::rollBack();
                $pesan = ['error', $message_default['error'], 3];
            }
        } catch (\Illuminate\Database\QueryException $e) {
            DB::rollBack();
            if ($e->errorInfo[1] == '1062') {
            }
            $pesan = ['error', $message_default['error'], 3];
        } catch (\Throwable $e) {
            DB::rollBack();
            $pesan = ['error', $message_default['error'], 3];
        }

        return redirect()->route($this->url_name, $link_back_param)->with([$pesan[0] => $pesan[1]]);
    }
}
