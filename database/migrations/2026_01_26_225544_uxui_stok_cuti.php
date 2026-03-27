<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class UxuiStokCuti extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
       Schema::create('uxui_stok_cuti', function(Blueprint $table) {
            $table->id();

            $table->unsignedInteger('id_karyawan');
            $table->date('awal_cuti');
            $table->date('akhir_cuti');
            $table->integer('jumlah');
            $table->integer('pakai');
            $table->integer('sisa');
            $table->integer('tukar');
            $table->unsignedBigInteger('id_jenis_cuti');
            $table->timestamps();

            $table->foreign('id_karyawan')
                  ->references('id_karyawan')
                  ->on('ref_karyawan')
                  ->onDelete('cascade');

            $table->foreign('id_jenis_cuti')
                  ->references('id')
                  ->on('uxui_jenis_cuti')
                  ->onDelete('cascade');
       });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('uxui_stok_cuti');
    }
}
