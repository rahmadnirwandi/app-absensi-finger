<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddTglMasukToRefKaryawanTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up(): void
    {
        Schema::table('ref_karyawan', function (Blueprint $table) {
            $table->date('tgl_masuk')->nullable()->after('nm_karyawan');
        });
    }


    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down(): void
    {
        Schema::table('ref_karyawan', function (Blueprint $table) {
            $table->dropColumn('tgl_masuk');
        });
    }
}
