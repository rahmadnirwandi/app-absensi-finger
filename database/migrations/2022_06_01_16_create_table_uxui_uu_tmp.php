<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateTableUxuiUuTmp extends \App\Classes\MyMigration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {

        $table_name='uxui_uu_tmp';
        $table=new Blueprint($table_name);
        $table->integer('id')->length(10)->unsigned();
        $table->string('uraian',300);
        $table->foreign(['id'],$table_name.'_fk1')->references(['id'])->on('uxui_users')->onUpdate('cascade')->onDelete('cascade');
        $this->set_table($table_name,$table);
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        $table_name='uxui_uu_tmp';
        if (Schema::hasTable($table_name)) {
            Schema::dropIfExists($table_name);
        }
    }
}
