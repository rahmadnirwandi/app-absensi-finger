<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

class RefUserInfo extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        $table_name='ref_user_info';
        if (!Schema::hasTable($table_name)) {
            Schema::create($table_name, function (Blueprint $table) use ($table_name) {
                $table->charset = 'latin1';
                $table->collation = 'latin1_swedish_ci';
                $table->increments('id_user_info');
                // $table->integer('id_mesin_absensi')->length(10)->unsigned();
                $table->integer('id_user');
                $table->string('name', 40);
                $table->string('password', 20)->nullable(true);
                $table->integer('group')->nullable(true);
                $table->integer('privilege')->nullable(true);
                $table->string('card', 20)->nullable(true);
                $table->integer('pin2')->nullable(true);
                $table->integer('tz1')->nullable(true);
                $table->integer('tz2')->nullable(true);
                $table->integer('tz3')->nullable(true);
                
                $table->index('id_user');
                $table->unique(['id_user'],$table_name.'_uniq');
                // $table->foreign(['id_mesin_absensi'],$table_name.'_fk1')->references(['id_mesin_absensi'])->on('ref_mesin_absensi')->onUpdate('cascade')->onDelete('cascade');
            });
        }

        $another_create_integer=['pin'];
        foreach($another_create_integer as $value){
            if (!Schema::hasColumn($table_name, $value)){
                Schema::table($table_name, function (Blueprint $table) use ($value){
                    $table->integer($value);
                });
            }
        }

        $table_name='ref_user_info_detail';
        if (!Schema::hasTable($table_name)) {
            Schema::create($table_name, function (Blueprint $table) use ($table_name) {
                $table->charset = 'latin1';
                $table->collation = 'latin1_swedish_ci';
                
                // $table->integer('id_mesin_absensi')->length(10)->unsigned();
                $table->integer('id_user');
                $table->smallInteger('finger_id')->length(6);
                $table->string('size', 20)->nullable(true);
                $table->smallInteger('valid')->length(6);
                $table->text('finger');
                $table->unique(['id_user','finger_id'],$table_name.'_uniq');
                $table->foreign(['id_user'],$table_name.'_fk1')->references(['id_user'])->on('ref_user_info')->onUpdate('cascade')->onDelete('cascade');
            });
        }

        DB::statement("ALTER TABLE ".$table_name." MODIFY finger longtext");

        $another_create_integer=['pin'];
        foreach($another_create_integer as $value){
            if (!Schema::hasColumn($table_name, $value)){
                Schema::table($table_name, function (Blueprint $table) use ($value){
                    $table->integer($value);
                });
            }
        }


        $table_name='user_mesin_tmp';
        if (!Schema::hasTable($table_name)) {
            Schema::create($table_name, function (Blueprint $table) use ($table_name) {
                $table->charset = 'latin1';
                $table->collation = 'latin1_swedish_ci';
                $table->integer('id_mesin_absensi')->length(10)->unsigned();
                $table->integer('id_user');
                $table->string('name', 40);
                $table->integer('group')->nullable(true);
                $table->integer('privilege')->nullable(true);
            });
        }

        $another_create_integer=['pin','pin2'];
        foreach($another_create_integer as $value){
            if (!Schema::hasColumn($table_name, $value)){
                Schema::table($table_name, function (Blueprint $table) use ($value){
                    $table->integer($value);
                });
            }
        }

        $table_name='ref_karyawan_user';
        if (!Schema::hasTable($table_name)) {
            Schema::create($table_name, function (Blueprint $table) use ($table_name) {
                $table->charset = 'latin1';
                $table->collation = 'latin1_swedish_ci';
                $table->integer('id_karyawan')->length(10)->unsigned();
                // $table->integer('id_mesin_absensi')->length(10)->unsigned();
                $table->integer('id_user');
                
                // $table->unique(['id_karyawan','id_mesin_absensi','id_user'],$table_name.'_uniq');
                $table->unique(['id_karyawan','id_user'],$table_name.'_uniq');

                $table->foreign(['id_karyawan'],$table_name.'_fk1')->references(['id_karyawan'])->on('ref_karyawan')->onUpdate('cascade')->onDelete('cascade');
                // $table->foreign(['id_mesin_absensi','id_user'],$table_name.'_fk2')->references(['id_mesin_absensi','id_user'])->on('ref_user_info')->onUpdate('cascade')->onDelete('cascade');
            });
        }

        Schema::table($table_name, function (Blueprint $table) use ($table_name) {
            $sm = Schema::getConnection()->getDoctrineSchemaManager();
            $indexesFound = $sm->listTableIndexes($table_name);

            if(!array_key_exists($table_name.'_uniq1', $indexesFound)){
                $table->unique(['id_karyawan'],$table_name.'_uniq1');
            }

            if(!array_key_exists($table_name.'_uniq2', $indexesFound)){
                $table->unique(['id_user'],$table_name.'_uniq2');
            }
            
            if(array_key_exists($table_name.'_uniq', $indexesFound)){
                $table->dropUnique($table_name.'_uniq');
            }
        });
        
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        $table_name='ref_user_info';
        if (Schema::hasTable($table_name)) {
            Schema::dropIfExists($table_name);
        }
        
        $table_name='ref_user_info_detail';
        if (Schema::hasTable($table_name)) {
            Schema::dropIfExists($table_name);
        }

        $table_name='user_mesin_tmp';
        if (Schema::hasTable($table_name)) {
            Schema::dropIfExists($table_name);
        }

        $table_name='ref_karyawan_user';
        if (Schema::hasTable($table_name)) {
            Schema::dropIfExists($table_name);
        }
    }
}
