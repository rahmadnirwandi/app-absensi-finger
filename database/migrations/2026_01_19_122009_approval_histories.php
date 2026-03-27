<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class ApprovalHistories extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('approval_histories', function (Blueprint $table) {
            $table->id();

            $table->enum('jenis_pengajuan', ['izin', 'cuti', 'lembur']);
            $table->unsignedBigInteger('pengajuan_id');

            $table->integer('level');
            $table->unsignedInteger('approver_id');

            $table->enum('status', ['approved', 'rejected', 'pending']);
            $table->text('catatan')->nullable();
            $table->timestamp('approved_at')->nullable();

            $table->timestamps();

            $table->foreign('approver_id')
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
        Schema::dropIfExists('appro');
    }
}
