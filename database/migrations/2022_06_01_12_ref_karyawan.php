<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

// class RefKaryawan extends Migration
class RefKaryawan extends \App\Classes\MyMigration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        $table_name='ref_karyawan';
        $table=new Blueprint($table_name);
        $table->increments('id_karyawan');
        $table->string('nm_karyawan',50);
        $table->string('alamat',100);
        $table->string('nip',20);
        $table->smallInteger('id_jabatan');
        $table->smallInteger('id_departemen');
        $table->smallInteger('id_ruangan');
        $table->smallInteger('id_status_karyawan');
        // $table->foreign(['id_jabatan'],$table_name.'_1')->references(['id_jabatan'])->on('ref_jabatan')->onUpdate('cascade')->onDelete('NO ACTION');
        // $table->foreign(['id_departemen'],$table_name.'_2')->references(['id_departemen'])->on('ref_departemen')->onUpdate('cascade')->onDelete('NO ACTION');
        $this->set_table($table_name,$table);

        $field='created';
        if (!Schema::hasColumn($table_name,$field)){
            DB::statement("ALTER TABLE ".$table_name." ADD ".$field." TIMESTAMP DEFAULT CURRENT_TIMESTAMP NOT NULL");
        }
        DB::statement("ALTER TABLE ".$table_name." MODIFY ".$field." TIMESTAMP DEFAULT CURRENT_TIMESTAMP NOT NULL");

        if (Schema::hasTable($table_name)) {
            $table=new Blueprint($table_name);
            $params=['dropForeign'=>['id_jabatan','id_departemen','id_status_karyawan']];
            $this->update_table($table_name,$table,$params);
        }
        
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        $table_name='ref_karyawan';
        if (Schema::hasTable($table_name)) {
            Schema::dropIfExists($table_name);
        }
    }
}