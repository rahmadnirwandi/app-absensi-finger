<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class PengajuanCuti extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('pengajuan_cuti', function (Blueprint $table){
            $table->id();

            $table->date('tanggal_pengajuan');
            $table->unsignedInteger('id_karyawan');
            $table->date('tanggal_mulai');
            $table->date('tanggal_selesai')->nullable();
            $table->integer('jumlah_hari')->nullable();
            $table->unsignedBigInteger('id_jenis_cuti');
            $table->integer('sisa_cuti');
            $table->text('keterangan')->nullable();
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
        //
    }
}
