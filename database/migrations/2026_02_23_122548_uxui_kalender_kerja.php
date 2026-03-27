<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class UxuiKalenderKerja extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('uxui_kalender_kerja', function(Blueprint $table) {
            $table->id();

            $table->date('tanggal');
            $table->unsignedInteger('id_karyawan');
            $table->unsignedInteger('id_jenis_jadwal');
            $table->unsignedInteger('id_ruangan');

            $table->timestamps();

            $table->foreign('id_karyawan')
                    ->references('id_karyawan')
                    ->on('ref_karyawan')
                    ->onDelete('cascade')
                    ->onUpdate('cascade');
            
            $table->foreign('id_jenis_jadwal')
                    ->references('id_jenis_jadwal')
                    ->on('ref_jenis_jadwal')
                    ->onDelete('cascade')
                    ->onUpdate('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        //
    }
}
