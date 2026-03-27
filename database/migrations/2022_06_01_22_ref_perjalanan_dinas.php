<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class RefPerjalananDinas extends \App\Classes\MyMigration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        $table_name='ref_perjalanan_dinas';
        if (!Schema::hasTable($table_name)) {
            Schema::create($table_name, function (Blueprint $table) use ($table_name) {
                $table->charset = 'latin1';
                $table->collation = 'latin1_swedish_ci';

                $table->increments('id_spd');
                $table->integer('id_karyawan')->length(10)->unsigned();
                $table->smallInteger('jenis_dinas');
                $table->date('tgl_mulai');
                $table->date('tgl_selesai');
                $table->smallInteger('jumlah');

                $table->unique(['id_karyawan','tgl_mulai','tgl_selesai'],$table_name.'_uniq');
            });
        }

        $field='uraian';
        $primary_key='id_spd';
        if (!Schema::hasColumn($table_name,$primary_key,$field)){
            DB::statement("ALTER TABLE ".$table_name." ADD ".$primary_key." INT(10) PRIMARY KEY AUTO_INCREMENT");
            DB::statement("ALTER TABLE ".$table_name." ADD ".$field." varchar(100) NOT NULL");
        }

        if (Schema::hasTable($table_name)) {
            $table=new Blueprint($table_name);
            $params=['dropForeign'=>['id_karyawan']];
            $this->update_table($table_name,$table,$params);
        }

        $another_create_string=['uraian'=>100];
        foreach($another_create_string as $value => $length){
            if (!Schema::hasColumn($table_name, $value)){
                Schema::table($table_name, function (Blueprint $table) use ($value,$length){
                    $table->string($value,$length);
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
        $table_name='ref_perjalanan_dinas';
        if (Schema::hasTable($table_name)) {
            Schema::dropIfExists($table_name);
        }
    }
}
