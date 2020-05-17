<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class UpdateAbsensiRawTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('absensi_raw', function (Blueprint $table) {
            $table->dropColumn('pegawai_id');
            $table->unsignedBigInteger('absensi_raw_upload_id')->default(0)->comment('0 jika bukan dari upload file')->after('mesin_absensi_id');
            $table->renameColumn('is_manual','is_from_file');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('absensi_raw', function (Blueprint $table) {
            $table->dropColumn('absensi_raw_upload_id');
            $table->unsignedBigInteger('pegawai_id')->default(0)->comment('auto detek saat get data absen')->after('mesin_absensi_id');
            $table->renameColumn('is_from_file','is_manual');
        });
    }
}
