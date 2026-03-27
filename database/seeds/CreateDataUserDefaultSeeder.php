<?php

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class CreateDataUserDefaultSeeder extends Seeder
{

	public function __construct() {
		
	}
	/**
	 * Run the database seeds.
	 *
	 * @return void
	 */
	public function run()
	{
		try {      
			$username_default='adabsensi';
			$password_default='adabsensi321';
			$check_user=(new \App\Models\UserManagement\UxuiUsers)->select(['id'])->where('username', '=', $username_default)->first();
			if(empty($check_user)){
				DB::statement("ALTER TABLE ".( new \App\Models\UserManagement\UxuiUsers )->table." AUTO_INCREMENT = 1");
				
				$password=Hash::make($password_default);
				$model =(new \App\Models\UserManagement\UxuiUsers);
				$model->username=$username_default;
				$model->password=DB::raw("AES_ENCRYPT('".$password."','jiwana')");
				$model->save();
			}
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