<?php

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class SettingTabelSeeder extends Seeder
{
    public function run()
    {
        try {
            DB::statement('SET FOREIGN_KEY_CHECKS=0;');

            $table_name='migrations';
            if (Schema::hasTable($table_name)) {
                $table=DB::table($table_name);
                $table->truncate();
            }

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