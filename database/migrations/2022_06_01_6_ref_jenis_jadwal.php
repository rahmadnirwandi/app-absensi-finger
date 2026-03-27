<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

class RefJenisJadwal extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        $table_name='ref_jenis_jadwal';
        if (!Schema::hasTable($table_name)) {
            Schema::create($table_name, function (Blueprint $table) use ($table_name) {
                $table->charset = 'latin1';
                $table->collation = 'latin1_swedish_ci';
                $table->increments('id_jenis_jadwal');
                $table->string('nm_jenis_jadwal');
                $table->time('masuk_kerja');
                $table->time('pulang_kerja');
                $table->smallInteger('pulang_kerja_next_day');
                $table->time('awal_istirahat')->nullable();
                $table->time('akhir_istirahat')->nullable();
                $table->smallInteger('akhir_istirahat_next_day');
                $table->smallInteger('type_jenis');
                $table->string('bg_color')->length(10);
            });
        }

        $another_create_time=['masuk_kerja','pulang_kerja'];
        foreach($another_create_time as $value){
            if (!Schema::hasColumn($table_name, $value)){
                Schema::table($table_name, function (Blueprint $table) use ($value){
                    $table->time($value);
                });
            }
        }

        $another_create_time=['awal_istirahat','akhir_istirahat'];
        foreach($another_create_time as $value){
            if (!Schema::hasColumn($table_name, $value)){
                Schema::table($table_name, function (Blueprint $table) use ($value){
                    $table->time($value)->nullable();
                });
            }else{
                Schema::table($table_name, function (Blueprint $table) use ($value){
                    $table->time($value)->nullable()->change();
                });
            }
        }

        $another_create_string=['hari_kerja'=>50,'bg_color'=>10];
        foreach($another_create_string as $value => $length){
            if (!Schema::hasColumn($table_name, $value)){
                Schema::table($table_name, function (Blueprint $table) use ($value,$length){
                    $table->string($value,$length);
                });
            }
        }

        $another_create_smallInteger=['type_jenis','pulang_kerja_next_day','akhir_istirahat_next_day'];
        foreach($another_create_smallInteger as $value){
            if (!Schema::hasColumn($table_name, $value)){
                Schema::table($table_name, function (Blueprint $table) use ($value){
                    $table->smallInteger($value);
                });
            }
        }
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        $table_name='ref_jenis_jadwal';
        if (Schema::hasTable($table_name)) {
            Schema::dropIfExists($table_name);
        }
    }
}
