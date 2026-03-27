<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

class CreateUserAksesTables extends \App\Classes\MyMigration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        if (!Schema::hasTable('uxui_auth_routes')) {
            Schema::create('uxui_auth_routes', function (Blueprint $table) {
                $table->charset = 'latin1';
                $table->collation = 'latin1_swedish_ci';

                $table->increments('id');
                $table->string('title',255); 
                $table->string('url',255); 
                $table->string('controller',255);
                $table->string('type',255);
                $table->timestamp('created')->default(DB::raw('CURRENT_TIMESTAMP'));

                $table->unique(['title', 'url', 'controller', 'type']);
            });
        }

        if (!Schema::hasTable('uxui_auth_group')) {
            Schema::create('uxui_auth_group', function (Blueprint $table) {
                $table->charset = 'latin1';
                $table->collation = 'latin1_swedish_ci';

                $table->increments('id');
                $table->string('name',100);
                $table->string('alias',100);
                $table->string('keterangan',255);
                $table->timestamp('created')->default(DB::raw('CURRENT_TIMESTAMP'));

                $table->unique(['name','alias']);

                $table->index(['alias'], 'uxui_auth_group_alias_index');
            });
        }

        $table_name='uxui_auth_permission';
        $table=new Blueprint($table_name);
        $table->string('alias_group',100); 
        $table->string('url',255);                
        $table->unique(['alias_group', 'url']);
        $this->set_table($table_name,$table);

        $table_name='uxui_auth_users';
        $table=new Blueprint($table_name);
        $table->string('id',700);
        $table->string('id_user',700);
        $table->string('alias_group',100);
        $table->unique(['id', 'id_user', 'alias_group']);
        $table->foreign('alias_group')->references('alias')->on('uxui_auth_group')->onDelete('cascade')->onUpdate('cascade');
        $this->set_table($table_name,$table);

    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        if (Schema::hasTable('uxui_auth_users')) {
            Schema::dropIfExists('uxui_auth_users');
        }

        if (Schema::hasTable('uxui_auth_routes')) {
            Schema::dropIfExists('uxui_auth_routes');
        }

        if (Schema::hasTable('uxui_auth_group')) {
            Schema::dropIfExists('uxui_auth_group');
        }

        if (Schema::hasTable('uxui_auth_permission')) {
            Schema::dropIfExists('uxui_auth_permission');
        }
    }
}
