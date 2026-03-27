<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

class RefTemplateJadwalShiftWaktu extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        $table_name='ref_template_jadwal_shift_waktu';
        if (!Schema::hasTable($table_name)) {
            Schema::create($table_name, function (Blueprint $table) use ($table_name) {
                $table->charset = 'latin1';
                $table->collation = 'latin1_swedish_ci';

                $table->integer('id_template_jadwal_shift_detail');
                $table->integer('id_jenis_jadwal');
                $table->smallInteger('type')->comment('1=jadwal,2=>libur');
                $table->text('tgl');

                $table->unique(['id_template_jadwal_shift_detail','id_jenis_jadwal'],$table_name.'_uniq');
            });
        }

        $another_create_string=[
            'id_template_jadwal_shift_detail'=>['type'=>'INT','length'=>10,'option'=>'UNSIGNED NOT NULL'],
            'id_jenis_jadwal'=>['type'=>'INT','length'=>10,'option'=>'']
        ];
        foreach($another_create_string as $key => $value){
            if (Schema::hasColumn($table_name, $key)){
                DB::statement("ALTER TABLE ".$table_name." MODIFY ".$key." ".$value['type']."(".$value['length'].")".$value['option']." ");
            }
        }

        $doctrineTable = Schema::getConnection()->getDoctrineSchemaManager()->listTableDetails($table_name);
        $another_create_foreign=[
            $table_name.'_fk1'=>[
                'key'=>['id_template_jadwal_shift_detail'],
                'target'=>['id_template_jadwal_shift_detail'],
                'tbl'=>'ref_template_jadwal_shift_detail'
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
        $table_name='ref_template_jadwal_shift_waktu';
        if (Schema::hasTable($table_name)) {
            Schema::dropIfExists($table_name);
        }
    }
}
