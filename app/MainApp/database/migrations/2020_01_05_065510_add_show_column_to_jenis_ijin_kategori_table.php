<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddShowColumnToJenisIjinKategoriTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('jenis_ijin_kategori', function (Blueprint $table) {
            $table->tinyInteger('tampil_rekap_kehadiran')->default(1)->comment('1: ditampilkan di laporan rekap kehadiran')->after('singkatan');
            $table->tinyInteger('tampil_kehadiran_harian')->default(1)->comment('1: ditampilkan di laporan kehadiran harian')->after('singkatan');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('jenis_ijin_kategori', function (Blueprint $table) {
            $table->dropColumn(['tampil_rekap_kehadiran','tampil_kehadiran_harian']);
        });
    }
}
