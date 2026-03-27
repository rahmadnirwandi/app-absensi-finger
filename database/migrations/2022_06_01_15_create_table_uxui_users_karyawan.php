<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

class CreateTableUxuiUsersKaryawan extends \App\Classes\MyMigration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {

        $table_name='uxui_users_karyawan';
        $table=new Blueprint($table_name);
        $table->increments('id_users_karyawan');
        $table->integer('id_uxui_users')->length(10)->unsigned();
        $table->integer('id_karyawan')->length(10)->unsigned();
        $table->foreign(['id_uxui_users'],$table_name.'_fk1')->references(['id'])->on('uxui_users')->onUpdate('cascade')->onDelete('cascade');
        $table->foreign(['id_karyawan'],$table_name.'_fk2')->references(['id_karyawan'])->on('ref_karyawan')->onUpdate('cascade')->onDelete('cascade');
        $this->set_table($table_name,$table);
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        $table_name='uxui_users_karyawan';
        if (Schema::hasTable($table_name)) {
            Schema::dropIfExists($table_name);
        }
    }
}
