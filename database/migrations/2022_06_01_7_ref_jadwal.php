<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

class RefJadwal extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        $table_name='ref_jadwal';
        if (!Schema::hasTable($table_name)) {
            Schema::create($table_name, function (Blueprint $table) use ($table_name) {
                $table->charset = 'latin1';
                $table->collation = 'latin1_swedish_ci';
                $table->increments('id_jadwal');
                $table->smallInteger('kd_jadwal');
                $table->integer('id_jenis_jadwal')->length(10)->unsigned();
                $table->string('uraian');
                $table->time('jam_awal');
                $table->time('jam_akhir');
                $table->string('alias');
                $table->smallInteger('status_toren_jam_cepat');
                $table->time('toren_jam_cepat');
                $table->smallInteger('status_toren_jam_telat');
                $table->time('toren_jam_telat');
                $table->smallInteger('status_jadwal');
                $table->foreign(['id_jenis_jadwal'],$table_name.'_fk1')->references(['id_jenis_jadwal'])->on('ref_jenis_jadwal')->onUpdate('cascade')->onDelete('cascade');
            });
        }
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        $table_name='ref_jadwal';
        if (Schema::hasTable($table_name)) {
            Schema::dropIfExists($table_name);
        }
    }
}
