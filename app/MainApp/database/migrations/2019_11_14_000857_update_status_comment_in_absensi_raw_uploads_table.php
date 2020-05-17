<?php

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class UpdateStatusCommentInAbsensiRawUploadsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('absensi_raw_uploads', function (Blueprint $table) {
            //
        });
        DB::statement("ALTER TABLE `absensi_raw_uploads` CHANGE `status` `status` TINYINT(4) NOT NULL DEFAULT '0' COMMENT 'status data rawnya sudah diinsert ke table absensi_raw atau belum, 0=new file, 1=masih proses insert, 2=sudah diinsert semua, 3=sedang proses kalkulasi, 4=sudah dikalkulasi';");
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('absensi_raw_uploads', function (Blueprint $table) {
            //
        });
    }
}
