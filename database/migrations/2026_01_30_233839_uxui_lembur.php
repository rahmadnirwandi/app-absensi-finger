<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class UxuiLembur extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('uxui_lembur', function (Blueprint $table) {
            $table->id();

            $table->unsignedInteger('id_karyawan');
            $table->unsignedInteger('id_ruangan');
            $table->date('tgl_pengajuan');
            $table->date('tgl_lembur');
            $table->time('jam_mulai');
            $table->time('jam_selesai');
            $table->text('keterangan')->nullable();
            $table->text('file_dokumen')->nullable();
            $table->string('jenis_lembur', 100);
            $table->decimal('rupiah_per_jam')->nullable();
            $table->integer('total_jam')->default(0);
            $table->decimal('total_bayar')->nullable();
            $table->integer('deposit_jam')->default(0);
            $table->boolean('redeemed')->default(false);

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
        Schema::dropIfExists('uxui_lembur');
    }
}
