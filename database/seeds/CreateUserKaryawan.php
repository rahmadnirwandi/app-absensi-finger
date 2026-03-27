<?php

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class CreateUserKaryawan extends Seeder
{
    public function run()
    {
        try {
            DB::beginTransaction();

            // ambil NIP sebagai username
            $karyawans = DB::table('ref_karyawan')
                ->select('id_karyawan', 'nip')
                ->whereNotNull('nip')
                ->get();

            $key_encripsi = (new \App\Services\AuthService)->key_encripsi();

            foreach ($karyawans as $row) {

                $hashPassword = Hash::make('12345');

                $userId = DB::table('uxui_users')->insertGetId([
                    'username' => $row->nip,
                    'password' => DB::raw("AES_ENCRYPT('" . $hashPassword . "','" . $key_encripsi . "')"),
                    'status' => 1,
                    'created' => now(),
                    'remember_token' => null,
                ]);

                // INSERT auth user (group karyawan)
                DB::table('uxui_auth_users')->insert([
                    'id_user' => $userId,
                    'alias_group' => 'group_karyawan',
                ]);

                DB::table('uxui_users_karyawan')->insert([
                    'id_uxui_users' => $userId,
                    'id_karyawan' => $row->id_karyawan,
                ]);
            }

            DB::commit();

        } catch (\Throwable $e) {
            DB::rollBack();
            dd($e);
        }
    }
}
