<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class UxuiCuti extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('uxui_cuti', function (Blueprint $table) {
            $table->id();

            $table->unsignedInteger('id_karyawan');
            
            $table->date('tgl_pengajuan');
            $table->date('tgl_mulai');
            $table->date('tgl_selesai');

            $table->integer('jumlah_hari');
            $table->integer('sisa_cuti');
            $table->unsignedInteger('id_ruangan');
            $table->unsignedBigInteger('id_jenis_cuti');
            $table->integer('current_level')->default(1);
            $table->enum('status', ['pending', 'approved', 'rejected'])->default('pending');
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
        Schema::dropIfExists('uxui_cuti');
    }
}
