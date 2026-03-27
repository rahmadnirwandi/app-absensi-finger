<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class PengajuanIzin extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('pengajuan_izin', function (Blueprint $table) {
            $table->id();

            $table->unsignedInteger('id_karyawan');
            $table->text('keterangan')->nullable();

            $table->date('tgl_mulai');
            $table->date('tgl_selesai');

            $table->string('file_pendukung')->nullable();
            $table->string('jenis_pengajuan', 100)->nullable();
            $table->unsignedInteger('id_ruangan');
            $table->integer('current_level')->default(1);
            $table->enum('status', ['pending', 'approved', 'rejected'])->default('pending');

            $table->timestamps();

            $table->foreign('id_karyawan')
                ->references('id_karyawan')
                ->on('ref_karyawan')
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
        Schema::dropIfExists('pengajuan_izin');
    }
}
