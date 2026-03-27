<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

class RefKaryawanJadwal extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        $table_name='ref_karyawan_jadwal';
        if (!Schema::hasTable($table_name)) {
            Schema::create($table_name, function (Blueprint $table) use ($table_name) {
                $table->charset = 'latin1';
                $table->collation = 'latin1_swedish_ci';
                $table->integer('id_karyawan')->length(10)->unsigned();
                $table->integer('id_jenis_jadwal')->length(10)->unsigned();
                
                $table->unique(['id_karyawan'],$table_name.'_uniq');
                $table->foreign(['id_karyawan'],$table_name.'_fk1')->references(['id_karyawan'])->on('ref_karyawan')->onUpdate('cascade')->onDelete('cascade');
                $table->foreign(['id_jenis_jadwal'],$table_name.'_fk2')->references(['id_jenis_jadwal'])->on('ref_jenis_jadwal')->onUpdate('cascade')->onDelete('cascade');
            });
        }

        $table_name='ref_karyawan_jadwal';
        Schema::table($table_name, function (Blueprint $table) use ($table_name) {
            $sm = Schema::getConnection()->getDoctrineSchemaManager();
            $indexesFound = $sm->listTableIndexes($table_name);

            if(!array_key_exists($table_name.'_uniq1', $indexesFound)){
                $table->unique(['id_karyawan'],$table_name.'_uniq1');
            }

            if(array_key_exists($table_name.'_uniq', $indexesFound)){
                $table->dropUnique($table_name.'_uniq');
            }
        });
        
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
