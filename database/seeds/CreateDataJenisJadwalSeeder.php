<?php

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class CreateDataJenisJadwalSeeder extends Seeder
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
            $table=(new \App\Models\RefJenisJadwal());
            $check=$table::count();
            if($check==0){
                DB::statement('SET FOREIGN_KEY_CHECKS=0;');
                $db=$table::truncate();
                DB::statement('SET FOREIGN_KEY_CHECKS=1;');

                DB::statement("ALTER TABLE ".( new \App\Models\RefJenisJadwal )->table." AUTO_INCREMENT = 1");

                $table::insertOrIgnore([
                    'id_jenis_jadwal' => 1,
                    'nm_jenis_jadwal'=>'Jadwal Rutin',
                    'masuk_kerja'=>'08:00:00',
                    'pulang_kerja'=>'16:45:00',
                    'awal_istirahat'=>'12:00:00',
                    'akhir_istirahat'=>'14:00:00',
                    'hari_kerja'=>'Mon,Tue,Wed,Thu,Fri',
                ]);
            }
        } catch(\Illuminate\Database\QueryException $e){
            if($e->errorInfo[1] == '1062'){

            }
            dd($e);
        } catch (\Throwable $e) {
            dd($e);
        }


        try {
            $table=(new \App\Models\RefJadwal());
            $check=$table::count();
            if($check==0){
                DB::statement('SET FOREIGN_KEY_CHECKS=0;');
                $db=$table::truncate();
                DB::statement('SET FOREIGN_KEY_CHECKS=1;');

                DB::statement("ALTER TABLE ".( new \App\Models\RefJadwal )->table." AUTO_INCREMENT = 1");

                $get_data=(new \App\Models\RefJenisJadwal)->select(['id_jenis_jadwal'])->get();
                if($get_data){
                    $data_save=[];
                    foreach($get_data as $value){
                        $data_ref=(new \App\Models\RefJadwal)->referensi_data();
                        foreach($data_ref as $value_ref){
                            $data_save[]=[
                                'id_jenis_jadwal' => $value->id_jenis_jadwal,
                                'kd_jadwal' => $value_ref->kd_jadwal,
                                'uraian'=>$value_ref->uraian,
                                'alias'=>$value_ref->alias,
                                'jam_awal'=>'00:00:00',
                                'jam_akhir'=>'00:00:00',
                                'status_toren_jam_cepat'=>$value_ref->status_toren_jam_cepat,
                                'toren_jam_cepat'=>'00:00:00',
                                'status_toren_jam_telat'=>$value_ref->status_toren_jam_telat,
                                'toren_jam_telat'=>'00:00:00',
                                'status_jadwal'=>$value_ref->status_jadwal,
                            ];
                        }   
                    }
                    if(!empty($data_save)){
                        $table::insertOrIgnore($data_save);
                    }
                    
                }
            }
        } catch(\Illuminate\Database\QueryException $e){
            if($e->errorInfo[1] == '1062'){

            }
            dd($e);
        } catch (\Throwable $e) {
            dd($e);
        }
    }
}