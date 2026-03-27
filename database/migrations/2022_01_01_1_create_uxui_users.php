<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

class CreateUxuiUsers extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        if (!Schema::hasTable('uxui_users')) {
            Schema::create('uxui_users', function (Blueprint $table) {
                $table->charset = 'latin1';
                $table->collation = 'latin1_swedish_ci';
                $table->increments('id');
                $table->string('username',150)->unique();
                $table->string('password',300);
                $table->boolean('status')->default(1);
                $table->timestamp('created')->default(DB::raw('CURRENT_TIMESTAMP'));
                $table->rememberToken();
            });
        }
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        if (Schema::hasTable('uxui_users')) {
            Schema::dropIfExists('uxui_users');
        }
    }
}
