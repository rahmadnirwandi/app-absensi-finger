<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

class RefTemplateJadwalShiftLanjutan extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        $table_name='ref_template_jadwal_shift_lanjutan';
        if (!Schema::hasTable($table_name)) {
            Schema::create($table_name, function (Blueprint $table) use ($table_name) {
                $table->charset = 'latin1';
                $table->collation = 'latin1_swedish_ci';

                $table->integer('id_template_jadwal_shift');
                $table->year('tahun');
                $table->smallInteger('bulan');
                $table->longText('data');

                $table->unique(['id_template_jadwal_shift','tahun','bulan'],$table_name.'_uniq');
            });
        }

        $another_create_string=[
            'id_template_jadwal_shift'=>['type'=>'INT','length'=>10,'option'=>'UNSIGNED NOT NULL']
        ];
        foreach($another_create_string as $key => $value){
            if (Schema::hasColumn($table_name, $key)){
                DB::statement("ALTER TABLE ".$table_name." MODIFY ".$key." ".$value['type']."(".$value['length'].")".$value['option']." ");
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
        $table_name='ref_template_jadwal_shift_lanjutan';
        if (Schema::hasTable($table_name)) {
            Schema::dropIfExists($table_name);
        }
    }
}
