<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

class RefKaryawanJadwalShiftWaktu extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        $table_name='ref_karyawan_jadwal_shift_waktu';
        if (!Schema::hasTable($table_name)) {
            Schema::create($table_name, function (Blueprint $table) use ($table_name) {
                $table->charset = 'latin1';
                $table->collation = 'latin1_swedish_ci';
                $table->integer('id_karyawan')->length(10)->unsigned();
                $table->integer('id_template_jadwal_shift')->length(10)->unsigned();
                $table->date('tanggal');
                $table->longText('data');
                $table->text('data_item');

                $table->unique(['id_karyawan','id_template_jadwal_shift','tanggal'],$table_name.'_uniq');
            });
        }

        $another_create_string=['id_template_jadwal_shift'=>['type'=>'INT','length'=>10,'option'=>'UNSIGNED NOT NULL']];
        foreach($another_create_string as $key => $value){
            if (Schema::hasColumn($table_name, $key)){
                DB::statement("ALTER TABLE ".$table_name." MODIFY ".$key." ".$value['type']."(".$value['length'].")".$value['option']." ");
            }
        }

        $doctrineTable = Schema::getConnection()->getDoctrineSchemaManager()->listTableDetails($table_name);
        $another_create_foreign=[
            $table_name.'_fk1'=>[
                'key'=>['id_karyawan'],
                'target'=>['id_karyawan'],
                'tbl'=>'ref_karyawan'
            ],
            $table_name.'_fk2'=>[
                'key'=>['id_template_jadwal_shift'],
                'target'=>['id_template_jadwal_shift'],
                'tbl'=>'ref_template_jadwal_shift'
            ]
        ];
        foreach($another_create_foreign as $key => $value){
            if(!$doctrineTable->hasForeignKey($key)){
                Schema::table($table_name, function($table) use ($key,$value) {
                    $table->foreign($value['key'],$key)->references($value['target'])->on($value['tbl'])->onUpdate('cascade')->onDelete('cascade');
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
        $table_name='ref_karyawan_jadwal_shift_waktu';
        if (Schema::hasTable($table_name)) {
            Schema::dropIfExists($table_name);
        }
    }
}
