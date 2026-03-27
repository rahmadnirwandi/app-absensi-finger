<?php

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

use App\Models\UxuiSettinganAppVariable;

class CreateDataUxuiSettingAppVariabelSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        try {
            $file_env_deafult='./.env_default';
            if (!file_exists($file_env_deafult))
            {
                dd("file .env_default tidak ditemukan");
            }else{
                $check=file_get_contents($file_env_deafult);
                if(empty($check)){
                    dd("file .env_default kosong");
                }
            }

            UxuiSettinganAppVariable::truncate();
            DB::statement('SET FOREIGN_KEY_CHECKS=0;');
            DB::statement('SET FOREIGN_KEY_CHECKS=0;');

            $file_default = fopen("./.env_default", "rb");
            $exception =[];
            while (!feof($file_default) ) {
                $line_of_text = fgets($file_default);
                $parts = explode('=', $line_of_text);
                array_push($exception,$parts[0]);
            }
            fclose($file_default);

            $config_file_path = "./.env";

            if (!file_exists($config_file_path))
            {
                dd("file .env tidak ditemukan");
            }

            $file_primary = fopen($config_file_path, "rb");

            while (!feof($file_primary) ) {
                $line_of_text = fgets($file_primary);
                $parts = explode('=', $line_of_text);
                if(count($parts)!==1){
                    if(!in_array($parts[0],$exception)){
                        $data_save[]=[
                            'nm_variable'=>$parts[0],
                            'value_variable'=>trim($parts[1])
                        ];
                    }
                }
            }
            fclose($file_primary);
            if(!empty($data_save)){
                UxuiSettinganAppVariable::insertOrIgnore($data_save);
            }
        } catch(\Illuminate\Database\QueryException $e){
            if($e->errorInfo[1] == '1062'){
                dd($e);
            }
            dd($e);
        }
        catch (\Throwable $e) {
            dd($e);
        }
    }
}