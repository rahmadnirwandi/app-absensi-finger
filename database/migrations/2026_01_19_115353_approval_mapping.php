<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class ApprovalMapping extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('approval_mappings', function (Blueprint $table) {
            $table->id();
            $table->unsignedInteger('id_karyawan');
            $table->enum('jenis_pengajuan', ['izin', 'cuti', 'lembur']);
            $table->integer('level'); 

            $table->enum('approver_type', ['jabatan', 'role']);
            $table->string('approver_value'); 

            $table->enum('scope_type', ['global', 'ruangan'])->default('global');
            $table->unsignedInteger('scope_id')->nullable();

            $table->boolean('aktif')->default(true);
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
        Schema::dropIfExists('approval_mappings');
    }
}
