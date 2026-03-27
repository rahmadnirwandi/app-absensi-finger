<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

class RefKaryawanJadwalShift extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        $table_name='ref_karyawan_jadwal_shift';
        if (!Schema::hasTable($table_name)) {
            Schema::create($table_name, function (Blueprint $table) use ($table_name) {
                $table->charset = 'latin1';
                $table->collation = 'latin1_swedish_ci';
                $table->integer('id_karyawan')->length(10)->unsigned();
                $table->integer('id_template_jadwal_shift')->length(10)->unsigned();

                $table->unique(['id_karyawan'],$table_name.'_uniq1');
                $table->foreign(['id_karyawan'],$table_name.'_fk1')->references(['id_karyawan'])->on('ref_karyawan')->onUpdate('cascade')->onDelete('cascade');
                $table->foreign(['id_template_jadwal_shift'],$table_name.'_fk2')->references(['id_template_jadwal_shift'])->on('ref_template_jadwal_shift')->onUpdate('cascade')->onDelete('cascade');
            });
        }

        $another_create_string=['id_template_jadwal_shift'=>['type'=>'INT','length'=>10,'option'=>'UNSIGNED NOT NULL']];
        foreach($another_create_string as $key => $value){
            if (Schema::hasColumn($table_name, $key)){
                DB::statement("ALTER TABLE ".$table_name." MODIFY ".$key." ".$value['type']."(".$value['length'].")".$value['option']." ");
            }
        }

        $table_name='ref_karyawan_jadwal_shift';
        Schema::table($table_name, function (Blueprint $table) use ($table_name) {
            $sm = Schema::getConnection()->getDoctrineSchemaManager();
            $indexesFound = $sm->listTableIndexes($table_name);

            if(array_key_exists($table_name.'_uniq', $indexesFound)){
                $table->dropUnique($table_name.'_uniq');
            }

            if(array_key_exists($table_name.'_uniq2', $indexesFound)){
                $table->dropUnique($table_name.'_uniq2');
            }

            if(!array_key_exists($table_name.'_uniq1', $indexesFound)){
                $table->unique(['id_karyawan'],$table_name.'_uniq1');
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
        $table_name='ref_karyawan_jadwal_shift';
        if (Schema::hasTable($table_name)) {
            Schema::dropIfExists($table_name);
        }
    }
}
