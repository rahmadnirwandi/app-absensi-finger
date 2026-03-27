<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

class CreateTableAbsensi extends \App\Classes\MyMigration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        $table_name='ref_data_absensi_tmp';
        $table=new Blueprint($table_name);
        $table->integer('id_mesin_absensi')->length(10)->unsigned();
        $table->integer('id_user')->length(10)->unsigned();
        $table->dateTime('waktu');
        $table->string('verified', 20);
        $table->string('status', 20);

        $table->unique(['id_user','waktu','verified','status'],$table_name.'_uniq');
        $this->set_table($table_name,$table);


        $table_name='data_absensi_karyawan_rekap';
        $table=new Blueprint($table_name);
        $table->integer('id_user')->length(10)->unsigned();
        $table->integer('id_karyawan')->length(10)->unsigned();
        $table->smallInteger('id_jabatan');
        $table->smallInteger('id_departemen');
        $table->smallInteger('id_ruangan');
        $table->smallInteger('id_status_karyawan');
        $table->date('tgl_presensi');
        $table->text('presensi_jadwal');
        $table->text('presensi_all');
        $table->text('presensi_detail');
        $table->integer('total_waktu_kerja_user_sec')->length(40);
        $table->string('status_kerja',255);
        $table->integer('id_jenis_jadwal')->length(10)->unsigned();
        $table->unique(['id_user','id_karyawan','tgl_presensi','id_jenis_jadwal'],$table_name.'_uniq');
        $this->set_table($table_name,$table);

        DB::statement("ALTER TABLE ".$table_name." MODIFY total_waktu_kerja_user_sec double NOT NULL");
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        $table_name='ref_data_absensi_tmp';
        if (Schema::hasTable($table_name)) {
            Schema::dropIfExists($table_name);
        }

        $table_name='data_absensi_karyawan_rekap';
        if (Schema::hasTable($table_name)) {
            Schema::dropIfExists($table_name);
        }
    }
}
