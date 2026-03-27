<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

class CreateTableUxuiSettinganAppVariable extends \App\Classes\MyMigration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {

        $table_name='uxui_settingan_app_variable';
        $table=new Blueprint($table_name);
        $table->id('id_variable');
        $table->string('nm_variable',255);
        $table->string('value_variable',255);
        $table->unique(['nm_variable']);
        $this->set_table($table_name,$table);
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        if (Schema::hasTable('uxui_settingan_app_variable')) {
            Schema::dropIfExists('uxui_settingan_app_variable');
        }
    }
}
