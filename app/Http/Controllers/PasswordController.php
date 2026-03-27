<?php

namespace App\Http\Controllers;

use App\Services\GlobalService;
use App\Services\RefKaryawanService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class PasswordController extends Controller
{
    public $part_view, $url_index, $url_name, $title, $breadcrumbs, $globalService;
    public function __construct()
    {
        $router_name = (new \App\Http\Traits\GlobalFunction)->getRouterIndex();
        $this->part_view = $router_name->path_base;
        $this->url_index = $router_name->uri;
        $this->url_name = $router_name->router_name;

        $this->title = 'Edit Password';
        $this->breadcrumbs = [
            ['title' => $this->title, 'url' => url('/') . "/" . $this->url_index],
        ];

        $this->globalService = new GlobalService;
    }

    public function actionIndex(Request $request) {
        $user_auth = (new \App\Http\Traits\AuthFunction)->getUser();

        $parameter_view = [
            'title' => $this->title,
            'breadcrumbs' => $this->breadcrumbs,
            'model' => ['id_user' => $user_auth->id_user],
        ];

        return view('password.index', $parameter_view);
    }

    public function actionUpdate(Request $request) {
        $data_req = $request->all();
        $id_user = $data_req['id_user'];
        $current_password = $data_req['current_password'];
        $new_password = $data_req['new_password'];
        $new_password_confirmation = $data_req['new_password_confirmation'];

        $message_default = [
            'success' => 'Password berhasil diubah',
            'error'   => 'Password tidak berhasil diubah',
        ];

        try {

            $key_encripsi = (new \App\Services\AuthService)->key_encripsi();

            $user = \App\Models\UserManagement\UxuiUsers::where('id', $id_user)->first();

            if (!$user) {
                return redirect()->route($this->url_name)->with('error', 'User tidak ditemukan');
            }

            $password_db = DB::table('uxui_users')
                ->select(DB::raw("AES_DECRYPT(password, '$key_encripsi') as password"))
                ->where('id', $id_user)
                ->first();

            $password_db = $password_db->password;

            if (!Hash::check($current_password, $password_db)) {
                return redirect()->route($this->url_name)->with('error', 'Password lama salah');
            }

            if ($new_password != $new_password_confirmation) {
                return redirect()->route($this->url_name)->with('error', 'Konfirmasi password tidak sama');
            }

            $password = Hash::make($new_password);

            $user->password = DB::raw("AES_ENCRYPT('".$password."', '".$key_encripsi."')");
            $user->save();

            return redirect()
                ->route($this->url_name)
                ->with('success', $message_default['success']);

        } catch (\Exception $e) {

            return redirect()
                ->route($this->url_name)
                ->with('error', $e->getMessage());
        }
    }
}
