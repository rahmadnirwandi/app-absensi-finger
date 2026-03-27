<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;
use App\Http\Traits\GlobalFunction;
use App\Services\UserManagement\UserGroupAppService;
use App\Services\UserManagement\PermissionGroupAppService;

class UserGroupAppController extends Controller
{
    public $part_view, $url_index, $url_name,$url_name_index, $title, $breadcrumbs, $globalService;
    public $userGroupAppService,$permissionGroupAppService,$globalFunction;

    public function __construct() {

        $this->title='User Group';
        $this->url_name_index='user_group_app';
        $this->breadcrumbs=[
            ['title'=>'Manajemen User','url'=>url('/')."/sub-menu?type=2"],
            ['title'=>$this->title,'url'=>url('/')."/user-group-app"],
        ];

        $this->userGroupAppService = new UserGroupAppService;
        $this->permissionGroupAppService = new PermissionGroupAppService;
        $this->globalFunction = new GlobalFunction;
    }

    function actionIndex(Request $request)
    {

        $filter_search_text = !empty($request->form_filter_text) ? $request->form_filter_text : '';
        $dataList = $this->userGroupAppService->getUxuiAuthGroup(['search' => $filter_search_text]);

        $parameter_view=[
            'title'=>$this->title,
            'breadcrumbs'=>$this->breadcrumbs,
            "dataList" => $dataList
        ];
        return view('user-management.user-group.index', $parameter_view);
    }


    function form(Request $request)
    {

        $kode = !empty($request->data_sent) ? $request->data_sent : null;
        $model = $this->userGroupAppService->uxuiAuthGroup->where('id', '=', $kode)->first();
        if ($model) {
            $action_form = 'user-group-app/update';
        } else {
            $action_form = 'user-group-app/create';
        }

        $parameter_view = [
            'action_form' => $action_form,
            'model' => $model
        ];

        $bagan_html = 'user-management.user-group.form';

        if ($request->ajax()) {
            $returnHTML = view($bagan_html, $parameter_view)->render();
            return response()->json(array('success' => true, 'html' => $returnHTML));
        } else {
            return view($bagan_html, $parameter_view);
        }
    }

    function actionCreate(Request $request)
    {
        $name = !empty($request->name) ? $this->globalFunction->remove_multiplespace($request->name) : null;
        $link_back = 'user_group_app';
        $link_back_param = [];

        if (empty($name)) {
            return redirect()->route($link_back, $link_back_param)->with(['error' => 'Pastikan anda mengisi nama group']);
        }

        DB::beginTransaction();
        $fields = $request->all();

        $message_default = [
            'success' => 'Level Akses Baru Berhasil di Tambah',
            'error' => 'Level Akses Baru Gagal di Tambah',
        ];

        try {
            $data_save = $fields;
            unset($data_save["_token"]);
            unset($data_save["key_old"]);
            $data_save['name'] = $this->globalFunction->remove_multiplespace($data_save['name']);
            $data_save['keterangan'] = $this->globalFunction->remove_multiplespace($data_save['keterangan']);
            $data_save['alias'] = 'group_' . str_replace(" ", "_", strtolower($name));

            $check_alias = $this->userGroupAppService->uxuiAuthGroup::where('alias', $data_save['alias'])->first();
            if ($check_alias) {
                return redirect()->route($link_back, $link_back_param)->with(['error' => 'Group route sudah terdaftar']);
            }

            if ($data_save) {
                $model_save = $this->userGroupAppService->insertAuthGroup($data_save);

                $is_save = 0;
                if ($model_save) {
                    $is_save = 1;
                }

                if ($is_save) {
                    DB::commit();
                    $pesan = ['success' => $message_default['success']];
                } else {
                    DB::rollBack();
                    $pesan = ['error' => $message_default['error']];
                }
            } else {
                $pesan = ['error' => $message_default['error']];
            }

            return redirect()->route($link_back, $link_back_param)->with($pesan);
        } catch (\Illuminate\Database\QueryException $e) {
            DB::rollBack();
            if ($e->errorInfo[1] == '1062') {
                return redirect()->route($link_back, $link_back_param)->with(['error' => 'Maaf gagal menyimpan data']);
            }
            return redirect()->route($link_back, $link_back_param)->with(['error' => $message_default['error']]);
        } catch (\Throwable $e) {
            DB::rollBack();
            return redirect()->route($link_back, $link_back_param)->with(['error' => $message_default['error']]);
        }
    }

    function actionUpdate(Request $request)
    {

        $name = !empty($request->name) ? $this->globalFunction->remove_multiplespace($request->name) : null;
        $key_old = !empty($request->key_old) ? $request->key_old : null;
        $link_back = 'user_group_app';
        $link_back_param = [];

        if (empty($name)) {
            return redirect()->route($link_back, $link_back_param)->with(['error' => 'Pastikan anda mengisi nama group']);
        }

        DB::beginTransaction();
        $fields = $request->all();

        $message_default = [
            'success' => 'Level Akses Berhasil di Ubah',
            'error' => 'Level Akses Gagal di Ubah',
        ];

        try {
            $data_save = $fields;
            unset($data_save["_token"]);
            unset($data_save["key_old"]);
            // remove_multiplespace
            $data_save['name'] = $this->globalFunction->remove_multiplespace($data_save['name']);
            $data_save['keterangan'] = $this->globalFunction->remove_multiplespace($data_save['keterangan']);
            $data_save['alias'] = 'group_' . str_replace(" ", "_", strtolower($name));

            if ($data_save) {
                $is_save = 0;
                $data_old = $this->userGroupAppService->uxuiAuthGroup->where('id', '=', $key_old)->first();
                $paramater_where = [
                    'id' => $key_old
                ];
                $model_save = $this->userGroupAppService->updateAuthGroup($paramater_where, $data_save);

                if ($model_save) {
                    $check = $this->userGroupAppService->uxuiAuthPermission->where('alias_group', '=', $data_old->alias)->count('alias_group');
                    if ($check && $data_save['alias'] != $data_old->alias) {
                        $data_save1 = [
                            'alias_group' => $data_save['alias']
                        ];
                        $paramater_where1 = [
                            'alias_group' => $data_old->alias
                        ];
                        $model_save1 = $this->permissionGroupAppService->updateAuthPermission($paramater_where1, $data_save1);
                        if ($model_save1) {
                            $is_save = 1;
                        }
                    } else {
                        $is_save = 1;
                    }
                }

                if ($is_save) {
                    DB::commit();
                    $pesan = ['success' => $message_default['success']];
                } else {
                    DB::rollBack();
                    $pesan = ['error' => $message_default['error']];
                }
            } else {
                $pesan = ['error' => $message_default['error']];
            }

            return redirect()->route($link_back, $link_back_param)->with($pesan);
        } catch (\Illuminate\Database\QueryException $e) {
            DB::rollBack();
            if ($e->errorInfo[1] == '1062') {
                return redirect()->route($link_back, $link_back_param)->with(['error' => 'Maaf gagal menyimpan data']);
            }
            return redirect()->route($link_back, $link_back_param)->with(['error' => $message_default['error']]);
        } catch (\Throwable $e) {
            DB::rollBack();
            return redirect()->route($link_back, $link_back_param)->with(['error' => $message_default['error']]);
        }
    }

    function actionDelete(Request $request)
    {
        DB::beginTransaction();
        $kode = !empty($request->data_sent) ? $request->data_sent : null;

        $link_back = 'user_group_app';
        $link_back_param = [];
        $message_default = [
            'success' => 'Level Akses Berhasil di Hapus',
            'error' => 'Level Akses Gagal di Hapus'
        ];

        try {
            $model = $this->userGroupAppService->uxuiAuthGroup->where('id', '=', $kode)->first();

            $check = $this->userGroupAppService->uxuiAuthPermission->where('alias_group', '=', $model->alias)->count('alias_group');
            if ($check) {
                return redirect()->route($link_back, $link_back_param)->with(['error' => 'Level Akses Sudah Memiliki Hak Permission']);
            }

            if (!empty($kode)) {
                $paramater_where = [
                    'id' => $kode
                ];
                $deleteUserGroup = $this->userGroupAppService->deleteAuthGroup($paramater_where);
                if ($deleteUserGroup) {
                    DB::commit();
                    $pesan = ['success' => $message_default['success']];
                } else {
                    DB::rollBack();
                    $pesan = ['error' => $message_default['error']];
                }
                return redirect()->route($link_back, $link_back_param)->with($pesan);
            }
            return redirect()->route($link_back, $link_back_param)->with(['error' => $message_default['error']]);
        } catch (\Illuminate\Database\QueryException $e) {
            DB::rollBack();
            if ($e->errorInfo[1] == '1062') {
                return redirect()->route($link_back, $link_back_param)->with(['error' => 'Maaf tidak dapat menyimpan data yang sama']);
            }
            return redirect()->route($link_back, $link_back_param)->with(['error' => $message_default['error']]);
        } catch (\Throwable $e) {
            DB::rollBack();
            return redirect()->route($link_back, $link_back_param)->with(['error' => $message_default['error']]);
        }
    }
}