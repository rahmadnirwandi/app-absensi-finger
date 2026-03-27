<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

class RefTemplateJadwalShift extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        $table_name='ref_template_jadwal_shift';
        if (!Schema::hasTable($table_name)) {
            Schema::create($table_name, function (Blueprint $table) use ($table_name) {
                $table->charset = 'latin1';
                $table->collation = 'latin1_swedish_ci';

                $table->increments('id_template_jadwal_shift');
                $table->string('nm_shift');
            });
        }

        $table_name='ref_template_jadwal_shift_detail';
        if (!Schema::hasTable($table_name)) {
            Schema::create($table_name, function (Blueprint $table) use ($table_name) {
                $table->charset = 'latin1';
                $table->collation = 'latin1_swedish_ci';

                $table->increments('id_template_jadwal_shift_detail');
                $table->integer('id_template_jadwal_shift');
                $table->date('tgl_mulai');
                $table->smallInteger('jml_periode');
                $table->smallInteger('type_periode');

                $table->unique(['id_template_jadwal_shift','jml_periode'],$table_name.'_uniq');
            });
        }

        $another_create_string=['id_template_jadwal_shift'=>['type'=>'INT','length'=>10,'option'=>'UNSIGNED NOT NULL']];
        foreach($another_create_string as $key => $value){
            if (Schema::hasColumn($table_name, $key)){
                DB::statement("ALTER TABLE ".$table_name." MODIFY ".$key." ".$value['type']."(".$value['length'].")".$value['option']." ");
            }
        }

        $doctrineTable = Schema::getConnection()->getDoctrineSchemaManager()->listTableDetails($table_name);
        $another_create_foreign=[$table_name.'_fk1'=>['key'=>['id_template_jadwal_shift'],'target'=>['id_template_jadwal_shift'],'tbl'=>'ref_template_jadwal_shift']];
        foreach($another_create_foreign as $key => $value){
            if(!$doctrineTable->hasForeignKey($key)){
                Schema::table($table_name, function($table) use ($key,$value) {
                    $table->foreign($value['key'],$key)->references($value['target'])->on($value['tbl'])->onUpdate('cascade')->onDelete('cascade');
                });
            }
        }

        $table_name='ref_template_jadwal_shift_detail';
        Schema::table($table_name, function (Blueprint $table) use ($table_name) {
            $sm = Schema::getConnection()->getDoctrineSchemaManager();
            $indexesFound = $sm->listTableIndexes($table_name);

            if(!array_key_exists($table_name.'_uniq1', $indexesFound)){
                $table->unique(['id_template_jadwal_shift'],$table_name.'_uniq1');
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
        $table_name='ref_template_jadwal_shift';
        if (Schema::hasTable($table_name)) {
            Schema::dropIfExists($table_name);
        }

        $table_name='ref_template_jadwal_shift_detail';
        if (Schema::hasTable($table_name)) {
            Schema::dropIfExists($table_name);
        }
    }
}
