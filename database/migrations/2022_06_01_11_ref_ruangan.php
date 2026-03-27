<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

class RefRuangan extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        $table_name='ref_ruangan';
        if (!Schema::hasTable($table_name) ) {
            Schema::create($table_name, function (Blueprint $table) use($table_name) {
                $table->charset = 'latin1';
                $table->collation = 'latin1_swedish_ci';
                $table->smallIncrements('id_ruangan');
                $table->string('nm_ruangan',50);
                $table->smallInteger('id_departemen');
                
                $table->foreign(['id_departemen'],$table_name.'_1')->references(['id_departemen'])->on('ref_departemen')->onUpdate('cascade')->onDelete('cascade');
            });

            DB::statement("ALTER TABLE ".$table_name." MODIFY id_ruangan smallint(6) NOT NULL AUTO_INCREMENT");
        }
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        $table_name='ref_ruangan';
        if (Schema::hasTable($table_name)) {
            Schema::dropIfExists($table_name);
        }
    }
}
