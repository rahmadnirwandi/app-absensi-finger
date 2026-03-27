<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Log;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Session;

use App\Services\AuthService;

class AuthController extends Controller
{
    protected $authService;

    public function __construct(){
        $this->authService = new AuthService;
    }

    public function index()
    {
        if (Auth::user()) {
            return redirect('/');
        }

        return view('auth/login');
    }

    public function login(Request $request)
    {
        

        if (Auth::user()) {
            return redirect('/');
        }

        $request->validate([
            'id_user' => 'required',
            'password' => 'required'
        ]);

        // Cek koneksi database
        try {
            \DB::connection()->getPDO();
            Log::info('Database connection success');
        } catch (\Exception $e) {
            Log::error('Database connection failed', [
                'error' => $e->getMessage()
            ]);

            Session::put(
                'login_error_message',
                'Silahkan hubungi IT support applikasi tidak connect dengan database'
            );

            return view('auth/login');
        }

        try {

            $user = $this->authService->getUserByCredential(
                $request->id_user,
                $request->password
            );

            if (!empty($user[0] == 'error')) {
                Log::warning('Login failed from AuthService', [
                    'message' => $user[1] ?? 'Unknown error'
                ]);

                Session::put('login_error_message', $user[1]);
                return view('auth/login');
            }

            $user = !empty($user[1]) ? $user[1] : null;

            if ($user) {
                Log::info('User found, logging in', [
                    'user_id' => $user->id
                ]);

                Auth::loginUsingId($user->id, true);
                Session::put('get_id_user', $user->id);
                $request->session()->regenerate();

                Log::info('Login success', [
                    'user_id' => $user->id
                ]);
            } else {
                Log::warning('User not found after AuthService call');
            }

        } catch (\Illuminate\Database\QueryException $e) {

            Log::error('QueryException during login', [
                'sql_error_code' => $e->errorInfo[1] ?? null,
                'message' => $e->getMessage()
            ]);

            if (($e->errorInfo[1] ?? null) == '1054') {
                Session::put(
                    'login_error_message',
                    'Silahkan hubungi IT support untuk tetap dapat mengakses aplikasi'
                );
            } else {
                Session::put('login_error_message', 'Id user atau password salah');
            }

            return view('auth/login');

        } catch (\Throwable $e) {

            Log::critical('Unexpected error during login', [
                'message' => $e->getMessage(),
                'file' => $e->getFile(),
                'line' => $e->getLine()
            ]);

            Session::put('login_error_message', 'Id user atau password salah');
            return view('auth/login');
        }

        return redirect()->intended('/');
    }


    public function logout(Request $request)
    {
        if (Auth::check()) {
            Auth::logout();
            $request->session()->invalidate();
            $request->session()->regenerateToken();
        }
        $request->session()->flush();

        return redirect('login');
    }
}
